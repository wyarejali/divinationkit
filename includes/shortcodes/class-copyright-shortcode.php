<?php
namespace DiviNationKit\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [copyright] — outputs a copyright line.
 *
 *   [copyright]                                 → © 2026
 *   [copyright start="2018"]                    → © 2018–2026
 *   [copyright name="My Site"]                  → © 2026 My Site
 *   [copyright start="2018" name="My Site"]     → © 2018–2026 My Site
 *
 * If start ≥ current year, the range collapses to a single year.
 */
class Copyright_Shortcode {

	public function __construct() {
		add_shortcode( 'copyright', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'start' => '',
				'name'  => '',
			),
			is_array( $atts ) ? $atts : array(),
			'copyright'
		);

		$year_end   = (int) wp_date( 'Y' );
		$year_start = (int) preg_replace( '/[^0-9]/', '', (string) $atts['start'] );

		if ( $year_start > 0 && $year_start < $year_end ) {
			$years = $year_start . '–' . $year_end;
		} else {
			$years = (string) $year_end;
		}

		$name   = trim( (string) $atts['name'] );
		$output = '© ' . $years;
		if ( $name !== '' ) {
			$output .= ' ' . $name;
		}

		return esc_html( $output );
	}
}
