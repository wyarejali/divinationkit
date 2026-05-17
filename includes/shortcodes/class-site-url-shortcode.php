<?php
namespace DiviNationKit\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [site_url] — the site's frontend home URL.
 *
 *   [site_url]                                  → https://example.com
 *   [site_url path="/contact"]                  → https://example.com/contact
 */
class Site_Url_Shortcode {

	public function __construct() {
		add_shortcode( 'site_url', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'path' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'site_url'
		);

		return esc_url( home_url( (string) $atts['path'] ) );
	}
}
