<?php
namespace DiviNationKit\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [user_first_name] — current logged-in user's first name.
 *
 *   [user_first_name]                           → "Wyarej"  (or empty)
 *   [user_first_name fallback="there"]          → "Wyarej" or "there" if logged out / no first name
 */
class User_First_Name_Shortcode {

	public function __construct() {
		add_shortcode( 'user_first_name', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'fallback' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'user_first_name'
		);

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$name = trim( (string) $user->first_name );
			if ( $name !== '' ) {
				return esc_html( $name );
			}
		}

		return esc_html( (string) $atts['fallback'] );
	}
}
