<?php

/**
 * Plugin Name: WP Auto Republish Premium
 * Plugin URI: https://wpautorepublish.com
 * Description: The WP Auto Republish plugin helps revive old posts by resetting the publish date to the current date. This will push old posts to your front page, the top of archive pages, and back into RSS feeds. Ideal for sites with a large repository of evergreen content.
 * Version: 1.2.5.1
 * Author: Sayan Datta
 * Author URI: https://www.sayandatta.in
 * License: GPLv3
 * Text Domain: wp-auto-republish
 * Domain Path: /languages
 * 
 * WP Auto Republish is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * WP Auto Republish is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Auto Republish. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Core
 * @package  WP Auto Republish
 * @author   Sayan Datta <hello@sayandatta.in>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://wordpress.org/plugins/wp-auto-republish/
 * 
 * @fs_ignore /vendor/
 * 
 * @fs_premium_only /includes/Core/Premium/, /includes/Helpers/Premium/, /assets/js/post.min.js, /assets/js/extras.min.js, /assets/js/social.min.js, /assets/css/post.min.css, /wpml-config.xml, /vendor/abraham/, /vendor/eher/, /vendor/facebook/, /vendor/guzzlehttp/, /vendor/psr/, /vendor/ralouphie/, /vendor/symfony/, /vendor/tumblr/, /vendor/zoonman/
 */
// If this file is called firectly, abort!!!
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// include freemius sdk

if ( function_exists( 'wpar_load_fs_sdk' ) ) {
    wpar_load_fs_sdk()->set_basename( true, __FILE__ );
} else {
    
    if ( !function_exists( 'wpar_load_fs_sdk' ) ) {
        // Create a helper function for easy SDK access.
        function wpar_load_fs_sdk()
        {
            global  $wpar_load_fs_sdk ;
            
            if ( !isset( $wpar_load_fs_sdk ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
                $wpar_load_fs_sdk = fs_dynamic_init( [
                    'id'             => '5789',
                    'slug'           => 'wp-auto-republish',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_94e7891c5190ae1f9af5110b0f6eb',
                    'is_premium'     => true,
                    'premium_suffix' => 'Premium',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'anonymous_mode' => true,
                    'trial'          => [
                    'days'               => 7,
                    'is_require_payment' => false,
                ],
                    'menu'           => [
                    'slug'    => 'wp-auto-republish',
                    'support' => false,
                ],
                    'is_live'        => true,
                ] );
            }
            
            return $wpar_load_fs_sdk;
        }
        
        // Init Freemius.
        wpar_load_fs_sdk();
        // Signal that SDK was initiated.
        do_action( 'wpar_load_fs_sdk_loaded' );
    }

}

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}
/**
 * The code that runs during plugin activation
 */
function wpar_plugin_activation()
{
    Wpar\Base\Activate::activate();
}

register_activation_hook( __FILE__, 'wpar_plugin_activation' );
/**
 * The code that runs during plugin deactivation
 */
function wpar_plugin_deactivation()
{
    Wpar\Base\Deactivate::deactivate();
}

register_deactivation_hook( __FILE__, 'wpar_plugin_deactivation' );
/**
 * The code that runs during plugin uninstalltion
 */
function wpar_plugin_uninstallation()
{
    Wpar\Base\Uninstall::uninstall();
}

wpar_load_fs_sdk()->add_action( 'after_uninstall', 'wpar_plugin_uninstallation' );
/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Wpar\\WPARLoader' ) ) {
    Wpar\WPARLoader::register_services();
}