<?php
namespace DiviNationKit\Features;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reading Progress Bar
 *
 * Renders a slim horizontal bar at the top (or bottom) of the viewport that
 * fills as the reader scrolls through the document. Auto-appears on the
 * selected singular post types whenever this feature is on.
 */
class Reading_Progress extends Feature {

	public function get_id(): string {
		return 'reading_progress';
	}

	public function get_label(): string {
		return __( 'Reading Progress Bar', 'divinationkit' );
	}

	public function get_description(): string {
		return __( 'Fixed slim bar that fills as the reader scrolls. Auto-applies to the post types you select below.', 'divinationkit' );
	}

	public function get_defaults(): array {
		return array(
			'enabled'     => 0,
			'bar_color'   => '#10b882',
			'track_color' => 'rgba(15,41,37,0.08)',
			'height'      => 4,
			'position'    => 'top',
			'post_types'  => array( 'post' ),
		);
	}

	public function get_fields(): array {
		return array(
			array(
				'id'    => 'bar_color',
				'type'  => 'color',
				'label' => __( 'Bar color', 'divinationkit' ),
			),
			array(
				'id'    => 'track_color',
				'type'  => 'color',
				'label' => __( 'Track color', 'divinationkit' ),
				'help'  => __( 'The empty rail behind the fill. A faint grey usually reads best.', 'divinationkit' ),
			),
			array(
				'id'    => 'height',
				'type'  => 'range',
				'label' => __( 'Height', 'divinationkit' ),
				'min'   => 1,
				'max'   => 12,
				'step'  => 1,
				'unit'  => 'px',
			),
			array(
				'id'      => 'position',
				'type'    => 'select',
				'label'   => __( 'Position', 'divinationkit' ),
				'options' => array(
					'top'    => __( 'Top of viewport', 'divinationkit' ),
					'bottom' => __( 'Bottom of viewport', 'divinationkit' ),
				),
			),
			array(
				'id'      => 'post_types',
				'type'    => 'multicheck',
				'label'   => __( 'Show on', 'divinationkit' ),
				'options' => $this->get_post_type_options(),
				'help'    => __( 'Only renders on singular views of the selected post types. Uncheck everything to disable without flipping the toggle.', 'divinationkit' ),
			),
		);
	}

	public function has_stylesheet(): bool {
		return true;
	}

	public function has_script(): bool {
		return true;
	}

	public function should_load_on_frontend( array $values ): bool {
		if ( is_admin() ) {
			return false;
		}
		$types = isset( $values['post_types'] ) && is_array( $values['post_types'] ) ? $values['post_types'] : array( 'post' );
		if ( empty( $types ) ) {
			return false;
		}
		return is_singular( $types );
	}

	public function inline_css( array $values ): string {
		$bar    = $values['bar_color'] ?? '#10b882';
		$track  = $values['track_color'] ?? 'rgba(15,41,37,0.08)';
		$height = max( 1, (int) ( $values['height'] ?? 4 ) );

		return sprintf(
			':root{--dnk-rp-bar:%s;--dnk-rp-track:%s;--dnk-rp-height:%dpx;}',
			esc_attr( $bar ),
			esc_attr( $track ),
			$height
		);
	}

	public function register(): void {
		add_action( 'wp_footer', array( $this, 'render_html' ), 5 );
	}

	public function render_html(): void {
		$values = \DiviNationKit\Plugin::instance()->settings->tool( $this->get_id() );
		if ( ! $this->should_load_on_frontend( $values ) ) {
			return;
		}
		$position = ( ( $values['position'] ?? 'top' ) === 'bottom' ) ? 'is-bottom' : 'is-top';
		printf(
			'<div class="dnk-progress-bar %s" aria-hidden="true"><span class="dnk-progress-bar-fill"></span></div>',
			esc_attr( $position )
		);
	}

	/**
	 * Build the post-types option map (label => name), excluding attachments.
	 */
	private function get_post_type_options(): array {
		$out   = array();
		$types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $types as $type ) {
			if ( $type->name === 'attachment' ) {
				continue;
			}
			$label = isset( $type->labels->name ) ? $type->labels->name : $type->name;
			$out[ $type->name ] = $label;
		}
		// Provide a sensible default if nothing is registered yet (early bootstrap).
		if ( empty( $out ) ) {
			$out = array(
				'post' => __( 'Posts', 'divinationkit' ),
				'page' => __( 'Pages', 'divinationkit' ),
			);
		}
		return $out;
	}
}
