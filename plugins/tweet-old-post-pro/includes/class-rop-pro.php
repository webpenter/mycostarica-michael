<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link       https://themeisle.com/
 * @since      2.0.0
 *
 * @package    Rop_Pro
 * @subpackage Rop_Pro/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Rop
 * @subpackage Rop/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class Rop_Pro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Rop_Pro_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'rop-pro';
		$this->version     = '3.0.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->load_admin();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rop_Pro_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    8.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rop_Pro_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	/**
	 * Load admin logic.
	 *
	 * @uses Rop_Pro_Admin
	 */
	public function load_admin() {

		$plugin_pro_admin = new Rop_Pro_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $plugin_pro_admin, 'register_meta_box', 2 );
		$this->loader->add_action( 'save_post', $plugin_pro_admin, 'custom_repeatable_meta_box_save' );
		$this->loader->add_filter( 'rop_available_services', $plugin_pro_admin, 'available_services' );
		$this->loader->add_action( 'wp_loaded', $this, 'upgrade', 2 );
		$this->loader->add_action( 'rop_before_prepare_post', $plugin_pro_admin, 'update_post_publish_date' );

		$this->loader->add_filter( 'attachment_fields_to_edit', $plugin_pro_admin, 'rop_media_attachment_field', null, 2 );
		$this->loader->add_filter( 'attachment_fields_to_save', $plugin_pro_admin, 'save_rop_media_attachment_field', null, 2 );
		// Don't allow rvivly shortener yet
		// $this->loader->add_filter( 'rop_available_shorteners', $plugin_pro_admin, 'available_shorteners', null, 1 );
		$this->loader->add_filter( 'rop_shorten_url', $plugin_pro_admin, 'shorten_url', 10, 5 );
	}


	/**
	 * Upgrade method called by plugins_loaded hook.
	 *
	 * @since   8.0.0
	 * @access  public
	 */
	public function upgrade() {
		$upgrade_helper = new Rop_Pro_Db_Upgrade();
		if ( $upgrade_helper->is_upgrade_required() ) {
			$upgrade_helper->do_upgrade();
		}
	}
	/**
	 * Register the lite for tgmpa.
	 */
	public function register_lite() {
		if ( ! function_exists( 'tgmpa' ) ) {
			include_once ROP_PRO_DIR_PATH . 'lib/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php';
		}

		if ( function_exists( 'tgmpa' ) ) {
			add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );
		}
	}

	/**
	 * Initialize TGM.
	 */
	public function tgmpa_register() {
		$plugins = array(
			array(
				'name'     => 'Revive Old Post Lite',
				'slug'     => 'tweet-old-post',
				'required' => true,
			),
		);
		$config  = array(
			'id'           => 'tweet-old-post',
			// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',
			// Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',
			// Menu slug.
			'parent_slug'  => 'plugins.php',
			// Parent menu slug.
			'capability'   => 'manage_options',
			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,
			// Show admin notices or not.
			'dismissable'  => true,
			// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',
			// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,
			// Automatically activate plugins after installation or not.
			'message'      => '',
		);
		tgmpa( $plugins, $config );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$this->loader = new Rop_Pro_Loader();
		$this->loader->add_action( 'after_setup_theme', $this, 'register_lite', 999 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Rop_Pro_Loader Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return the type of licence for the plugin
	 *
	 * @since     2.0.0
	 * @return    string    The licence of the plugin.
	 */
	public function is_business() {
		return 'business'; // other option is 'pro'
	}
}
