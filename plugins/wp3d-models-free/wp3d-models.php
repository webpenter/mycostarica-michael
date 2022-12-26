<?php
/*
 * Plugin Name: WP3D Models Free
 * Plugin URI: http://wp3dmodels.com
 * Description: Display, Organize & Share Matterport 3D Models on your WordPress-based website.
 * Version: 3.5.8
 * Author: Ross Peterson
 * Author URI: http://ross-peterson.com/
 * Requires at least: 3.9
 * Tested up to: 5.6.2
*/

// Check for required PHP version
if (version_compare(PHP_VERSION, '5.3', '<'))
{
    exit(sprintf('WP3D Models requires PHP 5.3 or higher. You’re still on %s.',PHP_VERSION));
}

if ( ! defined( 'ABSPATH' ) ) exit;

// the current version of the plugin
define( 'WP3D_MODELS_VERSION', '3.5.8' );

// The URL of the site with EDD installed
define( 'WP3D_MODELS_PLUGIN_URL', 'https://wp3dmodels.com' ); 

// the name of your product matching the download name in EDD exactly
define( 'WP3D_MODELS_PLUGIN_NAME', 'WP3D Models Plugin (Free)'); 
define( 'WP3D_MODELS_SINGLE_LICENSE_ID', 431 );
define( 'WP3D_MODELS_MULTI_LICENSE_ID', 34990 );
define( 'WP3D_MODELS_FREE_LICENSE_ID', 38489 );
define( 'WP3D_MODELS_DISCOUNT_CODE', 'FREETRIALCONVERSION' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/includes/lib/EDD_SL_Plugin_Updater.php' );
}

function wp3d_models_plugin_updater() {
	// retrieve our license key from the DB
	$license_key = trim( get_option( 'wp3d_license_key' ) );
	
	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( WP3D_MODELS_PLUGIN_URL, __FILE__, array( 
			'version' 	=> WP3D_MODELS_VERSION, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => WP3D_MODELS_PLUGIN_NAME, 	// name of this plugin
			'author' 	=> 'Ross Peterson',  // author of this plugin
			'url'       => home_url()
		)
	);

}
add_action( 'admin_init', 'wp3d_models_plugin_updater', 0 );

/**
 * Returns the main instance of WP3D_Models to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WP3D_Models
 */
function WP3D_Models() {
	
	$instance = WP3D_Models::instance( __FILE__, WP3D_MODELS_VERSION );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WP3D_Models_Settings::instance( $instance );
	}

	return $instance;
}

// Load the required plugins 
require_once dirname( __FILE__ ) . '/includes/lib/class-tgm-plugin-activation.php';
require_once( 'includes/wp3d-models-required-plugins.php' );

// Load plugin class files
require_once( 'includes/class-wp3d-models.php' );
require_once( 'includes/class-wp3d-models-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-wp3d-models-admin-api.php' );
require_once( 'includes/lib/class-wp3d-models-post-type.php' );
require_once( 'includes/lib/class-wp3d-models-taxonomy.php' );

// make the Model CPT & Tax
if (get_option('wp3d_single_slug')=='') { 
	$wp3d_single_slug = '3d-model';
} else {
	$wp3d_single_slug = sanitize_title(get_option('wp3d_single_slug'));
	update_option( 'wp3d_single_slug', $wp3d_single_slug );
}

// Generate the Post Types & Taxonomies
WP3D_Models()->register_post_type( 'model', __( 'Models', 'wp3d-models' ), __( 'Model', 'wp3d-models' ), $wp3d_single_slug);
WP3D_Models()->register_taxonomy( 'model-type', __( 'Model Types', 'wp3d-models' ), __( 'Model Type', 'wp3d-models' ), 'model' );
WP3D_Models()->register_taxonomy( 'model-client', __( 'Model Clients', 'wp3d-models' ), __( 'Model Client', 'wp3d-models' ), 'model' );
WP3D_Models()->register_post_type( 'wp3d_agent', __( 'Agents / Schedule<span class="dashicons 
dashicons-lock" style="margin-top:-3px"></span>', 'wp3d-models' ), __( 'Agent', 'wp3d-models' ), 'wp3d-agent');

// now, lets tweak the agents setup a bit (hides from public/search views)		
add_filter('wp3d_agent_register_args', function($args) {
    $args['public'] = false;
    $args['exclude_from_search'] = true;
    return $args;
});

// get the ACF Field groups 
require_once( 'includes/wp3d-models-field-groups.php' );

// shortcodes
require_once( 'includes/wp3d-models-shortcode.php' );

// single model template
require_once( 'includes/wp3d-models-single-template.php' );

WP3D_Models();
