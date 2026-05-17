<?php
namespace DiviNationKit\Features;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Expandable extends Feature {

	public function get_id(): string {
		return 'expandable';
	}

	public function get_label(): string {
		return __( 'Expandable Text Block', 'divinationkit' );
	}

	public function get_description(): string {
		return __( 'Adds the [expandable] shortcode for collapsible content with a Read More / Read Less toggle.', 'divinationkit' );
	}

	public function get_defaults(): array {
		return array(
			'enabled'          => 1,
			'word_limit'       => 30,
			'collapsed_height' => 80,
			'button_color'     => '#1959ff',
			'text_color'       => '#333333',
			'more_text'        => 'Read More',
			'less_text'        => 'Read Less',
		);
	}

	public function get_fields(): array {
		return array(
			array(
				'id'    => 'word_limit',
				'type'  => 'number',
				'label' => __( 'Word limit', 'divinationkit' ),
				'min'   => 1,
				'max'   => 500,
				'step'  => 1,
				'help'  => __( 'Default word count before the content is truncated. The [expandable words=""] attribute overrides this per-shortcode.', 'divinationkit' ),
			),
			array(
				'id'    => 'collapsed_height',
				'type'  => 'range',
				'label' => __( 'Collapsed height', 'divinationkit' ),
				'min'   => 40,
				'max'   => 400,
				'step'  => 4,
				'unit'  => 'px',
			),
			array(
				'id'    => 'button_color',
				'type'  => 'color',
				'label' => __( 'Toggle link color', 'divinationkit' ),
			),
			array(
				'id'    => 'text_color',
				'type'  => 'color',
				'label' => __( 'Body text color', 'divinationkit' ),
			),
			array(
				'id'    => 'more_text',
				'type'  => 'text',
				'label' => __( 'Expand button text', 'divinationkit' ),
			),
			array(
				'id'    => 'less_text',
				'type'  => 'text',
				'label' => __( 'Collapse button text', 'divinationkit' ),
			),
		);
	}

	public function has_stylesheet(): bool {
		return true;
	}

	public function has_script(): bool {
		return true;
	}

	public function inline_css( array $values ): string {
		$btn    = $values['button_color'] ?? '#1959ff';
		$text   = $values['text_color'] ?? '#333333';
		$height = (int) ( $values['collapsed_height'] ?? 80 );

		return sprintf(
			':root{--dnk-exp-button:%s;--dnk-exp-text:%s;--dnk-exp-collapsed:%dpx;}',
			esc_attr( $btn ),
			esc_attr( $text ),
			$height
		);
	}
}
