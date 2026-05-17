<?php
namespace DiviNationKit\Shortcodes;

use DiviNationKit\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Expandable_Shortcode {

	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
		add_shortcode( 'expandable', array( $this, 'render' ) );
	}

	public function render( $atts, $content = '' ) {
		if ( ! $this->settings->is_tool_enabled( 'expandable' ) ) {
			return do_shortcode( $content );
		}

		$tool = $this->settings->tool( 'expandable' );

		$atts = shortcode_atts(
			array(
				'words' => isset( $tool['word_limit'] ) ? (int) $tool['word_limit'] : 30,
				'more'  => $tool['more_text'] ?? 'Read More',
				'less'  => $tool['less_text'] ?? 'Read Less',
			),
			$atts,
			'expandable'
		);

		$limit   = max( 1, (int) $atts['words'] );
		$content = do_shortcode( wp_kses_post( $content ) );

		$plain_text = trim( wp_strip_all_tags( $content ) );
		$words      = preg_split( '/\s+/u', $plain_text, -1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) <= $limit ) {
			return $content;
		}

		static $i = 0;
		$id = 'expandable-' . ++$i;

		return sprintf(
			'<div id="%1$s" class="expandable-wrapper"><div class="exp-content">%4$s</div><a href="#" class="exp-toggle" data-more="%2$s" data-less="%3$s">%2$s</a></div>',
			esc_attr( $id ),
			esc_attr( $atts['more'] ),
			esc_attr( $atts['less'] ),
			$content
		);
	}
}
