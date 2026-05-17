<?php
namespace DiviNationKit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	private $settings;
	private $features;

	public function __construct( Settings $settings, Features $features ) {
		$this->settings = $settings;
		$this->features = $features;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
		add_action( 'wp_head', array( $this, 'output_inline_css' ), 20 );
	}

	public function enqueue_frontend(): void {
		foreach ( $this->features->all() as $feature ) {
			$id = $feature->get_id();
			if ( ! $this->settings->is_tool_enabled( $id ) ) {
				continue;
			}
			if ( ! $feature->should_load_on_frontend( $this->settings->tool( $id ) ) ) {
				continue;
			}

			$basename = $feature->asset_basename();

			if ( $feature->has_stylesheet() ) {
				$path = DIVINATIONKIT_DIR . 'assets/css/' . $basename . '.css';
				if ( file_exists( $path ) ) {
					wp_enqueue_style(
						$feature->asset_handle(),
						DIVINATIONKIT_URL . 'assets/css/' . $basename . '.css',
						array(),
						DIVINATIONKIT_VERSION
					);
				}
			}

			if ( $feature->has_script() ) {
				$path = DIVINATIONKIT_DIR . 'assets/js/' . $basename . '.js';
				if ( file_exists( $path ) ) {
					wp_enqueue_script(
						$feature->asset_handle(),
						DIVINATIONKIT_URL . 'assets/js/' . $basename . '.js',
						$feature->script_deps(),
						DIVINATIONKIT_VERSION,
						true
					);
				}
			}
		}
	}

	public function output_inline_css(): void {
		$css   = $this->design_css();
		$tools = '';

		foreach ( $this->features->all() as $feature ) {
			$id = $feature->get_id();
			if ( ! $this->settings->is_tool_enabled( $id ) ) {
				continue;
			}
			$values = $this->settings->tool( $id );
			if ( ! $feature->should_load_on_frontend( $values ) ) {
				continue;
			}
			$tools .= $feature->inline_css( $values );
		}

		$payload = trim( $css . $tools );
		if ( $payload === '' ) {
			return;
		}

		echo "<style id=\"divinationkit-inline\">{$payload}</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function design_css(): string {
		$design = $this->settings->design();
		$height = (int) $design['menu_height'];
		$width  = max( 50, min( 100, (int) $design['container_width'] ) );

		return sprintf(
			':root{--dnk-menu-height:%dpx;--dnk-container:%d%%;}'
			. 'body{--menu-height:%dpx;--container:%d%%;}'
			. 'nav > ul > li > a{line-height:var(--dnk-menu-height) !important;}'
			. '@media screen and (max-width:500px){.et_pb_row,header .container{width:var(--dnk-container) !important;}}',
			$height,
			$width,
			$height,
			$width
		);
	}
}
