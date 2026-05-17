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
require_once DIVINATIONKIT_DIR . 'includes/features/class-feature-reading-progress.php';
require_once DIVINATIONKIT_DIR . 'includes/class-assets.php';
require_once DIVINATIONKIT_DIR . 'includes/class-admin.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-expandable-shortcode.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-current-year-shortcode.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-copyright-shortcode.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-reading-time-shortcode.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-site-url-shortcode.php';
require_once DIVINATIONKIT_DIR . 'includes/shortcodes/class-user-first-name-shortcode.php';

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
		require_once DIVINATIONKIT_DIR . 'includes/features/class-feature-reading-progress.php';

		$settings = new Settings();
		$bootstrap_features = array(
			new Features\Mobile_Menu(),
			new Features\Expandable(),
			new Features\Reading_Progress(),
		);
		foreach ( $bootstrap_features as $feature ) {
			$settings->seed_defaults_for_feature( $feature->get_id(), $feature->get_defaults() );
		}
	}

	public function boot(): void {
		$this->settings = new Settings();
		$this->features = new Features();
		$this->features->register( new Features\Mobile_Menu() );
		$this->features->register( new Features\Expandable() );
		$this->features->register( new Features\Reading_Progress() );

		$this->assets = new Assets( $this->settings, $this->features );
		$this->admin  = new Admin( $this->settings, $this->features );

		// Let each enabled feature hook into WP (shortcodes, admin columns, etc.).
		foreach ( $this->features->all() as $feature ) {
			if ( $this->settings->is_tool_enabled( $feature->get_id() ) ) {
				$feature->register();
			}
		}

		new Shortcodes\Expandable_Shortcode( $this->settings );
		new Shortcodes\Current_Year_Shortcode();
		new Shortcodes\Copyright_Shortcode();
		new Shortcodes\Reading_Time_Shortcode();
		new Shortcodes\Site_Url_Shortcode();
		new Shortcodes\User_First_Name_Shortcode();

		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'divinationkit', false, dirname( DIVINATIONKIT_BASENAME ) . '/languages' );
	}
}
