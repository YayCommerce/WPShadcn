<?php
/**
 * Text-slot HTML helper for the AI Layout Builder.
 *
 * Locates the single text node inside a leaf block's `innerHTML` fragment
 * (core/heading, core/paragraph, core/button) and reads/replaces its text
 * content only, leaving the element's tag and attributes untouched.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class TextSlotHtml {

	/**
	 * Block names treated as text-slot leaves.
	 */
	const SLOT_BLOCK_NAMES = array( 'core/heading', 'core/paragraph', 'core/button' );

	/**
	 * Tag whose text holds the actual link label inside a core/button fragment.
	 */
	const BUTTON_TEXT_TAG = 'a';

	/**
	 * Whether a block name is treated as a text-slot leaf.
	 */
	public static function is_slot_block( $block_name ) {
		return in_array( $block_name, self::SLOT_BLOCK_NAMES, true );
	}

	/**
	 * Read the slot's current text.
	 */
	public static function read( $block_name, $html_fragment ) {
		$context = self::locate( $block_name, $html_fragment );

		return $context ? $context['target']->textContent : '';
	}

	/**
	 * Replace the slot's text, preserving tag/attributes and surrounding whitespace.
	 */
	public static function write( $block_name, $html_fragment, $new_text ) {
		$context = self::locate( $block_name, $html_fragment );

		if ( ! $context ) {
			return $html_fragment;
		}

		$dom    = $context['dom'];
		$root   = $context['root'];
		$target = $context['target'];

		while ( $target->firstChild ) {
			$target->removeChild( $target->firstChild );
		}
		$target->appendChild( $dom->createTextNode( $new_text ) );

		preg_match( '/^\s*/', $html_fragment, $leading );
		preg_match( '/\s*$/', $html_fragment, $trailing );

		return $leading[0] . $dom->saveHTML( $root ) . $trailing[0];
	}

	/**
	 * Parse the fragment and locate the DOM element that holds the slot's text:
	 * the root element itself for heading/paragraph, the nested `<a>` for button.
	 *
	 * @return array{dom: \DOMDocument, root: \DOMNode, target: \DOMNode}|null
	 */
	private static function locate( $block_name, $html_fragment ) {
		$dom = new \DOMDocument();

		$previous_setting = libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8" ?><html><body>' . $html_fragment . '</body></html>' );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous_setting );

		$body = $dom->getElementsByTagName( 'body' )->item( 0 );
		$root = $body ? self::first_element_child( $body ) : null;

		if ( ! $root ) {
			return null;
		}

		if ( 'core/button' === $block_name ) {
			$nodes  = $dom->getElementsByTagName( self::BUTTON_TEXT_TAG );
			$target = $nodes->length ? $nodes->item( 0 ) : null;
		} else {
			$target = $root;
		}

		if ( ! $target ) {
			return null;
		}

		return array(
			'dom'    => $dom,
			'root'   => $root,
			'target' => $target,
		);
	}

	/**
	 * First child node that is an actual element (skips whitespace text nodes).
	 */
	private static function first_element_child( \DOMNode $node ) {
		foreach ( $node->childNodes as $child ) {
			if ( XML_ELEMENT_NODE === $child->nodeType ) {
				return $child;
			}
		}

		return null;
	}
}
