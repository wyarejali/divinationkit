<?php
namespace DiviNationKit\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [reading_time] — estimated reading time for the current post.
 *
 *   [reading_time]                              → "3 min read"
 *   [reading_time wpm="180" suffix="minute read"]
 *   [reading_time words="450"]                  → calculates from given count
 *
 * If used outside the loop and no `words` attr is provided, returns empty.
 */
class Reading_Time_Shortcode {

	public function __construct() {
		add_shortcode( 'reading_time', array( $this, 'render' ) );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'wpm'    => 200,
				'suffix' => __( 'min read', 'divinationkit' ),
				'words'  => '',
			),
			is_array( $atts ) ? $atts : array(),
			'reading_time'
		);

		$word_count = absint( $atts['words'] );
		if ( ! $word_count ) {
			$post = get_post();
			if ( ! $post || empty( $post->post_content ) ) {
				return '';
			}
			$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
		}

		if ( $word_count < 1 ) {
			return '';
		}

		$wpm     = max( 50, absint( $atts['wpm'] ) );
		$minutes = max( 1, (int) ceil( $word_count / $wpm ) );
		$suffix  = trim( (string) $atts['suffix'] );

		return esc_html( $minutes . ( $suffix !== '' ? ' ' . $suffix : '' ) );
	}
}
