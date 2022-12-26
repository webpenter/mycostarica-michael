<?php
/**
 * Plugin Name: Homey Woo Addon
 * Plugin URI:  https://wordpress.org/plugins/homey-woo-commerce-addon/
 * Description: Add woocommerce functionality to homey theme
 * Version:     1.0.0
 * Author:      Favethemes
 * Author URI:  http://themeforest.net/user/favethemes
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: homey-woocommerce-addon
 * Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Homey_WooCommerce' ) ) :

    final class Homey_WooCommerce {

        /**
         * Plugin's current version
         *
         * @var string
         */
        public $version;

        /**
         * Minimum Homey Version
         *
         * @since 1.0.0
         *
         * @var string Minimum Homey version required to run the plugin.
         */
        const MINIMUM_HOMEY_VERSION = '1.6.0';

        /**
         * Plugin Name
         *
         * @var string
         */
        public $plugin_name;

        /**
         * Plugin's instance.
         *
         * @var Houzez_WOO
         */
        protected static $_instance;

        /**
         * Constructor function.
         */
        public function __construct() {

            $this->plugin_name = 'homey-woocommerce';
            $this->version     = '1.0.0';

            // Check if Homey Core installed and activated
            if ( ! did_action( 'homey_core' ) ) {
                add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
                return;
            }

            // Check for required Elementor version
            if ( ! version_compare( HOMEY_VERSION, self::MINIMUM_HOMEY_VERSION, '>=' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_homey_version' ] );
                return;
            }

            $this->define_constants();
            $this->include_files();
            $this->init_hooks();

            do_action( 'homey_woocommerce_loaded' );

        }

        /**
         * Provides instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Initialize hooks.
         */
        public function init_hooks() {
            add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
            register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
            register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivate' ) );
        }

        /**
         * Defines constants.
         */
        protected function define_constants() {

            if ( ! defined( 'HOMEY_WOO_VERSION' ) ) {
                define( 'HOMEY_WOO_VERSION', $this->version );
            }

            if ( ! defined( 'HOMEY_WOO_PLUGIN_FILE' ) ) {
                define( 'HOMEY_WOO_PLUGIN_FILE', __FILE__ );
            }

            if ( ! defined( 'HOMEY_WOO_DIR' ) ) {
                define( 'HOMEY_WOO_DIR', plugin_dir_path( __FILE__ ) );
            }

            if ( ! defined( 'HOMEY_WOO_URL' ) ) {
                define( 'HOMEY_WOO_URL', plugin_dir_url( __FILE__ ) );
            }

            if ( ! defined( 'HOMEY_WOO_BASENAME' ) ) {
                define( 'HOMEY_WOO_BASENAME', plugin_basename( __FILE__ ) );
            }

        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have Homey Core installed or activated.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_missing_main_plugin() {

            if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

            $message = sprintf(
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'homey-woocommerce-addon' ),
                '<strong>' . esc_html__( 'Homey WooCommerce Addon', 'homey-woocommerce-addon' ) . '</strong>',
                '<strong>' . esc_html__( 'Homey Core', 'homey-woocommerce-addon' ) . '</strong>'
            );

            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have WooCommerce installed or activated.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_missing_woocommerce_plugin() {

            if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

            $message = sprintf(
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'homey-woocommerce-addon' ),
                '<strong>' . esc_html__( 'Homey WooCommerce Addon', 'homey-woocommerce-addon' ) . '</strong>',
                '<strong>' . esc_html__( 'WooCommerce', 'homey-woocommerce-addon' ) . '</strong>'
            );

            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required Houzez version.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_minimum_homey_version() {

            if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'homey-woocommerce-addon' ),
                '<strong>' . esc_html__( 'Homey WooCommerce Addon', 'homey-woocommerce-addon' ) . '</strong>',
                '<strong>' . esc_html__( 'Homey', 'homey-woocommerce-addon' ) . '</strong>',
                 self::MINIMUM_HOMEY_VERSION
            );

            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

        }
    
    

        /**
         * Functions
         */
        public function include_files() {

            include_once( HOMEY_WOO_DIR . 'includes/payment.php' ); 
        }


        /**
         * Load text domain for translation.
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'homey-woocommerce-addon', false, dirname( HOMEY_WOO_BASENAME ) . '/languages' );
        }

        /**
         * plugin activation
         */
        public function plugin_activation() {
            
        }


        /**
         * plugin de-activation
         */
        public function plugin_deactivate() {

        }

        /**
         * Unserializing is forbidden.
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Not good; huh?', 'homey-woocommerce-addon' ), HOMEY_WOO_VERSION );
        }


        /**
         * Cloning is forbidden.
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Not good; huh?', 'homey-woocommerce-addon' ), HOMEY_WOO_VERSION );
        }

    }

endif; // End class_exists check.


/**
 * Instance of Houzez_Woocommerce.
 * @return Houzez_Woocommerce
 */
function Homey_WooLoader() {
    return Homey_WooCommerce::instance();
}
Homey_WooLoader();