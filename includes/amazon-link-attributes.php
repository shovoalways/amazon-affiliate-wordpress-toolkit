<?php

/**
 * Add affiliate attributes to Amazon links.
 *
 * Adds:
 * - rel="sponsored nofollow noopener"
 * - target="_blank"
 *
 * Existing rel and target attributes are preserved.
 */

add_filter( 'the_content', 'ah_amazon_link_attrs', 20 );

function ah_amazon_link_attrs( $content ) {

	if ( is_admin() || empty( $content ) ) {
		return $content;
	}

	return preg_replace_callback(
		'#<a\s([^>]*href=["\'][^"\']*amazon\.[^"\']*["\'][^>]*)>#i',
		static function ( $matches ) {

			$attrs = $matches[1];

			if ( stripos( $attrs, 'rel=' ) === false ) {
				$attrs .= ' rel="sponsored nofollow noopener"';
			}

			if ( stripos( $attrs, 'target=' ) === false ) {
				$attrs .= ' target="_blank"';
			}

			return '<a ' . $attrs . '>';
		},
		$content
	);
}
