<?php
/**
 * Resolves an AI-generated layout plan into real Gutenberg block markup,
 * sourced only from this theme's own registered patterns — never from
 * LLM-generated markup.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class PlanResolver {

	/**
	 * Resolve a single pattern with text-slot overrides applied.
	 *
	 * @param string $slug           Registered pattern slug, e.g. "shadcn/hero-section".
	 * @param array  $text_overrides Map of slot index => replacement text.
	 * @return string Serialized block markup.
	 * @throws UnknownPatternSlugException If $slug is not a registered pattern.
	 */
	public function resolve_pattern( $slug, array $text_overrides ) {
		$pattern = \WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );

		if ( ! $pattern ) {
			throw new UnknownPatternSlugException(
				sprintf( 'Unknown pattern slug: %s', $slug )
			);
		}

		$blocks = parse_blocks( $pattern['content'] );
		$index  = 0;

		$this->apply_overrides( $blocks, $text_overrides, $index );

		return serialize_blocks( $blocks );
	}

	/**
	 * Resolve an ordered plan of multiple patterns into one markup string.
	 *
	 * @param array $plan Ordered list of `{slug, textSlots}` entries.
	 * @return string Concatenated serialized block markup.
	 */
	public function resolve_plan( array $plan ) {
		$markup = array();

		foreach ( $plan as $entry ) {
			// array_key_exists(), not isset() — isset() returns false for an
			// explicit null value, which would silently treat a malformed
			// "textSlots": null entry as "no overrides" instead of the
			// resolver's own internal whitelist/shape guard catching it.
			$has_slots = array_key_exists( 'textSlots', $entry ) && is_array( $entry['textSlots'] );
			$overrides = $has_slots ? $entry['textSlots'] : array();
			$markup[]  = $this->resolve_pattern( $entry['slug'], $overrides );
		}

		return implode( "\n", $markup );
	}

	/**
	 * Walk the block tree in the same document order CatalogBuilder used to
	 * derive slot indices, applying any override found for that index.
	 * Override indices with no matching slot are silently ignored.
	 *
	 * @param array $blocks    Parsed block array, mutated in place.
	 * @param array $overrides Map of slot index => replacement text.
	 * @param int   $index     Running slot index, passed by reference.
	 */
	private function apply_overrides( array &$blocks, array $overrides, &$index ) {
		foreach ( $blocks as &$block ) {
			if ( null === $block['blockName'] ) {
				continue;
			}

			if ( TextSlotHtml::is_slot_block( $block['blockName'] ) ) {
				if ( array_key_exists( $index, $overrides ) ) {
					$new_html = TextSlotHtml::write(
						$block['blockName'],
						$block['innerHTML'],
						(string) $overrides[ $index ]
					);

					$block['innerHTML']       = $new_html;
					$block['innerContent'][0] = $new_html;
				}
				++$index;
				continue;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->apply_overrides( $block['innerBlocks'], $overrides, $index );
			}
		}
		unset( $block );
	}
}
