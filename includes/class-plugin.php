<?php
namespace DiviNationKit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once DIVINATIONKIT_DIR . 'includes/class-color.php';
require_once DIVINATIONKIT_DIR . 'includes/class-settings.php';
require_once DIVINATIONKIT_DIR . 'includes/class-features.php';
require_once DIVINATIONKIT_DIR . 'includes/features/class-feature.php';
require_once DIVINATIONKIT_DIR . 'includes/features/class-feature-mobile-menu.php';
require_once DIVINATIONKIT_DIR . 'includes/features/class-feature-expandable.php';
require_once DIVINATIONKIT_DIR . 'includes/class-assets.php';
require_once DIVINATIONKIT_DIR . 'includes/class-admin.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-expandable-shortcode.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-current-year-shortcode.php';

final class Plugin {

	private static $instance = null;

	/** @var Settings */
	public $settings;

	/** @var Features */
	public $features;

	/** @var Assets */
	public $assets;

	/** @var Admin */
	public $admin;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public static function on_activate(): void {
		require_once DIVINATIONKIT_DIR . 'includes/class-settings.php';
		require_once DIVINATIONKIT_DIR . 'includes/features/class-feature.php';
		require_once DIVINATIONKIT_DIR . 'includes/features/class-feature-mobile-menu.php';
		require_once DIVINATIONKIT_DIR . 'includes/features/class-feature-expandable.php';

		$settings = new Settings();
		foreach ( array( new Features\Mobile_Menu(), new Features\Expandable() ) as $feature ) {
			$settings->seed_defaults_for_feature( $feature->get_id(), $feature->get_defaults() );
		}
	}

	public function boot(): void {
		$this->settings = new Settings();
		$this->features = new Features();
		$this->features->register( new Features\Mobile_Menu() );
		$this->features->register( new Features\Expandable() );

		$this->assets = new Assets( $this->settings, $this->features );
		$this->admin  = new Admin( $this->settings, $this->features );

		new Shortcodes\Expandable_Shortcode( $this->settings );
		new Shortcodes\Current_Year_Shortcode();

		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'divinationkit', false, dirname( DIVINATIONKIT_BASENAME ) . '/languages' );
	}
}
