<?php
/**
 * Plugin Name:       DiviNationKit
 * Plugin URI:        https://www.divinationkit.com/
 * Description:       Admin control panel for DiviNationKit design tweaks, mobile menu, and content tools — built to extend the Divi theme.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Wyarej Ali
 * Author URI:        https://www.divinationkit.com/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       divinationkit
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DIVINATIONKIT_VERSION', '1.0.0' );
define( 'DIVINATIONKIT_FILE', __FILE__ );
define( 'DIVINATIONKIT_DIR', plugin_dir_path( __FILE__ ) );
define( 'DIVINATIONKIT_URL', plugin_dir_url( __FILE__ ) );
define( 'DIVINATIONKIT_BASENAME', plugin_basename( __FILE__ ) );

require_once DIVINATIONKIT_DIR . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( \DiviNationKit\Plugin::class, 'on_activate' ) );

\DiviNationKit\Plugin::instance()->boot();
