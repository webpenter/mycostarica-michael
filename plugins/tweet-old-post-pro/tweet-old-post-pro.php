<?php

/**
 * Main loader file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themeisle.com/
 * @package           ROP
 *
 * @wordpress-plugin
 * Plugin Name: Revive Old Posts Pro Add-on
 * Plugin URI: https://revive.social/plugins/revive-old-post
 * Description: This addon enables the paid functions of Revive Old Post Free plugin. Both the Free and Paid plugin need to be activated. For questions, comments, or feature requests, <a href="http://revive.social/support/">contact </a> us!
 * Author: revive.social
 * Version: 3.0.5
 * Author URI: http://revive.social
 * Requires at least: 3.5
 * Tested up to:      5.7
 * Stable tag:        trunk
 * WordPress Available:  no
 * Requires License:    yes
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tweet-old-post
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Bootstrap the plugin.
 */
function run_rop_pro() {

	define( 'ROP_PRO_VERSION', '3.0.5' );
	define( 'ROP_PRO_DIR_PATH', plugin_dir_path( __FILE__ ) );
	define( 'ROP_PRO_DIR_URL', plugin_dir_url( __FILE__ ) );

	/*
		$plugin = new ROP();
		$plugin->run();
	*/

	$vendor_file = ROP_PRO_DIR_PATH . '/vendor/autoload.php';
	if ( is_readable( $vendor_file ) ) {
		require_once $vendor_file;
	}
	add_filter(
		'themeisle_sdk_products',
		function ( $products ) {
			$products[] = __FILE__;

			return $products;
		}
	);

	$plugin = new Rop_Pro();
	$plugin->run();
}

require( 'class-rop-autoloader.php' );
Rop_Pro_Autoloader::define_namespaces( array( 'Rop_Pro' ) );

/**
 * Invocation of the Autoloader::loader method.
 *
 * @since   2.0.0
 */
spl_autoload_register( array( 'Rop_Pro_Autoloader', 'loader' ) );

run_rop_pro();
