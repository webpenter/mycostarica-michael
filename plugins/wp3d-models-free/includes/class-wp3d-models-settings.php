<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP3D_Models_Settings {

	/**
	 * The single instance of WP3D_Models_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wp3d_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
		
		// Add documentation link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_documentation_link' ) );		
		
		// Add What's New link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_whatsnew_link' ) );		
		
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		//add_options_page( __( 'WP3D Models Settings', 'wp3d-models' ) , __( 'WP3D Models', 'wp3d-models' ) , 'manage_options' , $this->parent->_token . '_settings_legacy' ,  array( $this, 'settings_page_legacy' ) );
		add_submenu_page( 'edit.php?post_type=model', __( 'WP3D Models Settings', 'wp3d-models' ) , __( 'Settings', 'wp3d-models' ), 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page') );
		add_submenu_page( 'edit.php?post_type=model', __( 'WP3D Models - What\'s New', 'wp3d-models' ) , __( 'What\'s New <span class="dashicons dashicons-star-filled wp3d-whats-new-star"></span>', 'wp3d-models' ), 'manage_options' , $this->parent->_token . '_whatsnew' ,  array( $this, 'whatsnew_page') );
		add_action( 'admin_enqueue_scripts', array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// Needed if image uploading is added to SETTINGS
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/min/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="' . esc_url(admin_url('/edit.php?post_type=model&page=' . $this->parent->_token . '_settings') ) . '">' . __( 'Settings', 'wp3d-models' ) . '</a>';
		//above link assembly could use some cleanup...use next line for reference
		//$settings_link = '<a href="' . esc_url( add_query_arg( 'page', $this->parent->_token . '_settings', admin_url( 'options-general.php' ) ) ) . '">' . __( 'Settings', 'wp3d-models' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}
	
	
	/**
	 * Add documentation link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_documentation_link ( $links ) {
		$documentation_link = '<a href="' . esc_url( 'http://wp3dmodels.com/docs/' ) . '" target="_blank">' . __( 'Docs', 'wp3d-models' ) . '</a>';
  		array_push( $links, $documentation_link );
  		return $links;
	}	
	
	
	/**
	 * Add whats new link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_whatsnew_link ( $links ) {
		$whatsnew_link = '<a href="' . esc_url( admin_url('/edit.php?post_type=model&page=' . $this->parent->_token . '_whatsnew')  ) . '" target="_blank">' . __( 'What\'s New', 'wp3d-models' ) . '</a>';
  		array_push( $links, $whatsnew_link );
  		return $links;
	}		
	

	/**
	 * Load whats new page content
	 * @return void
	 */
	public function whatsnew_page () {

		$dir_path = plugin_dir_path( __FILE__ );
		$includes_url = plugins_url().'/wp3d-models-free/includes';
		
		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_whatsnew">' . "\n";
		$html .= '<h2>' . __( 'WP3D Models - What\'s New' , 'wp3d-models' ) . '</h2>' . "\n";
		$html .= '<img src="'.$includes_url.'/whats-new/wp3d-logo-horizontal-trans.png" class="whats-new-header">' . "\n";
		$html .= file_get_contents( $dir_path.'/whats-new/wp3d-models-whatsnew.php', true );
		$html .= '</div>' . "\n";
		echo $html;
	}	
	
	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {
		
		$settings['settings'] = array(
			'title'					=> __( 'General', 'wp3d-models' ),
			'description'			=> __( '<p>Option(s) to customize your installation of WP3D Models</p>', 'wp3d-models' ),
			'fields'				=> array(
				// this too is a bit of a wildcard, doesn't follow the normal field "text" rules - see class-wp3d-models-admin-api.php
				array(
					'id' 			=> 'license_key',
					'label'			=> __( 'License Key', 'wp3d-models' ),
					'description'	=> '',
					'type'			=> 'license-text',
					'default'		=> '',
					'placeholder'	=> __( 'Enter WP3D License Here', 'wp3d-models' )
				),
				array(
					'id' 			=> 'single_slug',
					'label'			=> __( 'Single Page Slug', 'wp3d-models' ),
					'description'	=> __( 'Customize the single "slug" to be something different than "3d-model"', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( '3d-model', 'wp3d-models' )
				),
				array(
					'id' 			=> 'tools_location',
					'label'			=> __( '"WP3D Models Tools" Menu', 'wp3d-models' ),
					'description'	=> __( 'Where should the "WP3D Tools" Admin menu be displayed?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 'toolbar' => 'Toolbar', 'side' => 'Side' ),
					'default'		=> 'toolbar'
				),				
				array(
					'id' 			=> 'default_view_link',
					'label'			=> __( 'Default View', 'wp3d-models' ),
					'description'	=> __( 'Which "View" should list pages link to, by default?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 'stock' => 'Standard', 'skinned' => 'Skinned', 'nobrand' => 'No Branding', 'fullscreen' => 'Fullscreen', 'fullscreen-nobrand' => 'Fullscreen No Branding' ),
					'default'		=> 'stock'
				),
				array(
					'id' 			=> 'global_default_gallery',
					'label'			=> __( 'Photo Gallery', 'wp3d-models' ),
					'description'	=> __( 'Which photo gallery type should be used, by default?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 'standard_slider' => 'Standard Slider', 'zoom_slider' => 'Zoom Slider' ),
					'default'		=> 'standard_slider'
				),
				array(
					'id' 			=> 'desktop_columns',
					'label'			=> __( 'Desktop Columns', 'wp3d-models' ),
					'description'	=> __( 'For list pages, how many columns on a DESKTOP?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( '2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns' ),
					'default'		=> '3'
				),
				array(
					'id' 			=> 'tablet_columns',
					'label'			=> __( 'Tablet Columns', 'wp3d-models' ),
					'description'	=> __( 'For list pages, how many columns on a TABLET?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( '2' => '2 Columns', '3' => '3 Columns' ),
					'default'		=> '2'
				),	
				array(
					'id' 			=> 'phone_columns',
					'label'			=> __( 'Phone Columns', 'wp3d-models' ),
					'description'	=> __( 'For list pages, how many columns on a PHONE?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( '1' => '1 Column', '2' => '2 Columns' ),
					'default'		=> '1'
				),	
				array(
					'id' 			=> 'hide_branding',
					'label'			=> __( 'Hide WP3D Text', 'wp3d-models' ),
					'description'	=> __( 'Check this box if you\'d like to hide the "Powered by WP3D Models" Text', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> 'on'
				),
				array(
					'id' 			=> 'disable_schema',
					'label'			=> __( 'Disable Schema', 'wp3d-models' ),
					'description'	=> __( 'Disable the "Place" Schema data added to WP3D Models "views".', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'enable_form_capture_content',
					'label'			=> __( 'Form Capture Content', 'wp3d-models' ),
					'description'	=> __( 'Enable the "Capture Content" authorization checkbox on the Skinned view form.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'recaptcha_sitekey',
					'label'			=> __( 'reCAPTCHA Site Key', 'wp3d-models' ),
					'description'	=> __( 'Enter your reCAPTCHA "Site key" (<a href="https://wp3dmodels.com/doc/adding-recaptcha-api-keys/" target="_blank" title="Open Documentation in new window/tab">Documentation &raquo;</a>)', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'reCAPTCHA Site key', 'wp3d-models' )
				),
				array(
					'id' 			=> 'recaptcha_secretkey',
					'label'			=> __( 'reCAPTCHA Secret Key', 'wp3d-models' ),
					'description'	=> __( 'Enter your reCAPTCHA "Secret key" (<a href="https://wp3dmodels.com/doc/adding-recaptcha-api-keys/" target="_blank" title="Open Documentation in new window/tab">Documentation &raquo;</a>)', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'reCAPTCHA Secret key', 'wp3d-models' )
				),
				array(
					'id' 			=> 'disclaimer_text',
					'label'			=> __( 'Disclaimer Text', 'wp3d-models' ),
					'description'	=> __( 'Enter any (optional) disclaimer information to be included in the footer of "Skinned", "Standard" & "Nobrand" views. This field also accepts &lt;img&gt; and &lta&gt; HTML tags.', 'wp3d-models' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
			)
		);
		

		$settings['maps'] = array(
			'title'					=> __( 'Maps', 'wp3d-models' ),
			'description'			=> __( '<p>Option(s) to customize how maps are displayed on WP3D Models list pages & non-"Skinned" views.</p>', 'wp3d-models' ),
			'fields'				=> array(
				array(
					'id' 			=> 'google_maps_api_server_key',
					'label'			=> __( 'Google Maps API Key', 'wp3d-models' ),
					'description'	=> __( 'Enter your Google Maps API Key (<a href="https://wp3dmodels.com/doc/google-maps-api-keys/" target="_blank" title="Open Documentation in new window/tab">Documentation &raquo;</a>)', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'API Key', 'wp3d-models' )
				),				
				array(
					'id' 			=> 'single_page_map_zoom',
					'label'			=> __( 'Single Page Map Zoom Level', 'wp3d-models' ),
					'description'	=> __( 'Select the default zoom level for single model maps', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( '0' => '0 (Whole World)', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18 - (Max Zoom)',),
					'default'		=> '10'
				),
				array(
					'id' 			=> 'single_page_map_type',
					'label'			=> __( 'Single Page Map Type', 'wp3d-models' ),
					'description'	=> __( 'Pick the map type for your single model maps', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 'ROADMAP' => 'Roadmap', 'SATELLITE' => 'Satellite', 'HYBRID' => 'Hybrid', 'TERRAIN' => 'Terrain' ),
					'default'		=> 'ROADMAP'
				),	
				array(
					'id' 			=> 'list_page_map_type',
					'label'			=> __( 'List Page Map Type', 'wp3d-models' ),
					'description'	=> __( 'Pick the map type for your list/shortcode maps', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 'ROADMAP' => 'Roadmap', 'SATELLITE' => 'Satellite', 'HYBRID' => 'Hybrid', 'TERRAIN' => 'Terrain' ),
					'default'		=> 'ROADMAP'
				),					
				array(
					'id' 			=> 'marker_type',
					'label'			=> __( 'Map Marker Type', 'wp3d-models' ),
					'description'	=> __( 'What type of map "pin" do you want to use?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 'stock' => 'Stock Google Pin', 'circle' => 'Custom Circle' ),
					'default'		=> 'stock'
				),				
			)
		);		
		
		$settings['branding'] = array(
			'title'					=> __( 'Branding', 'wp3d-models' ),
			'description'			=> __( '<p>Use these fields to customize the way you WP3D Pages look &amp; feel.</p>', 'wp3d-models' ),
			'fields'				=> array(
				array(
					'id' 			=> 'custom_title',
					'label'			=> __( 'Custom Title', 'wp3d-models' ),
					'description'	=> __( '<br>Customize your "Title" here.  If empty, WP3D Models will use the information entered in the "Site Title" field from: Settings->General.', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( '"Title" Override', 'wp3d-models' )
				),
				array(
					'id' 			=> 'large_company_logo',
					'label'			=> __( 'Large Logo' , 'wp3d-models' ),
					'description'	=> __( 'This "large" logo image should be uploaded as a transparent (32-bit) PNG and visible on a dark background.<br>It should be sized at exactly 400px wide by 400px tall (square).<br><br>NOTE: Unless overridden by a specific model, this image will be seen atop all of your showcases with "Intro" enabled, as well as your "Skinned" view.', 'wp3d-models' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'small_company_logo',
					'label'			=> __( 'Small Logo' , 'wp3d-models' ),
					'description'	=> __( 'This "small" logo image should be uploaded as a transparent (32-bit) PNG and visible on a dark background.<br>It should be sized at exactly 300px wide by 120px tall (landscape).<br><br>NOTE: If enabled, this logo will be used for the default showcase overlay logo.', 'wp3d-models' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'logo_link',
					'label'			=> __( 'Skinned Logo Link', 'wp3d-models' ),
					'description'	=> __( 'Unless overriden, always link the "Skinned" view logo back to this site\'s home page URL.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),					
				array(
					'id' 			=> 'standard_company_branding',
					'label'			=> __( 'Standard Branding', 'wp3d-models' ),
					'description'	=> __( 'Even when overridden, always show my company logos (set above) on the "Standard" views.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'sold_image',
					'label'			=> __( 'Sold Image' , 'wp3d-models' ),
					'description'	=> __( 'This "SOLD" (corner flap) image should be uploaded as a transparent (32-bit) PNG and build to look/work well in the upper right corner of the showcase/space.<br>It should be sized at exactly 200px wide by 200px tall (square).', 'wp3d-models' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'pending_image',
					'label'			=> __( 'Pending Image' , 'wp3d-models' ),
					'description'	=> __( 'This "SALE PENDING" (corner flap) image should be uploaded as a transparent (32-bit) PNG and build to look/work well in the upper right corner of the showcase/space.<br>It should be sized at exactly 200px wide by 200px tall (square).', 'wp3d-models' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'custom_status_image',
					'label'			=> __( 'Custom Status Image' , 'wp3d-models' ),
					'description'	=> __( 'This "CUSTOM STATUS" (corner flap) image should be uploaded as a transparent (32-bit) PNG and build to look/work well in the upper right corner of the showcase/space.<br>It should be sized at exactly 200px wide by 200px tall (square).', 'wp3d-models' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),				
				array(
					'id' 			=> 'view_button_color',
					'label'			=> __( 'View Button Color', 'wp3d-models' ),
					'description'	=> __( 'Select a Color for the "VIEW" Button Color', 'wp3d-models' ),
					'type'			=> 'color',
					'default'		=> '#f39c12'
				),
				array(
					'id' 			=> 'map_button_color',
					'label'			=> __( 'Map Button Color', 'wp3d-models' ),
					'description'	=> __( 'Select a Color for the "MAP" Button Color', 'wp3d-models' ),
					'type'			=> 'color',
					'default'		=> '#e67e22'
				)
			)
		);					
				
	$settings['social'] = array(
			'title'					=> __( 'Social', 'wp3d-models' ),
			'description'			=> __( '<p>Use these fields to include links to your social network profiles.</p>', 'wp3d-models' ),
			'fields'				=> array(
				array(
					'id' 			=> 'enable_sharing',
					'label'			=> __( 'Enable Social Sharing', 'wp3d-models' ),
					'description'	=> __( 'Enable model sharing on Facebook, Twitter, LinkedIn & Pinterest', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'enable_social_overlay',
					'label'			=> __( 'Enable Social Overlay Image', 'wp3d-models' ),
					'description'	=> __( 'Dynamically add an icon overlay to images that are shared on social networks.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'connect_message',
					'label'			=> __( 'SOCIAL NETWORK PROFILES', 'wp3d-models' ),
					'description'	=> __( '<b>USE THE FOLLOWING FIELDS TO ENTER URLS TO YOUR COMPANY\'S SOCIAL NETWORK PROFILES</b><br>Note that this content is only used on the FULLSCREEN & EMBED views.', 'wp3d-models' ),
					'type'			=> 'message'
				),
				array(
					'id' 			=> 'company_website',
					'label'			=> __( 'Company Website', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your company website', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'http://yourcompanyurlhere.com', 'wp3d-models' )
				),
				array(
					'id' 			=> 'company_facebook',
					'label'			=> __( 'Facebook Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your Facebook Page', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://www.facebook.com/xxx', 'wp3d-models' )
				),
				array(
					'id' 			=> 'company_twitter',
					'label'			=> __( 'Twitter Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your Twitter Page', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://twitter.com/xxx', 'wp3d-models' )
				),	
				array(
					'id' 			=> 'company_linkedin',
					'label'			=> __( 'LinkedIn Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your LinkedIn Page/Profile', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://www.linkedin.com/xxx', 'wp3d-models' )
				),
				array(
					'id' 			=> 'company_youtube',
					'label'			=> __( 'YouTube Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your YouTube Channel/Profile', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://www.youtube.com/xxx', 'wp3d-models' )
				),
				array(
					'id' 			=> 'company_vimeo',
					'label'			=> __( 'Vimeo Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your Vimeo Page/Profile', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://www.vimeo.com/xxx', 'wp3d-models' )
				),				
				array(
					'id' 			=> 'company_pinterest',
					'label'			=> __( 'Pinterest Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your Pinterest Page/Profile', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://www.pinterest.com/xxx', 'wp3d-models' )
				),					
				array(
					'id' 			=> 'company_instagram',
					'label'			=> __( 'Instagram Page URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your Instagram Profile Page', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://instagram.com/xxx', 'wp3d-models' )
				),	
				array(
					'id' 			=> 'company_google',
					'label'			=> __( 'Google Business URL', 'wp3d-models' ),
					'description'	=> __( 'Full URL to your Google Business Page Profile', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'https://g.page/xxx?gm', 'wp3d-models' )
				)				
			)
		);
		
	$settings['options'] = array(
			'title'					=> __( 'Options', 'wp3d-models' ),
			'description'			=> __( '<p>Use these fields to make global WP3D options adjustments.</p>', 'wp3d-models' ),
			'fields'				=> array(
				array(
					'id' 			=> 'enable_sharing_autostart',
					'label'			=> __( 'Skinned/Intro Autostart', 'wp3d-models' ),
					'description'	=> __( 'Autostart "Skinned" & "Intro"-enabled views from inbout share links (simulates "play button" click).', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),	
				array(
					'id' 			=> 'disable_skinned_scroll',
					'label'			=> __( 'Skinned Scrolling', 'wp3d-models' ),
					'description'	=> __( 'When possible, disable content-specific mousewheel, trackpad, & swipe support on the "Skinned" view.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),	
				array(
					'id' 			=> 'max_property_tabs',
					'label'			=> __( 'Property Tab Limit', 'wp3d-models' ),
					'description'	=> __( 'Customize the maximum number of (Skinned View) Property Info Tabs. Default is 3.', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( '3', 'wp3d-models' )
				),	
				array(
					'id' 			=> 'enable_property_tab_media',
					'label'			=> __( 'Enable Property Tab Media', 'wp3d-models' ),
					'description'	=> __( 'Enable the advanced "Media" tools for Property Info Tabs (WYSIWYG).', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'mp_options',
					'label'			=> __( 'MATTERPORT', 'wp3d-models' ),
					'description'	=> __( '<b>Global Matterport-specific Settings.</b>', 'wp3d-models' ),
					'type'			=> 'message'
				),				
				array(
					'id' 			=> 'enable_global_quickstart',
					'label'			=> __( 'Skinned/Intro Quickstart', 'wp3d-models' ),
					'description'	=> __( 'Globally enable Showcase quickstart on WP3D "Intros" and "Skinned" view. "Help" screen will be disabled.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'disable_nobrand_links',
					'label'			=> __( 'Unbranded Mattertag Links', 'wp3d-models' ),
					'description'	=> __( 'On Unbranded ("Nobrand") views, also remove Mattertag links. (Required by some MLSs)', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'mp_guided_tour_seconds',
					'label'			=> __( 'Guided Tour Delay', 'wp3d-models' ),
					'description'	=> __( 'Number of seconds after initial fly-in before a guided tour automatically starts. Default is 1 second.', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( '1', 'wp3d-models' )
				),
				array(
					'id' 			=> 'mp_highlight_time',
					'label'			=> __( 'Default Hightlight Time', 'wp3d-models' ),
					'description'	=> __( 'Time in milliseconds spent at each highlight during a Guided Tour. Default is 3500 ms.', 'wp3d-models' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( '3500', 'wp3d-models' )
				),
				array(
					'id' 			=> 'mp_default_lang',
					'label'			=> __( 'Showcase Language', 'wp3d-models' ),
					'description'	=> __( 'Which Language should be used (for Showcase text labels), by default?', 'wp3d-models' ),
					'type'			=> 'select',
					'options'		=> array( 
									'en' => 'English (Default)', 
									'es' => 'Spanish', 
									'fr' => 'French', 
									'de' => 'German', 
									'ru' => 'Russian', 
									'cn' => 'Chinese', 
									'jp' => 'Japanese', 
									),
					'default'		=> 'en'
				)
			)
		);		

		$settings['css_js'] = array(
			'title'					=> __( 'CSS & JS', 'wp3d-models' ),
			'description'			=> __( '<p><b>DANGER ZONE! BE SURE YOU KNOW WHAT YOU\'RE DOING HERE!</b><br>Below are options to disable or modify specific Javascript/CSS files that are included with WP3D Models.</p>', 'wp3d-models' ),
			'fields'				=> array(		
				array(
					'id' 			=> 'custom_css',
					'label'			=> __( 'CSS Rules' , 'wp3d-models' ),
					'description'	=> __( 'Enter your (properly formatted) CSS rules above.', 'wp3d-models' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( '', 'wp3d-models' )
				),				
				array(
					'id' 			=> 'disable_css',
					'label'			=> __( 'Disable WP3D Models CSS', 'wp3d-models' ),
					'description'	=> __( 'Check this box if you\'re a CSS whiz and would like to disable the plugin CSS & manage the styling yourself', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),				
				array(
					'id' 			=> 'disable_fontawesome_css',
					'label'			=> __( 'Disable FontAwesome CSS', 'wp3d-models' ),
					'description'	=> __( '<a href="http://fontawesome.io/" target="_blank">FontAwesome</a> is used for icons. Disable here if you\'re sure it is being included elsewhere on your site.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),								
				array(
					'id' 			=> 'disable_google_maps_js',
					'label'			=> __( 'Disable Google Maps JS' , 'wp3d-models' ),
					'description'	=> __( '<a href="https://developers.google.com/maps/web/" target="_blank">Google Maps JS</a> is used for maps. Disable here if you\'re sure it is being included elsewhere on your site.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'disable_swiper_js',
					'label'			=> __( 'Disable Swiper JS' , 'wp3d-models' ),
					'description'	=> __( '<a href="http://www.idangero.us/swiper/" target="_blank">Swiper JS</a> is used on photo galleries. Disable here if you\'re sure it is being included elsewhere on your site.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'disable_slick_js',
					'label'			=> __( 'Disable Slick JS' , 'wp3d-models' ),
					'description'	=> __( '<a href="http://kenwheeler.github.io/slick/" target="_blank">Slick JS</a> is used for related model display. Disable here if you\'re sure it is being included elsewhere on your site.', 'wp3d-models' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),	
				// Not yet enabled - COMING SOON! Hint Hint
				// array(
				// 	'id' 			=> 'disable_isotope_js',
				// 	'label'			=> __( 'Disable Isotope JS' , 'wp3d-models' ),
				// 	'description'	=> __( '<a href="http://isotope.metafizzy.co/" target="_blank">Isotope - Magical Layouts</a>', 'wp3d-models' ),
				// 	'type'			=> 'checkbox',
				// 	'default'		=> ''
				// ),
				// Not necessary, can likely be fully removed
				// array(
				// 	'id' 			=> 'remove_actions',
				// 	'label'			=> __( 'WP3D Views Remove Actions' , 'wp3d-models' ),
				// 	'description'	=> __( 'DANGER - <a href="https://codex.wordpress.org/Function_Reference/remove_action" target="_blank">Be sure you know what you\'re doing here.</a> Syntax = [tag,function_name,priority]', 'wp3d-models' ),
				// 	'type'			=> 'textarea',
				// 	'default'		=> '',
				// 	'placeholder'	=> __( 'One removal per line:'."\r".'[tag,function_name,priority]', 'wp3d-models' )
				// ),				
			)
		);	
		
		$settings['documentation'] = array(
			'title'					=> __( 'Documentation', 'wp3d-models' ),
			'description'			=> __( '
				<h4>SHORTCODE SYNTAX:</h4>
				<div class="wp3d-wrapped-code"><code>[wp3d-models type="" client="" orderby="" order="" posts="" map="" filter=""]</code></div>
				<h4>SHORTCODE ATTRIBUTE OPTIONS:</h4>
				<ul class="wp3d-wrapped-list">
				<li><b>"type"</b> = <a href="edit-tags.php?taxonomy=model-type&post_type=model">Filter by Custom Model Type Slug</a> (default is ALL types)</li>
				<li><b>"client"</b> = <a href="edit-tags.php?taxonomy=model-client&post_type=model">Filter by Client Slug</a> (default is ALL clients)</li>
				<li><b>"orderby"</b> = "menu_order", title", or "date" (default is "menu_order")</li>	
				<li><b>"order"</b> = "ASC" or "DESC" (default is descending, "DESC")</li>
				<li><b>"posts"</b> = NUMERIC (if you entered "20", then the page would limit matching results to just 20)</li>
				<li><b>"map"</b> = "true" or "false" or "only" (default is "false" -- "only" will hide all thumbnails and just show the BIGGER mapped results)</li>
				<li><b>"filter"</b> = "true" or "false" (default is "false" -- "true" will return a list of links to filter ALL models by "type".  If a "type" is also included, it will be trumped.)</li>
				</ul>
				<h4>WP3D MODELS DOCUMENTATION:</h4>
				<ul class="wp3d-wrapped-list">
				<li><a href="http://wp3dmodels.com/docs/" target="_blank">View all WP3D Models Documentation &raquo;<a/></li>
				</ul>
			', 'wp3d-models' ),
			'fields'				=> array(
				// EMPTY, NO "REAL" CONFIGURABLE OPTONS IN THIS TAB
			)
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = $this->settings[ $section['id'] ]['description'] . "\n";
		
		echo $html;
	}
	
	
	/**
	 * Load LEGACY settings page content
	 * @return void
	 */
	public function settings_page_legacy () { // eventually this can be removed
		
		ob_start();

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings_legacy">' . "\n";
		$html .= '<h2>' . __( 'WP3D Models Settings - PAGE HAS MOVED!' , 'wp3d-models' ) . '</h2>' . "\n";
        $wp3d_settings_url = esc_url(admin_url('/edit.php?post_type=model&page=' . $this->parent->_token . '_settings')); 
        $html .= '<p>' . sprintf( __( '<strong>HEADS UP! The WP3D Settings page has moved.</strong>  Please note the <a href="%s">new location under the "MODELS" Menu &raquo;</a>', 'wp3d-models' ), $wp3d_settings_url ) . '</p>'. "\n";
		$html .= '<p>' . __( '<em>(This page will be going away in future versions.)</em>' , 'wp3d-models' ) . '</p>';
		$html .= ob_get_clean();

		echo $html;
	}	

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'WP3D Models Settings' , 'wp3d-models' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . esc_url($tab_link) . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wp3d-models' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
			
			// DISCLAIMER (DISABLED FOR NOW)
			//$html .= '<div>' . "\n";
			//$html .= '<i>All Matterport product and company names are trademarks&trade; or registered&reg; trademarks of Matterport, Inc. Use of them does not imply any affiliation with or endorsement by them.</i>';
			//$html .= '</div>' . "\n";
			
			
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main WP3D_Models_Settings Instance
	 *
	 * Ensures only one instance of WP3D_Models_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP3D_Models()
	 * @return Main WP3D_Models_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp3d-models' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp3d-models' ), $this->parent->_version );
	} // End __wakeup()

}
