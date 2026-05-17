<?php
namespace DiviNationKit\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [current_year] — outputs the current 4-digit year in the site's timezone.
 *
 * Always registered (no toggle); it is harmless when unused.
 */
class Current_Year_Shortcode {

	public function __construct() {
		add_shortcode( 'current_year', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		return esc_html( wp_date( 'Y' ) );
	}
}
