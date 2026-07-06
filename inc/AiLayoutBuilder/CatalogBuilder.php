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
	 * Build the catalog: slug/title/description/categories/textSlots for every
	 * `shadcn/*` registered pattern, including patterns with zero text slots.
	 *
	 * @return array[]
	 */
	public function build() {
		$catalog  = array();
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

			$catalog[] = array(
				'slug'        => $slug,
				'title'       => isset( $pattern['title'] ) ? $pattern['title'] : '',
				'description' => isset( $pattern['description'] ) ? $pattern['description'] : '',
				'categories'  => isset( $pattern['categories'] ) ? $pattern['categories'] : array(),
				'textSlots'   => $this->extract_text_slots( isset( $pattern['content'] ) ? $pattern['content'] : '' ),
			);
		}

		return $catalog;
	}

	/**
	 * Walk a pattern's parsed block tree and collect its text slots in
	 * document order. Zero-slot patterns return an empty array.
	 *
	 * @return array{index: int, blockName: string, defaultText: string}[]
	 */
	private function extract_text_slots( $content ) {
		$slots = array();
		$index = 0;

		$this->walk_for_slots( parse_blocks( $content ), $slots, $index );

		return $slots;
	}

	/**
	 * @param array $blocks Parsed block array (mutated only via the $index/$slots refs).
	 * @param array $slots  Accumulator, passed by reference.
	 * @param int   $index  Running slot index across the whole tree, passed by reference.
	 */
	private function walk_for_slots( array $blocks, array &$slots, &$index ) {
		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] ) {
				continue; // Whitespace-only fragment between sibling blocks.
			}

			if ( TextSlotHtml::is_slot_block( $block['blockName'] ) ) {
				$slots[] = array(
					'index'       => $index,
					'blockName'   => $block['blockName'],
					'defaultText' => TextSlotHtml::read( $block['blockName'], $block['innerHTML'] ),
				);
				++$index;
				continue;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->walk_for_slots( $block['innerBlocks'], $slots, $index );
			}
		}
	}
}
