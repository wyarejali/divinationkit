<?php
namespace DiviNationKit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Color {

	/**
	 * Parse a hex (#rgb, #rrggbb, #rrggbbaa) or rgb()/rgba() string into [r,g,b,a].
	 * Falls back to opaque black on bad input.
	 */
	public static function to_rgba( string $color ): array {
		$color = trim( $color );

		if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([\d.]+)\s*)?\)$/i', $color, $m ) ) {
			return array(
				max( 0, min( 255, (int) $m[1] ) ),
				max( 0, min( 255, (int) $m[2] ) ),
				max( 0, min( 255, (int) $m[3] ) ),
				isset( $m[4] ) ? max( 0.0, min( 1.0, (float) $m[4] ) ) : 1.0,
			);
		}

		if ( preg_match( '/^#?([A-Fa-f0-9]{6})([A-Fa-f0-9]{2})?$/', $color, $m ) ) {
			return array(
				hexdec( substr( $m[1], 0, 2 ) ),
				hexdec( substr( $m[1], 2, 2 ) ),
				hexdec( substr( $m[1], 4, 2 ) ),
				isset( $m[2] ) ? hexdec( $m[2] ) / 255 : 1.0,
			);
		}

		if ( preg_match( '/^#?([A-Fa-f0-9])([A-Fa-f0-9])([A-Fa-f0-9])$/', $color, $m ) ) {
			return array(
				hexdec( $m[1] . $m[1] ),
				hexdec( $m[2] . $m[2] ),
				hexdec( $m[3] . $m[3] ),
				1.0,
			);
		}

		return array( 0, 0, 0, 1.0 );
	}

	/**
	 * Multiply the alpha of a color by $alpha and return a rgba() string.
	 */
	public static function tint( string $color, float $alpha ): string {
		list( $r, $g, $b, $a ) = self::to_rgba( $color );
		$final = max( 0.0, min( 1.0, $a * $alpha ) );
		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, self::format_alpha( $final ) );
	}

	private static function format_alpha( float $value ): string {
		$out = number_format( $value, 3, '.', '' );
		$out = rtrim( $out, '0' );
		return rtrim( $out, '.' );
	}
}
