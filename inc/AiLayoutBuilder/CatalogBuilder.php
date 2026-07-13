<?php
/**
 * Builds the pattern catalog sent to the AI Layout Builder's LLM endpoint.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class CatalogBuilder {

	/**
	 * Prefix identifying this theme's own patterns among all registered patterns.
	 */
	const SLUG_PREFIX = 'shadcn/';

	/**
	 * Block names counted as an image for the structure summary.
	 */
	const IMAGE_BLOCK_NAMES = array( 'core/image', 'core/cover', 'core/media-text' );

	/**
	 * Non-image block names that still mark a pattern as media-bearing.
	 */
	const MEDIA_BLOCK_NAMES = array( 'core/video', 'core/embed' );

	/**
	 * Build the catalog: slug/title/description/categories/textSlots/structure
	 * for every `shadcn/*` registered pattern, including patterns with zero
	 * text slots.
	 *
	 * @return array[]
	 */
	public function build() {
		$catalog  = array();
		$hints    = ( new PatternHintReader() )->hints();
		$patterns = \WP_Block_Patterns_Registry::get_instance()->get_all_registered();

		// get_all_registered() returns array_values() of its internal
		// registry — a plain indexed array (0, 1, 2...), NOT keyed by
		// pattern slug. The slug lives in each element's own 'name' field.
		// (A stub used during standalone testing returned an associative
		// array keyed by slug, which masked this — every real pattern was
		// silently skipped in production since the array's numeric index
		// never matched the 'shadcn/' prefix check.)
		foreach ( $patterns as $pattern ) {
			$slug = isset( $pattern['name'] ) ? $pattern['name'] : '';

			if ( 0 !== strpos( $slug, self::SLUG_PREFIX ) ) {
				continue;
			}

			// Parse once; both the slot extractor and the structure
			// collector walk the same tree.
			$blocks = parse_blocks( isset( $pattern['content'] ) ? $pattern['content'] : '' );

			$slots = array();
			$index = 0;
			$stats = array(
				'headings'   => array(), // level => count.
				'paragraphs' => 0,
				'buttons'    => 0,
				'images'     => 0,
				'columns'    => 0,
				'hasMedia'   => false,
			);

			$this->walk( $blocks, $slots, $index, $stats );

			$catalog[] = array(
				'slug'        => $slug,
				'title'       => isset( $pattern['title'] ) ? $pattern['title'] : '',
				'description' => isset( $pattern['description'] ) ? $pattern['description'] : '',
				'categories'  => isset( $pattern['categories'] ) ? $pattern['categories'] : array(),
				'aiHint'      => isset( $hints[ $slug ] ) ? $hints[ $slug ] : '',
				'textSlots'   => $slots,
				'structure'   => $this->format_structure( $stats, $blocks ),
			);
		}

		return $catalog;
	}

	/**
	 * Single walk over a pattern's parsed block tree: collects text slots in
	 * document order (same semantics as before) and structural counts.
	 *
	 * @param array $blocks Parsed block array.
	 * @param array $slots  Slot accumulator, passed by reference.
	 * @param int   $index  Running slot index across the whole tree, passed by reference.
	 * @param array $stats  Structure counters, passed by reference.
	 */
	private function walk( array $blocks, array &$slots, &$index, array &$stats ) {
		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] ) {
				continue; // Whitespace-only fragment between sibling blocks.
			}

			$this->count_block( $block, $stats );

			if ( TextSlotHtml::is_slot_block( $block['blockName'] ) ) {
				$slots[] = array(
					'index'       => $index,
					'blockName'   => $block['blockName'],
					'defaultText' => TextSlotHtml::read( $block['blockName'], $block['innerHTML'] ),
				);
				++$index;
				continue;
			}

			// core/cover and core/media-text carry text slots inside their
			// innerBlocks — always keep recursing.
			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->walk( $block['innerBlocks'], $slots, $index, $stats );
			}
		}
	}

	/**
	 * Update structure counters for one block.
	 *
	 * @param array $block Parsed block.
	 * @param array $stats Structure counters, passed by reference.
	 */
	private function count_block( array $block, array &$stats ) {
		$name = $block['blockName'];

		if ( 'core/heading' === $name ) {
			$level                        = isset( $block['attrs']['level'] ) ? (int) $block['attrs']['level'] : 2;
			$stats['headings'][ $level ]  = isset( $stats['headings'][ $level ] ) ? $stats['headings'][ $level ] + 1 : 1;
			return;
		}

		if ( 'core/paragraph' === $name ) {
			++$stats['paragraphs'];
			return;
		}

		if ( 'core/button' === $name ) {
			++$stats['buttons'];
			return;
		}

		if ( in_array( $name, self::IMAGE_BLOCK_NAMES, true ) || false !== strpos( $name, 'svg-image' ) ) {
			++$stats['images'];
			$stats['hasMedia'] = true;
			return;
		}

		if ( in_array( $name, self::MEDIA_BLOCK_NAMES, true ) ) {
			$stats['hasMedia'] = true;
			return;
		}

		// Record the column count of the first columns row only — enough to
		// convey the pattern's dominant grid without over-describing it.
		if ( 'core/columns' === $name && 0 === $stats['columns'] && ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $child ) {
				if ( 'core/column' === $child['blockName'] ) {
					++$stats['columns'];
				}
			}
		}
	}

	/**
	 * Shape the raw counters into the catalog's `structure` object.
	 *
	 * @param array $stats  Filled counters.
	 * @param array $blocks Top-level parsed blocks (for root alignment).
	 * @return array
	 */
	private function format_structure( array $stats, array $blocks ) {
		ksort( $stats['headings'] );

		$headings = array();
		foreach ( $stats['headings'] as $level => $count ) {
			$headings[] = array(
				'level' => $level,
				'count' => $count,
			);
		}

		return array(
			'headings'   => $headings,
			'paragraphs' => $stats['paragraphs'],
			'buttons'    => $stats['buttons'],
			'images'     => $stats['images'],
			'columns'    => $stats['columns'],
			'hasMedia'   => $stats['hasMedia'],
			'fullWidth'  => $this->is_full_width( $blocks ),
		);
	}

	/**
	 * Whether the pattern's first real block is full-aligned.
	 *
	 * @param array $blocks Top-level parsed blocks.
	 * @return bool
	 */
	private function is_full_width( array $blocks ) {
		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] ) {
				continue;
			}

			return isset( $block['attrs']['align'] ) && 'full' === $block['attrs']['align'];
		}

		return false;
	}
}
