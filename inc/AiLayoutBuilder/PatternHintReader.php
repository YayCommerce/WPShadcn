<?php
/**
 * Reads curated `AI Hint:` headers from this theme's pattern files.
 *
 * WordPress core parses only its known pattern headers (Title, Slug,
 * Description, Categories, …) into WP_Block_Patterns_Registry — custom
 * headers never reach the registry. So the hint used to enrich the AI
 * Layout Builder catalog is read straight from the pattern files here.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class PatternHintReader {

	/**
	 * Per-request cache of the slug => hint map.
	 *
	 * @var array<string,string>|null
	 */
	private static $hints = null;

	/**
	 * Map of pattern slug (e.g. "shadcn/hero-section") to its AI hint.
	 * Patterns without an `AI Hint:` header are simply absent.
	 *
	 * @return array<string,string>
	 */
	public function hints() {
		if ( null !== self::$hints ) {
			return self::$hints;
		}

		self::$hints = array();

		$files = glob( get_theme_file_path( 'patterns' ) . '/*.php' );

		foreach ( $files ? $files : array() as $file ) {
			// get_file_data() reads the first 8KB — pattern headers sit at
			// the very top of each file, so that is always enough.
			$data = get_file_data(
				$file,
				array(
					'slug'   => 'Slug',
					'aiHint' => 'AI Hint',
				)
			);

			if ( '' !== $data['slug'] && '' !== $data['aiHint'] ) {
				self::$hints[ $data['slug'] ] = $data['aiHint'];
			}
		}

		/**
		 * Lets plugins that ship their own `shadcn/*` pattern files (e.g.
		 * Shadcn Blocks) contribute hints for the AI Layout Builder catalog.
		 * Runs before the per-request cache is returned, so contributed
		 * hints are cached alongside the theme's own.
		 *
		 * @param array<string,string> $hints Pattern slug => AI hint.
		 */
		self::$hints = apply_filters( 'shadcn_ai_pattern_hints', self::$hints );

		return self::$hints;
	}
}
