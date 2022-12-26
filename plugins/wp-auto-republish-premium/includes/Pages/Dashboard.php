<?php 
/**
 * Dashboard actions.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Pages
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Pages;

use Wpar\Api\SettingsApi;
use Wpar\Helpers\HelperFunctions;
use Wpar\Api\Callbacks\AdminCallbacks;
use Wpar\Api\Callbacks\ManagerCallbacks;

defined( 'ABSPATH' ) || exit;

/**
 * Dashboard class.
 */
class Dashboard
{
	use HelperFunctions;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Callbacks.
	 *
	 * @var array
	 */
	public $callbacks;

	/**
	 * Callback Managers.
	 *
	 * @var array
	 */
	public $callbacks_manager;

	/**
	 * Settings pages.
	 *
	 * @var array
	 */
	public $pages = [];

	/**
	 * Register functions.
	 */
	public function register() 
	{
		$this->settings = new SettingsApi();
		$this->callbacks = new AdminCallbacks();
		$this->callbacks_manager = new ManagerCallbacks();

		$this->setPages();
		
		$this->setSettings();
		$this->setSections();
		$this->setFields();

		$this->settings->addPages( $this->pages )->withSubPage( __( 'WP Auto Republish', 'wp-auto-republish' ) )->register();
	}

	/**
	 * Register plugin pages.
	 */
	public function setPages() 
	{
		$this->pages = [
			[
				'page_title' => __( 'WP Auto Republish', 'wp-auto-republish' ), 
				'menu_title' => __( 'Auto Republish', 'wp-auto-republish' ), 
				'capability' => 'manage_options', 
				'menu_slug' => 'wp-auto-republish', 
				'callback' => [ $this->callbacks, 'adminDashboard' ], 
				'icon_url' => 'dashicons-update', 
				'position' => 100
			]
		];
	}

	/**
	 * Register plugin settings.
	 */
	public function setSettings()
	{
		$args = [
			[
				'option_group' => 'wpar_plugin_settings_fields',
				'option_name' => 'wpar_plugin_settings',
			]
		];

		$this->settings->setSettings( $args );
	}

	/**
	 * Register plugin sections.
	 */
	public function setSections()
	{
		$sections = [ 'default', 'post_query', 'post_type', 'republish_info', 'tools' ];
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$sections = array_merge( $sections, [ 'single_republish', 'email_notify', 'facebook', 'twitter', 'linkedin', 'tumblr' ] );
		}
		$args = [];
		foreach ( $sections as $section ) {
		    $args[] = [
		    	'id' => 'wpar_plugin_'.$section.'_section',
		    	'title' => '',
		    	'callback' => null,
		    	'page' => 'wpar_plugin_'.$section.'_option'
			];
		}

		$this->settings->setSections( $args );
	}

	/**
	 * Register settings fields.
	 */
	public function setFields()
	{
		$args = [];
		foreach ( $this->build_settings_fields() as $key => $value ) {
			foreach ( $value as $type => $settings ) {
			    $args[] = [
			    	'id' => $type,
			    	'title' => $settings,
			    	'callback' => [ $this->callbacks_manager, $type ],
			    	'page' => 'wpar_plugin_' . $key . '_option',
			    	'section' => 'wpar_plugin_' . $key . '_section',
			    	'args' => [
			    		'label_for' => 'wpar_' . str_replace( '__premium_only', '', $type ),
			    		'class' => 'wpar_css_' . str_replace( '__premium_only', '', $type )
			    	]
				];
			}
		}

		$this->settings->setFields( $args );
	}

	/**
	 * Build settings fields.
	 */
	private function build_settings_fields()
	{
		$managers = [
            'default' => [
				'enable_plugin' => __( 'Enable Auto Republishing?', 'wp-auto-republish' ),
				'minimun_republish_interval' => __( 'Run Republish Process in Every:', 'wp-auto-republish' ),
			    'random_republish_interval' => __( 'New Date Time Randomness:', 'wp-auto-republish' ),
				'republish_post_position' => __( 'Republish Post to Position:', 'wp-auto-republish' ),
			    'republish_time_start' => __( 'Start Time for Republishing:', 'wp-auto-republish' ),
			    'republish_time_end' => __( 'End Time for Republishing:', 'wp-auto-republish' ),
			    'republish_days' => __( 'Select Weekdays to Republish:', 'wp-auto-republish' ),
			],
			'republish_info' => [
			    'republish_info' => __( 'Show Original Publication Date:', 'wp-auto-republish' ),
				'republish_info_text' => __( 'Original Publication Message:', 'wp-auto-republish' ),
			],
			'post_query' => [
			    'republish_post_age' => __( 'Post Republish Eligibility Age:', 'wp-auto-republish' ),
			    'republish_order' => __( 'Select Published Posts Order:', 'wp-auto-republish' ),
				'republish_orderby' => __( 'Select Published Posts Order by:', 'wp-auto-republish' ),
			],
			'post_type' => [
			    'post_types_list' => __( 'Select Post Type(s) to Republish:', 'wp-auto-republish' ),
			    'exclude_by_type' => __( 'Auto Republish Old Posts by:', 'wp-auto-republish' ),
			    'post_taxonomy' => __( 'Select Post Type(s) Taxonomies:', 'wp-auto-republish' ),
			    'override_category_tag' => __( 'Override Taxonomies Filtering for these Specified Post Types:', 'wp-auto-republish' ),
			],
			'tools' => [
			    'remove_plugin_data' => __( 'Delete Plugin Data on Uninstall?', 'wp-auto-republish' ),
			],
		];

		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
		    $managers['default'] = $this->insert_settings__premium_only( $managers['default'], 2, [
				'republish_custom_interval__premium_only' => __( 'Custom Interval (in minutes):', 'wp-auto-republish' ),
			] );
			
			$managers['default'] = $this->insert_settings__premium_only( $managers['default'], 5, [
				'republish_action__premium_only' => __( 'Post Auto Republish Action:', 'wp-auto-republish' ),
				'number_of_posts__premium_only' => __( 'No. of Posts to be Republished:', 'wp-auto-republish' ),
			] );

			$managers['republish_info'] = $this->insert_settings__premium_only( $managers['republish_info'], 2, [
		        'post_types_list_display__premium_only' => __( 'Select Post Type(s) to Display:', 'wp-auto-republish' ),
			] );

			$managers['single_republish'] = [
				'enable_single_republishing__premium_only' => __( 'Enable Individual Republishing:', 'wp-auto-republish' ),
				'enable_instant_republishing__premium_only' => __( 'Enable One Click Republishing:', 'wp-auto-republish' ),
				'single_republish_action__premium_only' => __( 'Single Post Republish Action:', 'wp-auto-republish' ),
				'post_types_list_single__premium_only' => __( 'Enable Republish Options on:', 'wp-auto-republish' ),
				'single_roles__premium_only' => __( 'Allow Republish for User Roles:', 'wp-auto-republish' ),
			];

			$managers['post_query'] = $this->insert_settings__premium_only( $managers['post_query'], 1, [
				'republish_custom_age__premium_only' => __( 'Enter Custom Age (in days):', 'wp-auto-republish' ),
			] );

			$managers['post_query'] = $this->insert_settings__premium_only( $managers['post_query'], 4, [
				'republish_post_age_start_method__premium_only' => __( 'Exclude Posts Published Before:', 'wp-auto-republish' ),
				'republish_post_age_start__premium_only' => __( 'Specific Date to Exclude Posts:', 'wp-auto-republish' ),
				'republish_custom_age_start__premium_only' => __( 'Enter Time Interval (in minutes):', 'wp-auto-republish' ),
			] );
			
			$managers['email_notify'] = [
				'enable_email_notify__premium_only' => __( 'Enable Auto Email Notification?', 'wp-auto-republish' ),
				'enable_post_author_email__premium_only' => __( 'Send Notification to Post Author?', 'wp-auto-republish' ),
				'email_recipients__premium_only' => __( 'Set List of Email Recipient(s):', 'wp-auto-republish' ),
				'email_post_types__premium_only' => __( 'Enable Email for Post Types:', 'wp-auto-republish' ),
				'email_subject__premium_only' => __( 'Notification Email Subject:', 'wp-auto-republish' ),
				'email_message__premium_only' => __( 'Notification Email Message Body:', 'wp-auto-republish' ),
			];

			$managers['facebook'] = [
				'fb_social_enable__premium_only' => __( 'Enable Auto Facebook Share:', 'wp-auto-republish' ),
				'fb_social_og_tag__premium_only' => __( 'Add Facebook Meta to Header:', 'wp-auto-republish' ),
				'fb_social_post_as__premium_only' => __( 'Facebook Post Default Content:', 'wp-auto-republish' ),
				'fb_social_content_source__premium_only' => __( 'Content Source for Post:', 'wp-auto-republish' ),
				'fb_social_template__premium_only' => __( 'Facebook Share / Post Template:', 'wp-auto-republish' ),
				'fb_post_types_list_display__premium_only' => __( 'Enable Share for Post Type(s):', 'wp-auto-republish' ),
				'fb_social_taxonomy__premium_only' => __( 'Post Taxonomies as Hashtags:', 'wp-auto-republish' ),
			];

			$managers['twitter'] = [
				'tw_social_enable__premium_only' => __( 'Enable Auto Twitter Share:', 'wp-auto-republish' ),
				'tw_social_thumbnail__premium_only' => __( 'Post Default Thumbnail Posting:', 'wp-auto-republish' ),
				'tw_social_content_source__premium_only' => __( 'Content Source for Tweet:', 'wp-auto-republish' ),
				'tw_social_template__premium_only' => __( 'Twitter Share / Tweet Template:', 'wp-auto-republish' ),
				'tw_post_types_list_display__premium_only' => __( 'Enable Share for Post Type(s):', 'wp-auto-republish' ),
				'tw_social_taxonomy__premium_only' => __( 'Tweet Taxonomies as Hashtags:', 'wp-auto-republish' ),
			];

			$managers['linkedin'] = [
				'ld_social_enable__premium_only' => __( 'Enable Auto Linkedin Share:', 'wp-auto-republish' ),
				'ld_social_post_as__premium_only' => __( 'Linkedin Post Default Content:', 'wp-auto-republish' ),
				'ld_social_content_source__premium_only' => __( 'Content Source for Post:', 'wp-auto-republish' ),
				'ld_social_template__premium_only' => __( 'Linkedin Share / Post Template:', 'wp-auto-republish' ),
				'ld_post_types_list_display__premium_only' => __( 'Enable Share for Post Type(s):', 'wp-auto-republish' ),
				'ld_social_taxonomy__premium_only' => __( 'Post Taxonomies as Hashtags:', 'wp-auto-republish' ),
			];

			$managers['tools'] = $this->insert_settings__premium_only( $managers['tools'], 0, [
				'disable_log__premium_only' => __( 'Disable Republish Log History:', 'wp-auto-republish' ),
				'enable_silent_republishing__premium_only' => __( 'Disable Actual Publishing Event:', 'wp-auto-republish' )
			] );
		}

		return $managers;
	}
}