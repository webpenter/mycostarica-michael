<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP3D_Models {

	/**
	 * The single instance of WP3D_Models.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = WP3D_MODELS_VERSION ) {
		$this->_version = $version;
		$this->_token = 'WP3D_Models';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		// This may need to be revisited...but for now, script debugging (via WP Config) is disabled
		//$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->script_suffix = '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );
		
		// checks version
		add_action('plugins_loaded', array( $this, 'check_version'), 10, 1 );

		// Look for ACF Set the Primary Wordpress Image Filter & moving the content editor
		if ( class_exists('acf') ) {
			// set the "image_override" field to be featured
			add_filter( 'acf/update_value/name=image_override', array($this, 'set_featured_image'), 10, 3 );
			// set the "image_override" field to be featured
			add_filter( 'acf/update_value/name=agent_image', array($this, 'set_featured_image'), 10, 3 );			
			// set the google maps API Key using the stored WP3D setting
			add_filter('acf/fields/google_map/api', array($this, 'acf_google_map_api'), 10, 3 );
			// Enable ACF 4 class names 
			add_filter('acf/compatibility/field_wrapper_class', '__return_true');		
			// move the 'model' content editor to live inside the "INFO" tab
			add_action('acf/input/admin_head', array( $this, 'acf_content_to_info'), 10);
		}
		
		// Checking for Yoast SEO - Hiding some columns on the models
		if ( get_option( 'wpseo' ) && function_exists('wpseo_init') ) {
			add_filter( 'manage_edit-model_columns', array($this, 'wp_seo_columns_filter'), 10 );
		}
		
		// Checking for Yoast SEO Open Graph class - REQUIRES 'WORDPRESS SEO' Plugin by Yoast
		if ( get_option( 'wpseo_social' ) && function_exists('wpseo_init') ) {
			add_filter('wpseo_opengraph_image', array( $this, 'get_fb_image' ), 10 );
			add_filter('wpseo_twitter_image', array( $this, 'get_fb_image' ), 10 );
			add_filter('wpseo_opengraph_desc', array( $this, 'get_fb_description' ), 10 );
			add_filter('wpseo_canonical', array( $this, 'get_model_canonical' ), 10 );
			add_filter('wpseo_opengraph_url', array( $this, 'get_model_canonical' ), 10 );
		}
		
		// Checking for a new plugin setting and the existance of Yoast SEO - if there's a match, we try to add the new OG Video tags
		// NO REASON TO USE YET
		// if ( get_option( 'wp3d_enable_og_video_tags' ) && class_exists('WPSEO_OpenGraph') ) {
		// 	add_action( 'wpseo_head', array( $this, 'get_wpseo_video_tags' ), 40 );
		// }
		
		// Checking for and adding JSON Schema data, if it isn't disabled
		if (!get_option('wp3d_disable_schema')) { // if the "disable schema" global option is not set
		     add_action('wp_head', array( $this, 'show_schema_data'), 10 );     
		} // end disable schema check 
		
		// Checking to see if Social Images are enabled, modify the OG Image Width/Height
		// This is a bit of a mess still, as of 5/2/20 Yoast SEO doesn't have a filter in place to independently override the image og:image:width & og:image:height values for cases when a new/different image has been uploaded.
		if (get_option('wp3d_enable_social_overlay') && class_exists('Yoast\WP\SEO\Integrations\Front_End_Integration')) { 
			add_filter( 'wpseo_frontend_presenters', array( $this, 'add_wpseo_custom_image_sizes' ), 10 );
		}

		// Customize the Model Columns
		add_action('manage_pages_custom_column', array( $this, 'model_custom_columns'), 10);
		add_filter('manage_edit-model_columns', array( $this, 'model_columns_order'), 10);

		// Check to see if we need a single model NAG
		add_action( 'admin_notices', array( $this, 'single_model_view_override_notice'), 10); 
		add_action( 'admin_notices', array( $this, 'single_model_exclude_notice'), 10); 
		
		// Extend HTTP response timeout
		add_filter( 'http_request_timeout', array( $this, 'extend_http_request_timeout'), 10);
		
		if( get_option('wp3d_tools_location') == '' || get_option('wp3d_tools_location') == 'toolbar'  ) {
			
			// Force load Admin Bar for Admins
			add_filter('show_admin_bar', array( $this, 'show_admin_bar'), 10);
		
			// Add content to the Admin Bar
			add_action('admin_bar_menu', array( $this, 'admin_bar_menu'), 1000 );
		}
		
		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		
		// Load inline styles if optional colors, or override "flaps" are set
		if (
			get_option('wp3d_view_button_color') || 
			get_option('wp3d_map_button_color') ||
			get_option('wp3d_sold_image') ||
			get_option('wp3d_pending_image') ||
			get_option('wp3d_custom_status_image') 
			) {
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_styles' ), 20, 1 );
		}

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );
		
		// Load functions to run when models are added/saved/updated
		add_image_size( 'mp-thumb-size', 400, 230, true ); // hard crop mode for shortcode thumbs
		add_image_size( 'mp-intro-size', 1920, 1080, true ); // hard crop mode for model replacement images
		add_image_size( 'wp3d-gallery-size', 1920, 1920 ); // variable width gallery images with soft crop
		
		// These functions run on every post save/update
		add_action( 'save_post', array( $this, 'get_mp_apidata' ), 10, 3 );
		add_action( 'save_post', array( $this, 'get_tst_apidata' ), 10, 3 );
		add_action( 'save_post', array( $this, 'get_other_apidata' ), 10, 3 );		
		add_action( 'save_post', array( $this, 'delete_post_transient' ), 10, 3 );
		add_action( 'save_post', array( $this, 'add_social_play_button' ), 99, 3 );

		
		// Remove "View" from Agents Admin Screen
		add_filter( 'page_row_actions', array( $this, 'remove_agent_view_action'), 10, 1 );

		
		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new WP3D_Models_Admin_API();
			
			// Filter Yoast Meta Priority
			if ( get_option( 'wpseo' ) && function_exists('wpseo_init') ) {
				add_filter( 'wpseo_metabox_prio', array( $this, 'lower_wp_seo_metabox' ), 10 );
			}
			
			// quick check for Jetpack masterbar
			if( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'masterbar' ) ) {
				$jetpack_masterbar_exists = true;
			} else {
				$jetpack_masterbar_exists = false;
			}
			
			if( get_option('wp3d_tools_location') == 'side' || $jetpack_masterbar_exists ) {
				add_action( 'add_meta_boxes_model', array( $this, 'wp3d_side_tools_menu' ), 10, 2 );
				// Remove content from the Admin Bar
				add_action('admin_bar_menu', array( $this, 'admin_bar_menu_cleanup'), 1000 );
			} 

		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		
		// Add WP3D Query Vars
		add_filter( 'query_vars', array( $this, 'add_wp3d_query_vars_filter' ), 10 );
		
		// Add WP3D Endpoints
		add_action( 'init', array( $this, 'add_wp3d_endpoints' ), 0 );
		
		// oEmbed Functions
		add_action( 'init', array( $this, 'wp3d_custom_oembed_providers' ), 0 );		
		add_filter('oembed_result', array( $this, 'wp3d_wrap_oembed' ), 10, 3);
        add_action('wp_ajax_wp3d_get_permalink', [$this, 'getPostPermalink']);
        add_action('wp_ajax_wp3d_disable_notice', [$this, 'disableNotice']);
        add_action('wp_logout', [$this, 'logout']);

	} // End __construct ()

	/**
	 * Check if ACF plugin is free or pro version
	 */
	public function get_acf_version() {

		// If we can't detect ACF
		if ( ! class_exists( 'acf' )  ) {
			return;
		 }

		// Check for the function acf_get_setting - this came in version 5
		if ( function_exists( 'acf_get_setting' ) ) {
			// Get the version to be sure
			// This will return a srting of the version eg '5.0.0'
			$version = acf_get_setting( 'version' );
		} else {
			// Use the version 4 logic to get the version
			// This will return a string if the plugin is active eg '4.4.11'
			// This will retrn the string 'version' if the plugin is not active
			$version = apply_filters( 'acf/get_info', 'version' );
		}

		// Get only the major version from the version string (the first character)
		$major_version = substr( $version, 0 , 1 );
		return $major_version;

	}
	
	/**
	* Extend the default HTTP response timeout
	*/
	public function extend_http_request_timeout( $timeout ) {
		return 45; // seconds default wordpress is 5
	}
	
	/**
	* Reduce Priority of Yoast WPSEO Metabox
	*/
	public function lower_wp_seo_metabox( $priority ) {
		$priority = 'low';
		return $priority;
	}

	/**
	 * Function to hide some of the Yoast SEO columns, if they exist
	 */
	public function wp_seo_columns_filter( $columns ) {
	    //unset($columns['wpseo-score']);
	    //unset($columns['wpseo-title']);
	    unset($columns['wpseo-metadesc']);
	    unset($columns['wpseo-focuskw']);
	    return $columns;
	}

	/**
	 * Remove AGENTS "view" action
	 */
	public function remove_agent_view_action( $actions ) {
		
		//print_r($actions); exit;
		if( get_post_type() == 'wp3d_agent' ) {
        	unset( $actions['view'] );
        	unset( $actions['inline hide-if-no-js'] );
		}
	    
	    return $actions;
	}	
			

	/**
	 * Turn on the admin bar for all users who can edit posts.
	 * @param  boolean $show Existing setting passed by WordPress
	 * @return boolean	Whether to show the bar or not.
	 */
	static function show_admin_bar($show = false) {
		
		$is_skinned = get_query_var('skinned', null);
        
        // hang in here...this reads if a user is an editor (or above), and we're on a single model, and we're not on the admin pages, and we're not on a "skinned" page, then show the admin bar  
		if(current_user_can('manage_options') && 
		is_singular('model') && 
		!is_admin() && 
		!isset($_GET['skinned']) && 
		!isset($is_skinned)) {
			return true;
		}
		return $show;
	}
	
	public function generate_tools_message() {
		$message_contents = "
		<h3>Message Template</h3>
		<p><em>Your links/codes all on one page! Copy the following content to be used wherever you like:</em></p>
		<div style=\"margin-top: 10px; padding: 40px; border: 1px solid #CCCCCC;\">
			<p><b>BRANDED MODEL LINKS</b></p>
			<ul>
				<li><a href=\"".esc_url(trailingslashit(get_permalink()).'skinned/')."\" target=\"_blank\">Skinned View</a></li>
	            <li><a href=\"".esc_url(trailingslashit(get_permalink()).'skinned/#start')."\" target=\"_blank\">Skinned View (Autostart)</a></li>
	            <li><a href=\"".esc_url(trailingslashit(get_permalink()))."\" target=\"_blank\">Standard View</a></li>
	            <li><a href=\"".esc_url(trailingslashit(get_permalink()).'fullscreen/')."\" target=\"_blank\">Fullscreen View</a></li>
	        </ul>
	        <p><b>UNBRANDED MODEL LINKS</b></p>
	        <ul>
	            <li><a href=\"".esc_url(trailingslashit(get_permalink()).'nobrand/')."\" target=\"_blank\">Standard View</a></li>
            	<li><a href=\"".esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/')."\" target=\"_blank\">Fullscreen View</a></li>
	        </ul>
	        
	        <p><b>BRANDED JAVASCRIPT EMBED CODE</b></p>
			<p>&lt;div id='wp3d-".get_the_ID()."'&gt;&lt;a href='".esc_url(trailingslashit(get_permalink()).'fullscreen/?embedded')."'&gt;LOADING - ".get_the_title()."&lt;/a&gt;&lt;script src='//wp3d-models.s3.us-east-2.amazonaws.com/js/embed-iframe.js?id=wp3d-".get_the_ID()."'&gt;&lt;/script&gt;&lt;/div&gt;</p>
	
			<p><b>BRANDED IFRAME EMBED CODE</b></p>
			<p>&lt;iframe width='853' height='480' src='".esc_url(trailingslashit(get_permalink()).'fullscreen/?embedded')."' frameborder='0' allow='vr' allowfullscreen='allowfullscreen'&gt;&lt;/iframe&gt;</p>
	        
	        <p><b>UNBRANDED JAVASCRIPT EMBED CODE</b></p>
	        <p>&lt;div id='wp3d-".get_the_ID()."'&gt;&lt;a href='".esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/?embedded')."'&gt;LOADING - ".get_the_title()."&lt;/a&gt;&lt;script src='//wp3d-models.s3.us-east-2.amazonaws.com/js/embed-iframe.js?id=wp3d-".get_the_ID()."'&gt;&lt;/script&gt;&lt;/div&gt;</p>
	            
	        <p><b>UNBRANDED IFRAME EMBED CODE</b></p>    
	        <p>&lt;iframe width='853' height='480' src='".esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/?embedded')."' frameborder='0' allow='vr' allowfullscreen='allowfullscreen'&gt;&lt;/iframe&gt;</p>
        
        </div>
        ";
        return $message_contents;
	}
	
	/** 
	 * Show this simple metabox if Jetpack 'masterbar' has removed our "WP3D Models Tools" toolbar menu
	 * 
	 */
	public function wp3d_side_tools_menu($post) {
		
		// DEBUG
		// print_r(get_post_custom($post->ID)); exit;
		
		global $wp,$wp_admin_bar;
		
		$wp_admin_bar->remove_menu('comments'); // Comments are disabled for models, remove this from the admin bar 
		$wp_admin_bar->remove_menu('view'); // We're doing our own "View" links below, remove stock
		
		if ( get_field('_wp3d_model_update', $post->ID) == 'update' ) {
			add_meta_box( 
		        'wp3d-tools-box',
		        __( 'WP3D Models Tools' ),
		        array($this, 'render_wp3d_tools_box'),
		        'model',
		        'side',
		        'high'
		    ); 
		}
	}
	
	public function render_wp3d_tools_box($post) {
	$mp_id = WP3D_Models()->mp_id_from_url(get_field('model_link', $post->ID)); 
	
?>
<div id="wp3d-tools-tabs">
        <ul class="category-tabs">
            <li class="ui-state-active"><a href="#branded">Branded</a></li>
            <li><a href="#unbranded">Unbranded</a></li>
            <li><a href="#embed">Embed</a></li>
            <li><a href="#misc">Misc</a></li>
        </ul>
        <div class="wp-tab-panel" id="branded">
            <ul class="wp3d-tools-link-list">
            	<li><a href="<?php echo esc_url(trailingslashit(get_permalink()).'skinned/'); ?>" target="_blank">Skinned View <span class="dashicons dashicons-external"></span></a></li>
            	<li><a href="<?php echo esc_url(trailingslashit(get_permalink()).'skinned/#start'); ?>" target="_blank">Skinned View (Autostart)<span class="dashicons dashicons-external"></span></a></li>
            	<li><a href="<?php echo esc_url(trailingslashit(get_permalink())); ?>" target="_blank">Standard View <span class="dashicons dashicons-external"></span></a></li>
            	<li><a href="<?php echo esc_url(trailingslashit(get_permalink()).'fullscreen/'); ?>" target="_blank">Fullscreen View <span class="dashicons dashicons-external"></span></a></li>
            </ul>
        </div>
        <div class="hidden wp-tab-panel" id="unbranded">
            <ul class="wp3d-tools-link-list">
            	<li><a href="<?php echo esc_url(trailingslashit(get_permalink()).'nobrand/'); ?>" target="_blank">Standard View <span class="dashicons dashicons-external"></span></a></li>
            	<li><a href="<?php echo esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/'); ?>" target="_blank">Fullscreen View <span class="dashicons dashicons-external"></span></a></li>
            </ul>

        </div>
        <div class="hidden wp-tab-panel" id="embed">
			<ul class="wp3d-tools-link-list">
            	<li><a href="#" onClick="prompt('BRANDED JAVASCRIPT EMBED CODE:', '<div id=\'wp3d-<?php echo get_the_ID(); ?>\'><a href=\'<?php echo esc_url(trailingslashit(get_permalink()).'fullscreen/?embedded'); ?>\'>LOADING - <?php echo get_the_title(); ?></a><script src=\'//wp3d-models.s3.us-east-2.amazonaws.com/js/embed-iframe.js?id=wp3d-<?php echo get_the_ID(); ?>\'></script></div>'); return false;">Standard Fullscreen (JS)</a></li>
            	<li><a href="#" onClick="prompt('BRANDED IFRAME EMBED CODE:', '<iframe width=\'853\' height=\'480\' src=\'<?php echo esc_url(trailingslashit(get_permalink()).'fullscreen/?embedded'); ?>\' frameborder=\'0\' allow=\'vr\' allowfullscreen=\'allowfullscreen\'></iframe>'); return false;">Standard Fullscreen (IFRAME)</a></li>
            	<li><a href="#" onClick="prompt('UNBRANDED JAVASCRIPT EMBED CODE:', '<div id=\'wp3d-<?php echo get_the_ID(); ?>\'><a href=\'<?php echo esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/?embedded'); ?>\'>LOADING - <?php echo get_the_title(); ?></a><script src=\'//wp3d-models.s3.us-east-2.amazonaws.com/js/embed-iframe.js?id=wp3d-<?php echo get_the_ID(); ?>\'></script></div>'); return false;">Unbranded Fullscreen (JS)</a></li>
            	<li><a href="#" onClick="prompt('UNBRANDED IFRAME EMBED CODE:', '<iframe width=\'853\' height=\'480\' src=\'<?php echo esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/?embedded'); ?>\' frameborder=\'0\' allow=\'vr\' allowfullscreen=\'allowfullscreen\'></iframe>'); return false;">Unbranded Fullscreen (IFRAME)</a></li>
            </ul>
        </div>
        <div class="hidden wp-tab-panel" id="misc">
            <ul class="wp3d-tools-link-list">
            	<li><a href="#" onClick="prompt('MODEL SHORTCODE:', '[wp3d-model id=\'<?php echo get_the_ID(); ?>\']'); return false;">Single Shortcode</a></li>
            	<?php if (isset($mp_id) && $mp_id != 'error') {  // if we've got a valid ID, add the ZIP download option ?>
            	<li><a href="<?php echo esc_url(plugins_url().'/wp3d-models-free/includes/wp3d-models-get-zip.php?mid='
                        .$mp_id); ?>" target="_blank" onClick="confirm('Generate ZIP download from Matterport?');">Download ZIP from Matterport</a>
            	<?php } ?>
            	<li><a href="https://developers.facebook.com/tools/debug/sharing/?q=<?php the_permalink(); ?>" target="_blank">Facebook Debugger <span class="dashicons dashicons-external"></span></a></li>
            	<li><a href="#TB_inline?width=600&height=550&inlineId=wp3d-tools-message" class="thickbox">Message Template</a>
					<div id="wp3d-tools-message" style="display:none;">
					    <?php echo WP3D_Models()->generate_tools_message(); ?>
					</div>            		
            	</li>
            </ul>
        </div>
    </div>
<?php
	}	
	
	/** 
	 * Show a NAG notice if a single model has an overriding URL added for the "VIEW" button 
	 * 
	 */
	static function single_model_view_override_notice() {
		$screen = get_current_screen(); 
		if ('model' == $screen->post_type && 'post' == $screen->base) { // gotta be on the 'model' post type AND the single 'post' (EDIT) screen
			if (function_exists('get_field')) { // check for ACF
				if (get_field('view_link_override') && get_field(default_view_link) == 'custom') { // also gotta have something entered into the overriding field
					$nag_class = "update-nag";
					$nag_message = __('FYI : This model has an overriding URL for the "VIEW" Button.', 'wp3d-models');
		        	echo '<div class="'.esc_attr($nag_class).'"> '.esc_html($nag_message).'</div>';
				}
			} // end ACF check
		}
	}
	
	/** 
	 * Show a NAG notice if a single model is excluded from LIST views 
	 * 
	 */
	static function single_model_exclude_notice() {
		$screen = get_current_screen(); 
		if ('model' == $screen->post_type && 'post' == $screen->base) { // gotta be on the 'model' post type AND the single 'post' (EDIT) screen
			if (function_exists('get_field')) { // check for ACF
				if (get_field('model_list_exclude')) { // also gotta have something entered into the overriding field
					$nag_class = "update-nag";
					$nag_message = __('FYI : This model is currently excluded from ALL LIST VIEWS.', 'wp3d-models');
		        	echo '<div class="'.esc_attr($nag_class).'"> '.esc_html($nag_message).'</div>';
				}
			} // end ACF check
		}
	}

    /**
     * Move the default WP "Content Editor" to live inside the INFO Tab on the Model Input screen
     * Also deal with some serious nonsense re: re-drawing TMCE when switching between tabs
     *
     */
    static function acf_content_to_info()
    {
        // check if this is a registration page
        if ($GLOBALS['pagenow'] != 'wp-login.php') {
            $screen = get_current_screen();
            if ('model' == $screen->post_type && 'post' == $screen->base) { // gotta be on the 'model' post type AND the single 'post' (EDIT) screen ?>
                <script type="text/javascript">
                    (function ($) {
                        var contentwrapwidth = '';
                        $(document).ready(function () {
                <?php if (class_exists('acf_pro')) { //do pro ACF js stuff
                    // CHECK THE PRO VERSION OF ACF 
                    if (function_exists('acf_get_setting')) {
                        $this_acf_version = acf_get_setting('version');
                        if (version_compare($this_acf_version, '5.3.5', '<=')) { ?>
                                // This is for ACF PRO 5.3.5 and lower
                                console.log('ACF PRO is less than 5.3.5');
    
                                $("#postdivrich").wrap("<div id='wp3d-content-wrap' style='display:none;'></div>");
    
                                var wysiwygBlock = $(".acf-field-wysiwyg[data-name='model_content']");
                                var wysiwygLabel = wysiwygBlock.children("div.acf-label");
                                wysiwygBlock.html($('#postdivrich'));
                                wysiwygBlock.prepend(wysiwygLabel);
    
                                $("#wp3d-content-wrap").remove();
    
                        <?php } elseif ( version_compare($this_acf_version, '5.3.6.1', '>=') && version_compare($this_acf_version, '5.7.0', '<') ) { ?>
                                // This is for ACF PRO 5.3.6.1 & greater, but less than 5.7.0.
                                console.log('ACF PRO is between 5.3.6.1 and 5.7.0');
    
                                $("#postdivrich").wrap("<div id='wp3d-content-wrap' style='display:none;'></div>");
    
                                var wysiwygOriginal = $('#postdivrich');
                                var wysiwygBlock = $(".acf-field-wysiwyg[data-name='model_content']");
                                var wysiwygLabel = wysiwygBlock.children("div.acf-label");
    
                                wysiwygBlock.children("div.acf-input").hide();
                                wysiwygLabel.after(wysiwygOriginal);
    
                                $("#wp3d-content-wrap").remove();
    
                        <?php } elseif ( version_compare($this_acf_version, '5.7.0', '>=') ) { ?>
                                // This is for ACF PRO greater than 5.7.0.
                                console.log("ACF PRO 5.7.0 & greater");
    
                                $('.acf-field-wysiwyg[data-name="model_content"] .acf-input div').css("display", "none");
                                $('.acf-field-wysiwyg[data-name="model_content"] .acf-input').append($('#postdivrich'));
    
                        <?php } // end pro version compare
                    } // end check for acf_get_setting 
                } else { // end acf_pro check, do regular ACF free stuff 
                    // ACF FREE VERSION (NOT PRO)
                    // 
                    if (function_exists('acf_get_setting')) { // this function only exists in versions greater than 5.6.2 (similar to PRO)
                        $this_acf_version = acf_get_setting('version'); ?>
                            console.log('ACF (not PRO) Version <?php echo $this_acf_version; ?>');
                        <?php if (version_compare($this_acf_version, '5.6.2', '>=')) { ?>
                            console.log("ACF FREE 5.6.2 & greater");
                            $('.acf-field-wysiwyg[data-name="model_content"] .acf-input > div').css("display","none");
                            if ($('.acf-field-wysiwyg[data-name="model_content"]').length) {
                                $('.acf-field-wysiwyg[data-name="model_content"]').after('<div id="model-content-wrapper" class="acf-field acf-field- acf-field-bb773a38c8228 field_type- field_key-field_bb773a38c8228" data-key="acf-field acf-field- acf-field-bb773a38c8228 field_type- field_key-field_bb773a38c8228" style="margin-top: -45px"></div>');
                                $('#model-content-wrapper').append($('#postdivrich'));    
                            }
                        <?php
                        } // end version compare check															  
                    } else { // end if 'acf_get_setting exists, means this is an older "FREE" Version (less than or equal to v.4.4.12) ?>
                            $("#postdivrich").wrap("<div id='wp3d-content-wrap' style='display:none;'></div>");

                            var wysiwygBlock = $('#acf-model_content');
                            var wysiwygLabel = wysiwygBlock.children("p.label");
                            wysiwygBlock.html($('#postdivrich'));
                            wysiwygBlock.prepend(wysiwygLabel);

                            $("#wp3d-content-wrap").remove();

                    <?php 
                    } // end ACF Free (4.4.12 or older) Version Checking
                } // end "NOT PRO" ACF Functions
                ?>
                        });

                        function resizewysiwyg($field) {
                            contentwrapwidth = $('#wp-content-wrap').width();
                            //console.log (contentwrapwidth);
                            $field.find('#wp-content-wrap').css('padding-top', 55);
                            $field.find('.mce-edit-area').css('padding-top', 67);
                            $field.find('#wp-content-editor-tools').width(contentwrapwidth);
                            $field.find('.mce-toolbar-grp').width(contentwrapwidth - 2);
                            $field.find('#ed_toolbar').width(contentwrapwidth - 40);
                        }

                <?php if (class_exists('acf_pro')) { //do pro ACF js stuff ?>
                        // ACF5
                        acf.add_action('show_field', function ($field, context) {

                            // validate
                            if ($field.attr('data-type') == 'wysiwyg' && $field.attr('data-name') == 'model_content') {
                                resizewysiwyg($field);
                            }

                        });
                <?php } else {    // ACF FREE
                        if (function_exists('acf_get_setting')) {
                            $this_acf_version = acf_get_setting('version');
                        }
                        if (version_compare($this_acf_version, '5.6.2', '>=')) { ?>

                        // ACF FREE 5.6.2 or greater (uses ACF "Pro" filters)
                        acf.add_action('show_field', function ($field, context) {

                            // validate
                            if ($field.attr('data-type') == 'wysiwyg' && $field.attr('data-name') == 'model_content') {
                                resizewysiwyg($field);
                            }

                        });

                <?php } else { ?>
                        $(document).on('acf/fields/tab/show acf/conditional_logic/show', function (e, $field) {
                            // This may need some attention in the future....all of this below just resizes the (moved) WYSIWYG whenever its tab comes into focus
                            //console.log($field);
                            // validate
                            if ($field.attr('data-field_type') == 'wysiwyg') {
                                resizewysiwyg($field);
                            }

                        });
                <?php
                } // end ACF FREE 5.6.2 or greater check
            } // end ACF FREE check 
            ?>
                    })(jQuery);
                </script>
                <?php
            } // end check for single 'model' screen
        } // end login screen check
    }


	/**
	 * Add New Model Columns
	 *	 
	 */
static function model_columns_order($columns) {

  $new = array();
  foreach($columns as $key => $title) {
    // Put the NEW columns before the Author column
    if ($key=='taxonomy-model-type') { 
      	$new['wp3d-thumbnail'] = __( 'Image', 'wp3d-models');
      	$new['wp3d-exclude'] =  __( 'Exclude', 'wp3d-models');
      }
    $new[$key] = $title;
  }
  return $new;

}

	/**
	 * Customize the content of the new columns
	 *	 
	 */
static function model_custom_columns($column)
{
	global $post;
	if($column == 'wp3d-thumbnail')
	{
		echo wp_get_attachment_image( get_post_thumbnail_id(), array(200,50) );
	}
	elseif($column == 'wp3d-exclude')
	{
		if (function_exists('get_field') && get_field('model_list_exclude'))
		{
			echo '<span class="dashicons dashicons-hidden"></span>';
		}
		else
		{
			echo '';
		}
	}
}


	/**
	 * Remove default "Model" link from the toolbar
	 *	 
	 * @global $wp_admin_bar_cleanup
	 * @global $wp
	 * @since 3.0
	 */
	static function admin_bar_menu_cleanup() {
		global $wp,$wp_admin_bar;

		if(!current_user_can('manage_options')) { return; } // need to be an admin (of sorts) 
		
		if(is_admin()) { // if we're on the admin side of things, get the screen so we can test later 
		$screen = get_current_screen(); 
			if ('model' == $screen->post_type && 'post' == $screen->base && isset($_GET['post'])) { // gotta be on the 'model' post type AND the single 'post' (EDIT) screen
				$current_post = $_GET['post'];
				$wp_admin_bar->remove_menu('comments'); // Comments are disabled for models, remove this from the admin bar 
				$wp_admin_bar->remove_menu('view'); // We're doing our own "View" links below, remove stock

			} 
		} 
		
	}



	/**
	 * Add the WP3D menu to the WP Admin Bar
	 *	 
	 * @global $wp_admin_bar
	 * @global $wp
	 * @since 2.0.48
	 */
	static function admin_bar_menu() {
		global $wp,$wp_admin_bar;

		if(!current_user_can('manage_options')) { return; } // need to be an admin (of sorts) 
		
		if(is_admin()) { // if we're on the admin side of things, get the screen so we can test later 
		$screen = get_current_screen(); 
			if ('model' == $screen->post_type && 'post' == $screen->base && isset($_GET['post'])) { // gotta be on the 'model' post type AND the single 'post' (EDIT) screen
				$current_post = $_GET['post'];
				$admin_show_model_links = true;
			} else {
				$admin_show_model_links = false;
			}
		} else { // not admin, but we still need to test to see if we should show the admin model links, on model single views
			if(is_singular('model')) { 
				$admin_show_model_links = true;
			} else {
				$admin_show_model_links = false;
			}
		}

		if( $admin_show_model_links ) { // are we showing the links?
		
			$current_post = get_the_ID();
			if (function_exists('get_field')) { // checking for ACF function 'get_field'
		
				// go get the model link
				$mp_image_url = trim(get_field('model_link', $current_post));
				$mp_id = WP3D_Models()->mp_id_from_url($mp_image_url); 
			
			}
			
			$wp_admin_bar->remove_menu('comments'); // Comments are disabled for models, remove this from the admin bar 
			$wp_admin_bar->remove_menu('view'); // We're doing our own "View" links below, remove stock

			$wp_admin_bar->add_node(
				array(
					'id' => 'wp3d-models',
					'title' => '<span class="ab-icon"></span><span class="ab-label">'.__( 'WP3D Models Tools', 'wp3d-models').'</span>',
					'href' => ''
					)
				);
				
			$new_window = array (
				'target' => '_blank'
				);	
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models',
					'id' => 'wp3d-models-branded-menu',
					'title' => __( 'Branded Views', 'wp3d-models'),
					'href' => ''
				));
			
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-branded-menu',
					'id' => 'wp3d-models-skinned',
					'title' => __( 'Skinned View <span class="dashicons dashicons-external"></span>', 'wp3d-models'),
					'href' => esc_url(trailingslashit(get_permalink()).'skinned/'),
					'meta' => $new_window
				));	
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-branded-menu',
					'id' => 'wp3d-models-skinned-autostart',
					'title' => __( 'Skinned View (Autostart) <span class="dashicons dashicons-external"></span>', 'wp3d-models'),
					'href' => esc_url(trailingslashit(get_permalink()).'skinned/#start'),
					'meta' => $new_window
				));					
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-branded-menu',
					'id' => 'wp3d-models-standard',
					'title' => __( 'Standard View <span class="dashicons dashicons-external"></span>', 'wp3d-models'),
					'href' => esc_url(get_permalink()),
					'meta' => $new_window
				));
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-branded-menu',
					'id' => 'wp3d-models-fullscreen',
					'title' => __( 'Fullscreen View <span class="dashicons dashicons-external"></span>', 'wp3d-models'),
					'href' => esc_url(trailingslashit(get_permalink()).'fullscreen/'),
					'meta' => $new_window
				));	
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models',
					'id' => 'wp3d-models-nobrand-menu',
					'title' => __( 'Unbranded Views', 'wp3d-models'),
					'href' => ''
				));
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-nobrand-menu',
					'id' => 'wp3d-models-nobrand',
					'title' => __( 'Unbranded Standard View <span class="dashicons dashicons-external"></span>', 'wp3d-models'),
					'href' => esc_url(trailingslashit(get_permalink()).'nobrand/'),
					'meta' => $new_window
				));
								
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-nobrand-menu',
					'id' => 'wp3d-models-fullscreen-nobrand',
					'title' => __( 'Unbranded Fullscreen View <span class="dashicons dashicons-external"></span>', 'wp3d-models'),
					'href' => esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/'),
					'meta' => $new_window
				));	
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models',
					'id' => 'wp3d-models-embed-menu',
					'title' => __( 'Embed Codes', 'wp3d-models'),
					'href' => ''
				));				
				
			$embed_meta = array (
				'onclick' => 'promptJSShow("MODEL JAVASCRIPT EMBED CODE:","'.get_the_ID().'","'.esc_url(trailingslashit(get_permalink()).'fullscreen/?embedded').'","'.get_the_title().'"); return false;'
				);				

			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-embed-menu',
					'id' => 'wp3d-models-standard-embed',
					'title' => __( 'Standard Fullscreen Embed (JS)', 'wp3d-models'),
					'href' => esc_url('#'),
					'meta' => $embed_meta
				));	
				
			$embed_meta_iframe = array (
				'onclick' => 'promptIFShow("MODEL IFRAME EMBED CODE:","'.esc_url(trailingslashit(get_permalink()).'fullscreen/?embedded').'"); return false;'
			);

			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-embed-menu',
					'id' => 'wp3d-models-standard-iframe-embed',
					'title' => __( 'Standard Fullscreen Embed (IFRAME)', 'wp3d-models'),
					'href' => esc_url('#'),
					'meta' => $embed_meta_iframe
				));					
				
			$embed_meta_nobrand = array (
				'onclick' => 'promptJSShow("UNBRANDED MODEL JAVASCRIPT EMBED CODE:","'.get_the_ID().'","'.esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/?embedded').'","'.get_the_title().'"); return false;'
			);				

			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-embed-menu',
					'id' => 'wp3d-models-nobrand-embed',
					'title' => __( 'Unbranded Fullscreen Embed (JS)', 'wp3d-models'),
					'href' => esc_url('#'),
					'meta' => $embed_meta_nobrand
				));	
				
			$embed_meta_nobrand_iframe = array (
				'onclick' => 'promptIFShow("UNBRANDED MODEL IFRAME EMBED CODE:","'.esc_url(trailingslashit(get_permalink()).'fullscreen-nobrand/?embedded').'"); return false;'
			);			
			
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-embed-menu',
					'id' => 'wp3d-models-nobrand-iframe-embed',
					'title' => __( 'Unbranded Fullscreen Embed (IFRAME)', 'wp3d-models'),
					'href' => esc_url('#'),
					'meta' => $embed_meta_nobrand_iframe
				));	
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models',
					'id' => 'wp3d-models-shortcode-menu',
					'title' => __( 'Shortcodes', 'wp3d-models'),
					'href' => ''
				));					

			$single_shortcode_meta = array (
				'onclick' => 'promptSCShow("MODEL SHORTCODE:","'.get_the_ID().'"); return false;'
				);	
				
			$wp_admin_bar->add_node(
				array(
					'parent' => 'wp3d-models-shortcode-menu',
					'id' => 'wp3d-models-single-shortcode',
					'title' => __( 'Single Shortcode', 'wp3d-models'),
					'href' => esc_url('#'),
					'meta' => $single_shortcode_meta
				)
            );
		}
	}

	/**
	 * Wrapper function to act as a catch all for non MP or TST Model Base Types
	 * This function runs on every save of every Model
	 * 
	 * @param  string $post_id    		Post ID
	 * @return varies		    		function may simply return, return 'mp-error', or save generated contents to a custom field
	 *
	 * 
	 */
	public function get_other_apidata($post_id, $post, $update) {

		    // If this isn't a 'model' post or ACF is not installed, beat it.
		    if ( 'model' != $post->post_type || !function_exists('get_field') ) { return; }
		
			// get the POST ID, if it wasn't passed in
			if (!isset($post_id)) {
				$post_id = $post->ID;
			}
			
			// Bounce if it is a Matterport or TST Model Type
			if ( get_field('wp3d_model_type') == 'matterport' || get_field('wp3d_model_type') == 'threesixtytours' ) { return; }
			
			// Okay, we know we're on a model page, with ACF installed, and we've got the POST ID, and it isn't a Matterport or TST Tour
			// First, we need to know if this is a new Model save.  If the "_wp3d_model_update" field has a value of 'update', its a re-run, may not need to get API data
			if ( get_field('_wp3d_model_update') == 'update' ) { 
				
				// if we're updating, just return (largely a placeholder function here...for future reference.)
				return;
				
			// no update field, either a NEW (or legacy) Model
			} else { 
				
				if (isset($post->post_status) && 'auto-draft' == $post->post_status) {
					// she's new!
					update_post_meta($post_id, '_wp3d_model_update', 'new');
				} else {
					update_post_meta($post_id, '_wp3d_model_update', 'update');
					return;
				}
			}
			
		//DEBUGGIN
		//return $mp_api_data;
		
	} // end get_other_apidata





	/**
	 * Wrapper function to get model image, data & address info provided via the TST API
	 * This function runs on every save of every Model
	 * 
	 * @param  string $post_id    		Post ID
	 * @param  string $tst_id			Matterport Model ID
	 * @param  boolean $just_image		If we only need the image, set to true
	 * @return varies		    		function may simply return, return 'mp-error', or save generated contents to a custom field
	 *
	 * 
	 */
	public function get_tst_apidata($post_id, $post, $update) {

		    // If this isn't a 'model' post or ACF is not installed, beat it.
		    if ( 'model' != $post->post_type || !function_exists('get_field') ) { return; }
		
			// get the POST ID, if it wasn't passed in
			if (!isset($post_id)) {
				$post_id = $post->ID;
			}
			
			// Bounce if it isn't a Matterport Model Type, everything in here relates to Matterport
			if ( get_field('wp3d_model_type') != 'threesixtytours') { return; }
			
			// Okay, we know we're on a model page, with ACF installed, and we've got the POST ID, lets make sure the rest of this function actually needs to run
			// First, we need to know if this is a new Model save.  If the "_wp3d_model_update" field has a value of 'update', its a re-run, may not need to get API data
			if ( get_field('_wp3d_model_update') == 'update' ) { 
				
				// By default, updated Models don't need to re-retrieve the API data
				$retrieve_tst_data = false; 
				
				// However, if the (force) "retreive" value is checked, we  do need to go get it (the data)
				if ( get_field('retrieve_tst_data') ) { $retrieve_tst_data = true; } 
			
			// no update field, either a NEW (or legacy) Model
			} else { 
				
				$retrieve_tst_data = true; 
				
			}
			
		    // if we already have an image AND no flag was set to retrieve or re-retrieve, beat it!
		    if (has_post_thumbnail($post_id) && $retrieve_tst_data == false) { 
		    	return;
		    }
			
			// Pressing on, looks like we need to go get the TST ID, if it wasn't passed in
			$tst_id = WP3D_Models()->tst_id_from_url(get_field('tst_link')); 	

			// DEBUG
			//print_r($tst_id); exit;
			
			// If the function above returned an error, it along
			if ('error' == $tst_id['id']) { // Check for error
				return 'tst-error';
			}
		
			// if we've got everything we need, get some JSON & PRESS ON
			if(isset($tst_id) && isset($post_id)) {
				// Get MODEL JSON
				if ($tst_id['type'] == 'tour') {
					$url = esc_url('https://my.threesixty.tours/app/controllers/tours/get_one/'.$tst_id['id']);
				} elseif ($tst_id['type'] == 'pano') {
					$url = esc_url('https://my.threesixty.tours/app/controllers/photos/get_one/'.$tst_id['id']);
				}
				
				$content = wp_remote_get($url);
				
				// https://wordpress.org/support/topic/fatal-error-cannot-use-object-of-type-wp_error-as-array-4
				// SLIGHTLY BETTER ERROR NOTE
				
				try { // no error, return json data
					
					$json = json_decode($content['body'], true);
					
					// DEBUG
					//print_r($json); exit;
					
					// ************* CHECK FOR EXISTING IMAGE HERE **************** //
					if (!has_post_thumbnail($post_id)) { // no post thumbnail, lets get the image from TST
					
						if ($tst_id['type'] == 'tour') {
							$tst_img_id = $json['data']['resource']['0']['id'];
						} elseif ($tst_id['type'] == 'pano') {
							$tst_img_id = $json['data']['id'];
						}
						
						$tst_src = 'https://my.threesixty.tours/app/images/photos/preview/'.$tst_img_id.'.jpg';
						
						// DEBUG
						//echo $tst_src; exit;

						if (!empty($tst_src)) { // the image source is not empty
								$result = media_sideload_image($tst_src, $post_id, 'tst_image_from_url'); // load the tst image
							} else { // otherwise get an error image
								$result = media_sideload_image('https://placehold.it/400x230/ccc.gif&text=No%20TST%20Image', $post_id, 'tst_image_from_url'); // load an error image
							}
						// Now we have the $result 	
						if (!is_wp_error($result)){
							$attachments = get_posts(array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC'));
							if(sizeof($attachments) > 0){
								// set image as the post thumbnail
								set_post_thumbnail($post_id, $attachments[0]->ID);
							} 
						} else {
							$error_string = $result->get_error_message();
		   					echo '<div id="message" class="error"><p>' . $error_string . '</p></div>'; exit; // Quick/Dirty - could be done better.
						} 
												
					}
					
					// IF THE RE-RETRIEVE DATA FLAG WAS SET, LETS RUN THROUGH THE REST OF THIS				
					if ($retrieve_tst_data) {									
										
						// GETTING TEXT CONTENT VALUES FROM THE API
						if (isset($json['data']['description']) && $json['data']['description'] != '' ) { $tst_api_data['summary'] = $json['data']['description'] ; } // Description
						if (isset($json['data']['created_at']) && $json['data']['created_at'] != '' ) { $tst_api_data['created'] = $json['data']['created_at'] ; } // Description
						// Image ID
						if ($tst_id['type'] == 'tour') {
							$tst_img_id = $json['data']['resource']['0']['id'];
						} elseif ($tst_id['type'] == 'pano') {
							$tst_img_id = $json['data']['id'];
						}						
						if (isset($tst_img_id) && $tst_img_id != '') { $tst_api_data['resource_img_id'] = $tst_img_id ; }
						
						// IF WE GOT THIS FAR, SAVE THE RESULTS TO OUR CUSTOM FIELD
						
						//DEBUG
						// print_r($tst_api_data); exit;
						
		 				// STORE THE NEWLY MINTED ARRAY IN A (HIDDEN) CUSTOM FIELD
		 				update_post_meta($post_id, '_tst_api_data', $tst_api_data); 
		 				
		 				// ALSO, UPDATE THE "RETRIEVE" CUSTOM FIELD TO BE UNCHECKED
		 				update_post_meta($post_id, 'retrieve_tst_data', false);
		 				
		 				// AlSO, UPDATE THE HIDDEN MODEL FIELD SO WE KNOW THAT THE MODEL IS NOT NEW
		 				update_post_meta($post_id, '_wp3d_model_update', 'update');
					}

				} catch ( Exception $ex ) {
					
					$json = null;
					return false;
					
				} // end try/catch
				
			} // end check for $tst_id & $post_id
		 
		//DEBUGGIN
		//return $mp_api_data;
		
	} // end get_tst_apidata		









	/**
	 * Wrapper function to get model image, data & address info provided via the Matterport API
	 * This function runs on every save of every Model
	 * 
	 * @param  string $mp_id    		Post ID
	 * @param  string $mp_id			Matterport Model ID
	 * @param  boolean $just_image		If we only need the image, set to true
	 * @return varies		    		function may simply return, return 'mp-error', or save generated contents to a custom field
	 *
	 * 
	 */
	public function get_mp_apidata($post_id, $post, $update) {

		    // If this isn't a 'model' post or ACF is not installed, beat it.
		    if ( 'model' != $post->post_type || !function_exists('get_field') ) { return; }
		
			// get the POST ID, if it wasn't passed in
			if (!isset($post_id)) {
				$post_id = $post->ID;
			}
			
			// Bounce if it isn't a Matterport Model Type, everything in here relates to Matterport
			if ( get_field('wp3d_model_type') != 'matterport') { return; }
			
			// Okay, we know we're on a model page, with ACF installed, and we've got the POST ID, lets make sure the rest of this function actually needs to run
			// First, we need to know if this is a new Model save.  If the "_wp3d_model_update" field has a value of 'update', its a re-run, may not need to get API data
			if ( get_field('_wp3d_model_update') == 'update' ) { 
				
				// By default, updated Models don't need to re-retrieve the API data
				$retrieve_mp_data = false; 
				
				// However, if the (force) "retreive" value is checked, we  do need to go get it (the data)
				if ( get_field('retrieve_mp_data') ) { $retrieve_mp_data = true; } 
			
			// no update field, either a NEW (or legacy) Model
			} else { 
				
				$retrieve_mp_data = true; 
				
			}
			
		    // if we already have an image AND no flag was set to retrieve or re-retrieve, beat it!
		    if (has_post_thumbnail($post_id) && $retrieve_mp_data == false) { 
		    	return;
		    }
			
			// Pressing on, looks like we need to go get the Matterport ID, if it wasn't passed in
			$mp_id = WP3D_Models()->mp_id_from_url(get_field('model_link')); 	

			
			// If the function above returned an error, it along
			if ('error' == $mp_id) { // Check for error
				return 'mp-error';
			}
		
			// if we've got everything we need, get some JSON & PRESS ON
			if(isset($mp_id) && isset($post_id)) {
				// Get MODEL JSON
				$url = esc_url('https://my.matterport.com/api/player/models/'.$mp_id.'?format=json');
				$content = wp_remote_get($url);
				
				// https://wordpress.org/support/topic/fatal-error-cannot-use-object-of-type-wp_error-as-array-4
				// SLIGHTLY BETTER ERROR NOTE
				
				try { // no error, return json data
					
					$json = json_decode($content['body'], true);
					
					// ************* CHECK FOR EXISTING IMAGE HERE **************** //
					if (!has_post_thumbnail($post_id)) { // no post thumbnail, lets get the image from MP
					
						$mp_src = $json['image'].'&width=1920';	

						if (!empty($mp_src)) { // the image source is not empty
								$result = media_sideload_image($mp_src, $post_id, 'mp_image_from_url'); // load the matterport image
							} else { // otherwise get an error image
								$result = media_sideload_image('https://placehold.it/400x230/ccc.gif&text=No%20Matterport%20Image', $post_id, 'mp_image_from_url'); // load an error image
							}
						// Now we have the $result 	
						if (!is_wp_error($result)){
							$attachments = get_posts(array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC'));
							if(sizeof($attachments) > 0){
								// set image as the post thumbnail
								set_post_thumbnail($post_id, $attachments[0]->ID);
							} 
						} else {
							$error_string = $result->get_error_message();
		   					echo '<div id="message" class="error"><p>' . $error_string . '</p></div>'; exit; // Quick/Dirty - could be done better.
						} 
												
					}
					
					// IF THE RE-RETRIEVE DATA FLAG WAS SET, LETS RUN THROUGH THE REST OF THIS				
					if ($retrieve_mp_data) {									
										
						// GETTING TEXT CONTENT VALUES FROM THE API
						if (isset($json['sid']) && $json['sid'] != '' ) { $mp_api_data['sid'] = $json['sid']; } // MP ID 
						if (isset($json['name']) && $json['name'] != '' ) { $mp_api_data['name'] = $json['name']; } // MP NAME 
						if (isset($json['created']) && $json['created'] != '' ) { $mp_api_data['created'] = $json['created']; } // CREATED DATE
						if (isset($json['summary']) && $json['summary'] != '' ) { $mp_api_data['summary'] = $json['summary']; } // SUMMARY TEXT
						if (isset($json['presented_by']) && $json['presented_by'] != '') { $mp_api_data['presented_by'] = $json['presented_by']; } // PRESENTED BY 
						if (isset($json['contact_name']) && $json['contact_name'] != '') { $mp_api_data['contact_name'] = $json['contact_name']; } // CONTACT NAME 
						if (isset($json['contact_phone']) && $json['contact_phone'] != '') { $mp_api_data['contact_phone'] = $json['contact_phone']; } // CONTACT PHONE
						if (isset($json['formatted_contact_phone']) && $json['formatted_contact_phone'] != '') { $mp_api_data['formatted_contact_phone'] = $json['formatted_contact_phone']; } // FORMATTED CONTACT PHONE
						if (isset($json['contact_email']) && $json['contact_email'] != '') { $mp_api_data['contact_email'] = $json['contact_email']; } // CONTACT EMAIL
						if (isset($json['external_url']) && $json['external_url'] != '') { $mp_api_data['external_url'] = $json['external_url']; } // EXTERNAL URL
						if (isset($json['player_options']) && $json['player_options'] != '' ) { $mp_api_data['player_options'] = $json['player_options']; } // PLAYER OPTIONS 
						if (isset($json['is_vr']) && $json['is_vr'] != '' ) { $mp_api_data['is_vr'] = $json['is_vr']; } // HAS VR 
						if (isset($json['vr_url']) && $json['vr_url'] != '' ) { $mp_api_data['vr_url'] = $json['vr_url']; } // VR URL 
						if (isset($json['external_url']) && $json['external_url'] != '' ) { $mp_api_data['external_url'] = $json['external_url']; } // EXTERNAL URL?? 

						// THIS IS A MESS, BUT WE'VE GOTTA FILTER THROUGH THE ADDRESS DATA THAT GETS RETURNED
						if ($json['address'] == '{}') { $has_address = false; } 
						elseif ($json['address']['city'] == '') { $has_address = false; }
						else { $has_address = true; } 
					
						// EVEN THOUGH THIS IS TRUE, IT DOESN'T MEAN WE'VE GOT VALID DATA...JUST MEANS WE NEED TO DIG DEEPER
						if ($has_address) { // if this flag is set && there is address data - run the whole address cleanup & geocode process
						
							// ADDRESS SPECIFIC REFORMATTING CLEANUP
							$a = $json['address'];
							
							if (is_array($a)) { // new treatment for existing sub array
							
							// DEBUG
							//print_r($a);
							
								// BUIDING THE NEW '$mp_api_data' ARRAY
								foreach ($a as $key => $value) {
								    $mp_api_data[trim($key)] = trim($value);
								} 
								
							} else { // need to push to an array
						
								$cut_it_out = array("{", "}", "\"");
								$a = str_replace($cut_it_out, "", $a);
								$a = explode(',', $a);
								
								// BUIDING THE NEW '$mp_api_data' ARRAY
								foreach ($a as $result) {
								    $b = explode(':', $result);
								    if (trim($b[1]) != '') { // checking for empty values
								    	$mp_api_data[trim($b[0])] = trim($b[1]);
								    }
								} 
							}
							
							// SETTING SOME DEFAULTS
							$mp_address_street = '';
							$mp_address_city = '';
							$mp_address_state = '';
							$mp_address_zip = '';
							
							// CHECKING TO SEE WHAT HAS BEEN SET, THEN BUILDING A GEOCODE URL
							if (isset($mp_api_data['address_1']) && $mp_api_data['address_1'] != '' ) { $mp_address_street = $mp_api_data['address_1']; }
							if (isset($mp_api_data['address_2']) && $mp_api_data['address_2'] != '' ) { $mp_address_street .= ",".$mp_api_data['address_2']; }
						 	if (isset($mp_api_data['city']) && $mp_api_data['city'] != '') { $mp_address_city = $mp_api_data['city']; }
						 	if (isset($mp_api_data['state']) && $mp_api_data['state'] != '') { $mp_address_state = $mp_api_data['state']; }
						 	if (isset($mp_api_data['zip']) && $mp_api_data['zip'] != '') { $mp_address_zip = $mp_api_data['zip']; }
						 	
						 	//print_r($mp_api_data); exit;
	
						 	if ($mp_address_street != '' && $mp_address_city != '' && $mp_address_state != '') {  // at a minimum, we need an address, city & state to geocode
						    //echo $mp_address_street; exit;
						 	
								$geocodeurl = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($mp_address_street).",".urlencode($mp_address_city)."+".urlencode($mp_address_state)."+".urlencode($mp_address_zip)."&sensor=true&key=".strip_tags(get_option('wp3d_google_maps_api_server_key'));    
								
								$geo_content = wp_remote_get($geocodeurl);
								
								$json_geo = json_decode($geo_content['body'], true); //json decoder
							
								if (isset($json_geo['results'][0]['formatted_address']) ) {
									$mp_api_data['address'] = $json_geo['results'][0]['formatted_address']; // adding this so that we've got consistent array values
								} 
								
								if (isset($json_geo['results'][0]['geometry']['location']['lat']) ) {
									$mp_api_data['lat'] = $json_geo['results'][0]['geometry']['location']['lat']; // get lat for json
								} 
								
								if (isset($json_geo['results'][0]['geometry']['location']['lng']) ) {
									$mp_api_data['lng'] = $json_geo['results'][0]['geometry']['location']['lng']; // get lng for json
								} 
						 	}
						}
						
						// IF WE GOT THIS FAR, SAVE THE RESULTS TO OUR CUSTOM FIELD
						
						//DEBUGGIN
						//print_r($mp_api_data); exit;
						
		 				// STORE THE NEWLY MINTED ARRAY IN A (HIDDEN) CUSTOM FIELD
		 				update_post_meta($post_id, '_matterport_api_data', $mp_api_data); 
		 				
		 				// ALSO, UPDATE THE "RETRIEVE" CUSTOM FIELD TO BE UNCHECKED
		 				update_post_meta($post_id, 'retrieve_mp_data', false);
		 				
		 				// AlSO, UPDATE THE HIDDEN MODEL FIELD SO WE KNOW THAT THE MODEL IS NOT NEW
		 				update_post_meta($post_id, '_wp3d_model_update', 'update');
					}

				} catch ( Exception $ex ) {
					
					$json = null;
					return false;
					
				} // end try/catch
				
				// MATTERTAG DATA
				// Get MODEL JSON
				$mtag_url = esc_url('https://my.matterport.com/api/v1/jsonstore/model/mattertags/'.$mp_id.'?format=json');
				$mtag_content = wp_remote_get($mtag_url);
				
				try { // no error, return mtag json data
				
					// IF THE RE-RETRIEVE DATA FLAG WAS SET, LETS RUN THROUGH THE REST OF THIS				
					if ($retrieve_mp_data) {	
					
						$mtag_json = json_decode($mtag_content['body'], true);
						
		 				// STORE THE NEWLY MINTED ARRAY IN A (HIDDEN) CUSTOM FIELD
		 				update_post_meta($post_id, '_matterport_mattertag_data', $mtag_json); 
		 				
		 				// ALSO, UPDATE THE "RETRIEVE" CUSTOM FIELD TO BE UNCHECKED
		 				update_post_meta($post_id, 'retrieve_mp_data', false);						
						
					}
					
				} catch ( Exception $mtag_ex ) {
					
					$json = null;
					return false;
					
				} // end try/catch
				
				
				
				
			} // end check for $mp_id & $post_id
		 
		//DEBUGGIN
		//return $mp_api_data;
		
	} // end get_mp_apidata	
	


	/**
	 * Wrapper function to show custom Model Schema JSON data, if enabled
	 * 
	 */
	public function show_schema_data(){	
		global $post;
		if ( empty($post) ) { return; } // a page with no $post? bounce.
		
		// If this isn't a 'model' post or ACF is not installed, beat it.
		if ( 'model' != $post->post_type || !function_exists('get_field') ) { return; }
		
		$post_id = $post->ID;
		
		$wp3d_model_type = get_field('wp3d_model_type', $post_id);
		
		// MATTERPORT SPECIFIC
		if ($wp3d_model_type == 'matterport') { 
			// $mp_incoming = trim(get_field('model_link', $post_id));
			// $mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
			$api_data = get_field('_matterport_api_data', $post_id); // hidden custom field stores any retrieved Matterport data in an array
		}
		
		// TST SPECIFIC
		if ($wp3d_model_type == 'threesixtytours') { 
			$api_data = get_field('_tst_api_data', $post_id); // hidden custom field stores any retrieved Matterport data in an array
		}
				
		// check for address
		$address = WP3D_Models()->get_model_address_info($post->ID);
		if (isset($address['lat']) && isset($address['lng'])) { $has_map = true; } else { $has_map = false; } 
		
		// Check for custom Title
		if (get_post_meta(get_the_ID($post_id), '_yoast_wpseo_title', true)) { 
		    $custom_seo_title = true;
		    $custom_seo_title_val = get_post_meta(get_the_ID($post_id), '_yoast_wpseo_title', true);
		} else {
		    $custom_seo_title = false;
		}		
			
		if ($custom_seo_title) { $schema_title = $custom_seo_title_val; } else { $schema_title = get_the_title(); }
		
		// get the best "description"
		$schema_description = '';
		if (get_the_excerpt($post_id)) { add_filter( 'excerpt_more', function() { return ' ...'; } ); $schema_description = esc_attr(get_the_excerpt($post_id)); } elseif (isset($api_data['summary'])) { $schema_description = $api_data['summary']; }
		
		$schema_data = '
		<script type="application/ld+json">{
	         "@context": "https://schema.org",
	         "@type": "Place",
	         ';
		     if ($has_map) {  
		     	$schema_data .= '"geo": {
		             "@type": "GeoCoordinates",
		             "latitude": "'.esc_attr($address['lat']).'",
		             "longitude": "'.esc_attr($address['lng']).'"
		         },';
		     };
		     $schema_data .= '
		     "url": "' . $this->get_model_canonical(get_the_permalink($post_id)) . '",
		     "name": "' . $schema_title .'"';
		     if ($schema_description) {
		     	$schema_data .= ',
		     "description": "' . $schema_description .'"';
			}
		    $schema_data .= '}
		 </script>'."\n\n";
		 
		echo $schema_data;
	     
     }



	/**
	 * Wrapper function to show custom Content Schema Markup
	 * 
	 */
	public function get_content_schema($post_id, $wp3d_id, $wp3d_iframe_src_url){
		$wp3d_model_type = get_field('wp3d_model_type', $post_id);
		$wp3d_inline_schema = '
		    <meta itemprop="embedURL" content="'.$wp3d_iframe_src_url.'">';
		
		// MATTERPORT SPECIFIC
		if ($wp3d_model_type == 'matterport') { 
			$api_data = get_field('_matterport_api_data', $post_id);
		
            if (isset($api_data['created'])) { $wp3d_inline_schema .= ' 
            <meta itemprop="uploadDate" content="'.esc_attr($api_data['created']).'">';
            }
            $wp3d_inline_schema .= '
            <meta itemprop="thumbnailUrl" content="https://my.matterport.com/api/v1/player/models/'. $wp3d_id .'/thumb">';
            if (isset($api_data['name'])) { $wp3d_inline_schema .= ' 
            <meta itemprop="name" content="'. esc_attr($api_data['name']).'">';
            }
            if (isset($api_data['summary'])) { $wp3d_inline_schema .= ' 
            <span itemprop="description" class="hidden wp3d-hidden">'. esc_attr($api_data['summary']).'</span>'."\n";
            } 
		}
		
		// TST SPECIFIC
		if ($wp3d_model_type == 'threesixtytours') { 
			$api_data = get_field('_tst_api_data', $post_id);
			
            if (isset($api_data['created'])) { $wp3d_inline_schema .= ' 
            <meta itemprop="uploadDate" content="'.esc_attr($api_data['created']).'">';
            }
            $wp3d_inline_schema .= '
            <meta itemprop="thumbnailUrl" content="https://my.threesixty.tours/app/images/photos/preview/'. $api_data['resource_img_id'] .'.jpg">';
            if (isset($api_data['name'])) { $wp3d_inline_schema .= ' 
            <meta itemprop="name" content="'. esc_attr($api_data['name']).'">';
            }
            if (isset($api_data['summary'])) { $wp3d_inline_schema .= ' 
            <span itemprop="description" class="hidden wp3d-hidden">'. esc_attr($api_data['summary']).'</span>'."\n";
            } 
		}
    
    	return $wp3d_inline_schema;
    }
	
	
	
	/**
	 * Wrapper function to set Google map API within ACF
	 * 
	 */
	public function acf_google_map_api( $api ){
		// It appears that we need the "Server" key in order to keep address lookup working in ACF
		$api['key'] = get_option('wp3d_google_maps_api_server_key');
		
		return $api;
	}
	

	/**
	 * Wrapper function to set the featured image with ACF
	 * 
	 */
	public function set_featured_image( $value, $post_id, $field )
	{
		if ( $value )
		{
			// Add the value which is the image ID to the _thumbnail_id meta data for the current post
			update_post_meta( $post_id, '_thumbnail_id', $value );
		}
		return $value;
	}



	/**
	 * Wrapper function to set the model social image 
	 * 
	 */
	public function add_social_play_button($post_id, $post, $update) {

		// get the POST ID, if it wasn't passed in
		if (!isset($post_id)) {
			$post_id = $post->ID;
		}
		
		if ( 'model' == $post->post_type && function_exists('get_field') && $update) { // needed the $update
		    
		    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return; // prevents goofy & empty PNG in the Media Library
		    
		    if (get_field('wp3d_model_type') == 'static_image') { // if we're on a STATIC IMAGE Model
		    
		    			// UPDATE THE "RETRIEVE" CUSTOM FIELD TO BE UNCHECKED
			 			update_post_meta($post_id, 'generate_social_image', false);
			 			
			 			// BOUNCE
			 			return;
		    }
		    		
			 if (!get_field('_social_image_id', $post_id) || get_field('generate_social_image', $post_id) ) {
		
				// **** if we're here, we can be pretty sure that we need to go make a new "social image" **** //
		
				// get ready to get the processed image
				$external_key_src = file_get_contents('http://tools.wp3dmodels.com/play-img/key.json');
				$external_key_json = json_decode($external_key_src, true);
				$imgkey = $external_key_json['key'];
				$siteurl = site_url().' : '.$post->ID;
				
				$thumb_id = get_post_thumbnail_id($post_id);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'mp-intro-size', true);
				
				//DEBUG
				// echo $imgkey; 
				// echo $thumb_url[0]; 
				// exit;
				
				// build the override image SRC URL
				$image_url = 'https://tools.wp3dmodels.com/play-img/show.php?src='.urlencode($thumb_url[0]).'&key='.$imgkey.'&site='.urlencode($siteurl);
				
				//DEBUG
				// echo $image_url; 
				// exit;				
				
				$image_tmp = download_url($image_url);
				
				    if( is_wp_error( $image_tmp ) ){
				        echo "<br>Social Image Download Failure.";
				    } else { // NO ERRORS SO FAR
				        $image_size = filesize($image_tmp);
				
				        $file = array(
				           'name' => $post_id.'-social.jpg', // ex: wp-header-logo.png
				           'type' => 'image/jpeg',
				           'tmp_name' => $image_tmp,
				           'error' => 0,
				           'size' => $image_size
				        );
				      
						// If error storing temporarily, unlink
						if ( is_wp_error( $image_tmp ) ) {
							@unlink($file['tmp_name']);
							$file['tmp_name'] = '';
						}        
				
				        //This image/file should show on media page...
				        $thumb_id = media_handle_sideload( $file, $post_id, 'social_image');
				        
						// If error storing permanently, unlink
						if ( is_wp_error($thumb_id) ) {
							@unlink($file['tmp_name']);
							return $thumb_id;
						}  
				        
				        //return wp_get_attachment_url($thumb_id);
				        update_post_meta($post_id, '_social_image_id', $thumb_id);
				        
			 			// ALSO, UPDATE THE "RETRIEVE" CUSTOM FIELD TO BE UNCHECKED
			 			update_post_meta($post_id, 'generate_social_image', false);
			        
			    	} // end errors else
				
			    } // end social image id
			
		    } // end is model
	    	
	    } // end get social image	        
	
		

	/**
	 * Wrapper function to set the correct OG image
	 * Only applies to "override" images because YOAST already look for the "featured" image
	 * 
	 */
	public function get_fb_image($image) {
		global $post;
		
		if (is_singular('model') && function_exists('get_field')) { // checking for models & ACF exists
		
			// This was added to allow for the ability to use Yoast SEO to set a completely unique FB image
			if (class_exists('WPSEO_Meta')) { // make sure this Yoast class is present
				$ogimg = WPSEO_Meta::get_value( 'opengraph-image', $post->ID ); 
				if ( $ogimg !== '' ) {
					return $image;
				}
			}

	    	//Are we on a STATIC IMAGE Model
	    	if (get_field('wp3d_model_type', $post->ID) == 'static_image') { // 
	    	
		  		$mp_image_url = get_field('image_override');
				$mp_src = $mp_image_url['sizes']['mp-intro-size'];
				return $mp_src;
			
			// if we're not on a static image AND the ovelay option is set AND there's a social image...then grab it!
	    	} elseif (get_option('wp3d_enable_social_overlay') && get_field('_social_image_id', $post->ID )) {
	    		
					$social_image = get_field('_social_image_id', $post->ID );
					$social_image_url = wp_get_attachment_url( $social_image );
					return $social_image_url;

			// if the social overlay options aren't set (or the social overlay image doesn't exist) & we have overrides
			} elseif (get_field('add_model_image_override', $post->ID ) && get_field('image_override', $post->ID )) { // if we've got an overriding image 
				
				//if (get_field('image_override')) { // if no image override has been set, we gotta get the image from MP
		  		$mp_image_url = get_field('image_override', $post->ID);
				$mp_src = $mp_image_url['sizes']['mp-intro-size'];
				return $mp_src;
			
			// if nothing else, revert to whatever Yoast is including	
			} else {
				return $image;
			}
			
		} else { // end singular post/location check...otherwise just return the image
			return $image;
		}
		
	}
	
	
	/**
	 * Wrapper function to set the correct canonical link
	 * 
	 */
	public function get_model_canonical($modelcanon) {
		global $post;
		if (function_exists('get_field')) { // check for ACF
			$model_default_view_link = get_field('default_view_link');
			$model_permalink = trailingslashit(esc_url(get_the_permalink()));
			if (is_singular('model')) { // checking for models only
				if ($model_default_view_link == 'skinned') { // skinned view canonical
					return $model_permalink.'skinned/';
				} elseif ($model_default_view_link == 'nobrand') { // nobrand view canonical
					return $model_permalink.'nobrand/';
				} elseif ($model_default_view_link == 'fullscreen') { // fullscreen view canonical
					return $model_permalink.'fullscreen/';
				} elseif ($model_default_view_link == 'fullscreen-nobrand') { // fullscreen-nobrand view canonical
					return $model_permalink.'fullscreen-nobrand/';
				} elseif ($model_default_view_link == 'custom') { // custom URL canonical
					$default_view_link = get_field('view_link_override');
					return esc_url($default_view_link);
				} else { // this covers the 'standard' or default view
					return $modelcanon;
				}
			} else { // end singular post/location check...otherwise just return canonical
				return $modelcanon;
			}
		}
	}	
	
	
	/**
	 * Wrapper function to set the correct OG description
	 * 
	 */
	public function get_fb_description($ogdesc) {
		global $post;
		if (is_singular('model')) { // checking for models only
			if (get_the_content() == '') { // if no content has been added, get the summary from the API/Matterport data
				$api_data = get_post_meta( $post->ID, '_matterport_api_data', true );
				if (isset($api_data['summary'])) {
					$ogdesc = $api_data['summary'];
				}
				return $ogdesc;
			} else {
				return $ogdesc;
			}
		} else { // end singular post/location check...otherwise just return the image
			return $ogdesc;
		}
	}
	
	
	
	/**
	 * Wrapper function to set the set OG video tags inside WPSEO
	 * 
	 */
	public function get_wpseo_video_tags($image) {
		global $post;
		
		if (is_singular('model') && function_exists('get_field')) { // checking for ACF function 'get_field'
		
			// go get the model link
			$mp_image_url = trim(get_field('model_link'));
			
			// if the image url is blank bounce
			if(empty($mp_image_url)) {
				return;
			}
			
			// otherwise, try to extract the ID from the url
			$mp_id = WP3D_Models()->mp_id_from_url($mp_image_url); 

			// if there's an error, bounce
			if ('error' == $mp_id) {
				return;
			}			
			
			// otherwise, lets jam in some new video tags
			$og_output = '<meta property="og:video:url" content="https://my.matterport.com/show/?m='.$mp_id.'&amp;play=1">
<meta property="og:video:secure_url" content="https://my.matterport.com/show/?m='.$mp_id.'&amp;play=1">
<meta property="og:video:type" content="text/html">
<meta property="og:video:width" content="640">
<meta property="og:video:height" content="480">';
			
			//echo $og_output;
		
		} // end function check & single 'model' check
		
	}
	
	
	/**
	 * Wrapper function to set the set OG image sizes, just for the SOCIAL IMAGE OVERRIDE
	 * 
	 */
	public function add_wpseo_custom_image_sizes($image) {
		global $post;
		
		if (is_singular('model') && function_exists('get_field')) { // Checking for a Model & ACF function 'get_field' 
			
			if (get_field('wp3d_model_type', $post->ID ) != "static_image" && get_field('_social_image_id', $post->ID )) { // checking that we're not on a static image & have an existing social image
			
				$social_image_id = get_field('_social_image_id', $post->ID);
				$social_image_attributes = wp_get_attachment_image_src( $social_image_id, 'mp-intro-size' ); 
				
				// otherwise, lets jam in some new image sizes tags
				$og_output = '<meta property="og:image:width" content="'.$social_image_attributes[1].'">
	<meta property="og:image:height" content="'.$social_image_attributes[2].'">'."\n";
				
				echo $og_output;
				
			}
		
		} // end function check & single 'model' check
		
	}	
	

	/**
	 * Wrapper function to retrieve YOUTUBE ID from YOUTUBE URL
	 * 
	 */	
	public function youtube_id_from_url($youtube_url) {
	
		    $parts = parse_url($youtube_url);
		    
		    if(isset($parts['query'])){
		        parse_str($parts['query'], $qs);
		        if(isset($qs['v'])){
		            $youtube_id = $qs['v'];
		        }else if(isset($qs['vi'])){
		            $youtube_id = $qs['vi'];
		        }
		    }
		    
		    if(!isset($youtube_id) && isset($parts['path'])){
		        $path = explode('/', trim($parts['path'], '/'));
		        $youtube_id = $path[count($path)-1];
		    }
	
		    if (isset($youtube_id)) {
		    	return $youtube_id;
		    } else {
		    	return false;
		    }
		    
	}


	/**
	 * Wrapper function to responsively wrap YOUTUBE Video from YOUTUBE URL
	 * 
	 */	
	public function youtube_embed_from_url($youtube_url, $just_src = false) {
	
		    $parts = parse_url($youtube_url);
		    
		    if(isset($parts['query'])){
		        parse_str($parts['query'], $qs);
		        if(isset($qs['v'])){
		            $youtube_id = $qs['v'];
		        }else if(isset($qs['vi'])){
		            $youtube_id = $qs['vi'];
		        }
		    }
		    
		    if(!isset($youtube_id) && isset($parts['path'])){
		        $path = explode('/', trim($parts['path'], '/'));
		        $youtube_id = $path[count($path)-1];
		    }
	
		    if (isset($youtube_id)) {
		    	
		    	if ($just_src) {
		    		return 'https://www.youtube.com/embed/'.$youtube_id.'?rel=0';
		    	} else {
				    return '<div class="embed-container embed-youtube">
				                <iframe src="https://www.youtube.com/embed/'.$youtube_id.'?rel=0" frameborder="0" allowfullscreen></iframe>
				            </div>';
		    	}
		    	
		    } else {

		    	if ($just_src) {
		    		return 'https://tools.wp3dmodels.com/iframe-error/?type=video';
		    	} else {
				    return '<div class="embed-container embed-youtube">
				                <iframe src="https://tools.wp3dmodels.com/iframe-error/?type=video" frameborder="0" allowfullscreen></iframe>
				            </div>';
		    	}

		    }
		    
	}
	

	/**
	 * Wrapper function to responsively wrap VIMEO Video from VIMEO URL
	 * 
	 */	
	public function vimeo_embed_from_url($vimeo_url, $just_src = false) {
	    
		if (preg_match("/(?:https?:\/\/)?(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $vimeo_url, $id_arr)) {
		    $vimeo_id = $id_arr[3];
		} 	    

	    if ($vimeo_id) {
	    	if ($just_src) {
	    		return 'https://player.vimeo.com/video/'.$vimeo_id.'?title=0&byline=0&portrait=0&wmode=transparent';
	    	} else {
			    return '<div class="embed-container embed-vimeo">
			                <iframe src="https://player.vimeo.com/video/'.$vimeo_id.'?title=0&byline=0&portrait=0&wmode=transparent" frameborder="0" allowFullScreen></iframe>
			            </div>';
	    	}
	    } else {
	    	if ($just_src) {
	    		return 'https://tools.wp3dmodels.com/iframe-error/?type=video';
	    	} else {
			    return '<div class="embed-container embed-youtube">
			                <iframe src="https://tools.wp3dmodels.com/iframe-error/?type=video" frameborder="0" allowfullscreen></iframe>
			            </div>';
	    	}
	    }
	}
	
	
	/**
	* Grab all iframe src from a string
	*/
	public function get_iframe_src( $input ) {
		
		$input = html_entity_decode($input);
		
		// Somebody just stuffed in a URL (matches either "http or https")
		if (strpos($input, 'http') === 0) {
			
			$src = $input;
				
		} elseif (strpos($input, 'iframe') !== false)  {
		
			// create new DOMDocument
			$doc = new DOMDocument();
			
			// set error level
			$internalErrors = libxml_use_internal_errors(true);
			
			// load HTML
			$doc->loadHTML($input);
			
			// Restore error level
			libxml_use_internal_errors($internalErrors);
				
			$src = $doc->getElementsByTagName('iframe')->item(0)->getAttribute('src');

		} else {
			
			$src = "https://tools.wp3dmodels.com/iframe-error/";
		}
		
		return $src;
	}

    /**
     * Grab iframe allow from a string
     */
    public function get_iframe_allow( $input ) {
        $input = html_entity_decode($input);
        $allow = '';

        if (strpos($input, 'iframe') !== false)  {
            // create new DOMDocument
            $doc = new DOMDocument();

            // set error level
            $internalErrors = libxml_use_internal_errors(true);

            // load HTML
            $doc->loadHTML($input);

            // Restore error level
            libxml_use_internal_errors($internalErrors);

            $allow = $doc->getElementsByTagName('iframe')->item(0)->getAttribute('allow');
        }

        return $allow;
    }
	
	
	/**
	 * Function to check the validity of a gravatar
	 * 
	 */	
	public function validate_gravatar($email) {
		// Craft a potential url and test its headers
		$hash = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		$headers = @get_headers($uri);
		if (!preg_match("|200|", $headers[0])) {
			$has_valid_avatar = FALSE;
		} else {
			$has_valid_avatar = TRUE;
		}
		return $has_valid_avatar;
	}	
	
	
	/**
	 * Wrapper function to determine if a model has an address (used for enqueing/etc)
	 * 
	 */
	public function get_wp3d_address($post_id) {
		
		if(!isset($post_id)) {
			global $post;
			$post_id = $post->ID;
		}
		
		if (get_post_type() == 'model') {
			$address = get_field('model_location', $post_id);
			
			// if this is a non-matterport Model, look for the separate custom map field
			if (empty($address)) { $address = get_field('model_location_noapi', $post_id); } 
			
			if (!empty($address)){	
				return $address;
			} else {
				return false;
			}
			
		} else {
			return false;
		}

	}
	
	
	
	/**
	 * Wrapper function to determine if a model has an address (using $GET value & raw meta query)
	 * Only used when assembling MODEL custom fields, and largely for legacy v1 -> v2 safeguards
	 * 
	 */
	public function has_wp3d_address() {
		
		if (is_admin() && 
		isset($_GET['post'])) {
			$post_id = $_GET['post'];
			if (get_post_meta( $post_id, 'model_location', true )) {
				return true;
			} else {
				return false;
			}			

		} else {
			return;
		}

	}
	

	/**
	 * is_wp3d_edit_page 
	 * function to check if the current page is a post edit page
	 * http://wordpress.stackexchange.com/questions/50043/how-to-determine-whether-we-are-in-add-new-page-post-cpt-or-in-edit-page-post-cp
	 * 
	 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
	 * @return boolean
	 */
	public function is_wp3d_edit_page($new_edit = null){
	    global $pagenow;
	    //make sure we are on the backend
	    if (!is_admin()) return false;
	
	    if($new_edit == "edit")
	        return in_array( $pagenow, array( 'post.php',  ) );
	    elseif($new_edit == "new") //check for new post page
	        return in_array( $pagenow, array( 'post-new.php' ) );
	    else //check for either new or edit
	        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}


	
	/**
	 * Wrapper function to determine basic info about a model (using $GET value & raw meta query)
	 * Only used when assembling MODEL custom fields
	 * 
	 */
	public function get_wp3d_type_info() {
		
		if (is_admin()) { // just checking this stuff in admin 
		
			$wp3d_type_info_arr = array();
		
			if (isset($_GET['post'])) {
				
				$wp3d_post_id = $_GET['post'];
				
				// pass the id to WordPress for basic post info, returned in an array
				//$wp3d_post_data = get_post( $wp3d_post_id, ARRAY_A );
				
				$wp3d_post_type = get_post_type($wp3d_post_id);
				$wp3d_model_type = get_post_meta( $wp3d_post_id, 'wp3d_model_type', true );
				$wp3d_model_location = get_post_meta( $wp3d_post_id, 'model_location', true );
				
				// can be expanded here
				
				// Build the Array
				$wp3d_type_info_arr['new'] = false;
				$wp3d_type_info_arr['post_type'] = $wp3d_post_type;
				
				// we know we're on an existing post 
				if ($wp3d_post_type == 'model') { // specific to "model" post type
				
					// MODEL TYPE
					if (get_post_meta( $wp3d_post_id, 'wp3d_model_type', true )) { // has an existing model type
						$wp3d_type_info_arr['model_type'] = get_post_meta( $wp3d_post_id, 'wp3d_model_type', true );
					} else { // if it is a "Model" it is likely a Matterport model, return default
						$wp3d_type_info_arr['model_header'] = 'matterport';
					}
					
					// MODEL LOCATION
					if (get_post_meta( $wp3d_post_id, 'model_location', true )) { // has existing location data
						$wp3d_type_info_arr['model_location'] = get_post_meta( $wp3d_post_id, 'model_location', true );
					} else { // does not have existing location data
						$wp3d_type_info_arr['model_location'] = false;
					}

				}

				return $wp3d_type_info_arr;
				//return false;

			} else { // must be a NEW Model
			
				$wp3d_type_info_arr['new'] = true;
				return $wp3d_type_info_arr;
				//return true;

			} // end else
			
			return;
			
		} // end admin check
	}	
	
	
	
	
	/**
	 * Wrapper function to delete any saved/cached related model query data
	 * 
	 */
	public function delete_post_transient($post_id, $post, $update) {
	global $post;
	     delete_transient( 'related_query_results_'.$post_id );
	}
	


	
	/**
	 * get model address info.  Must be called with a (model) postid as well as the Matterport id, returns false if no address data, or model is set to not include address data.
	 *
	 * @param string $postid 
	 * @return $address array with lat/lng data from matterport-provided (then geocoded) data, or from custom ACF field. phew.
	 */
	public function get_model_address_info($post_id) {
	    $address = false; // we'll start here
	    
	    if (function_exists('get_field')) { // check for ACF
	    
		    // run function to get get address info
			$address_source = get_field('model_address_source', $post_id);
			
		    if (empty($address_source)) {
		    	
		    	if (get_field('model_location', $post_id)) { 
		    		$address = WP3D_Models()->get_wp3d_address($post_id); // largely a legacy v1 -> v2 situation where there is actually address data but a previously added model hasn't yet been saved in v2
		    	} else {
		    		$address = false; // no address source set AND no previously added data, set as false!
		    	}
		    	
		    } elseif ($address_source == 'no_address') { // if nothing is selected 
		    	
		    	$address = false; 
		    	
		    } elseif ($address_source == 'matterport_address') { // try to get address info from Matterport
		    
				// this gets all returned api data, including address data, in an array
				$address = get_post_meta( $post_id, '_matterport_api_data', true );
				    
			} elseif ($address_source == 'custom_address') {
					
				$address = WP3D_Models()->get_wp3d_address($post_id);

			} 
			
	    } // end ACF function check
	
		return $address;
	}
	
	
	/**
	 * get the PRIMARY term ID from the associated Model Client taxonomy.  
	 * this requires a "term_list" array be passed in as well, as such:
	 * $term_list = wp_get_post_terms($post_id, $taxonomy, array("fields" => "ids"));
	 *
	 * @param string $post_id 
	 * @return the SRC for the appropriate model "Large" logo
	 */	
	public function get_primary_taxterm($post_id, $taxonomy, $term_list) {
		
		if ( class_exists('WPSEO_Primary_Term') ) {
			
			// Show the post's 'Primary' term, if this Yoast feature is available, & one is set
			$wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, $post_id );
			$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
			
			if (is_wp_error($wpseo_primary_term) || !$wpseo_primary_term) { 
				// Default to first category (not Yoast) if an error is returned
				$term_id = $term_list[0]; 
			} else { 
				// Yoast Primary category
				$term_id = $wpseo_primary_term;
			}
		
		} else {
			// Default, display the first category in WP's list of assigned categories
			$term_id = $term_list[0]; 
		}
		
		return $term_id;
		
	}
	

	/**
	 * get model large logo.  
	 *
	 * @param string $post_id 
	 * @return the SRC for the appropriate model "Large" logo
	 */
	public function get_model_large_logo($post_id) {
		
		// gotta figure out what (branded) view we're on - standard, branded, or fullscreen
		$is_fullscreen = get_query_var('fullscreen', null);
        $is_skinned = get_query_var('skinned', null);
        $force_standard_company = get_option('wp3d_standard_company_branding');
        
        // needed to test for a term-based small logo
        $taxonomy = 'model-client';
		$term_list = wp_get_post_terms($post_id, $taxonomy, array("fields" => "ids")); 
		
		if (isset($term_list[0])) { // if there's a model-client assigned, we need to take a second look to see if they also have a logo assigned.		
			$term_id = WP3D_Models()->get_primary_taxterm($post_id, $taxonomy, $term_list);
			$term_logo = get_field('large_logo_client_override', $taxonomy.'_'.$term_id); 
			$term_logo_src = $term_logo['url'];			
		} else { 
			$term_id = false; 
			$term_logo = false; 
			$term_logo_src = false; 
		}		
		
        
        // if we're on the "standard" view (not fullscreen or skinned)
        if (!isset($is_fullscreen) && !isset($is_skinned) ) { // neither fullscreen or skinned
        	if ($force_standard_company) {
	        	$large_logo = get_option('wp3d_large_company_logo');
				$large_logo_arr = wp_get_attachment_image_src( $large_logo, 'full' );
				$large_logo_src = $large_logo_arr[0];
				return $large_logo_src;
        	}
        }
        
    	// we override the large logo if it is both set AND the checkbox is checked
    	if (get_field('large_logo_override', $post_id) && get_field('add_override_logos', $post_id)) { 
			$large_logo = get_field('large_logo_override', $post_id); //look for the override logo
			$large_logo_src = $large_logo['url'];
    	} elseif ($term_logo_src) {
			$large_logo_src = $term_logo_src;			
		} elseif (get_option('wp3d_large_company_logo')) {
			$large_logo = get_option('wp3d_large_company_logo');
			$large_logo_arr = wp_get_attachment_image_src( $large_logo, 'full' );
			$large_logo_src = $large_logo_arr[0];
		} else {
			return false;
		}
	
		return $large_logo_src;

	}
	
	
	/**
	 * get model small logo.  
	 *
	 * @param string $post_id 
	 * @return the SRC for the appropriate model "Small" logo
	 */
	public function get_model_small_logo($post_id) {
		
		// gotta figure out what (branded) view we're on - standard, branded, or fullscreen
		$is_fullscreen = get_query_var('fullscreen', null);
        $is_skinned = get_query_var('skinned', null);
        $force_standard_company = get_option('wp3d_standard_company_branding');
        
        // needed to test for a term-based small logo
        $taxonomy = 'model-client';
		$term_list = wp_get_post_terms($post_id, $taxonomy, array("fields" => "ids"));
		
		if (isset($term_list[0])) { // if there's a model-client assigned, we need to take a second and look to see if they also have a logo assigned.		
			$term_id = WP3D_Models()->get_primary_taxterm($post_id, $taxonomy, $term_list);
			$term_logo = get_field('small_logo_client_override', $taxonomy.'_'.$term_id); 
			$term_logo_src = $term_logo['url'];			
			
		} else { 
			$term_id = false; 
			$term_logo = false; 
			$term_logo_src = false; 
		}

        // if we're on the "standard" view (not fullscreen or skinned)
        if (!isset($is_fullscreen) && !isset($is_skinned) ) { // neither fullscreen or skinned

        	if ($force_standard_company) {
	        	$small_logo = get_option('wp3d_small_company_logo');
				$small_logo_arr = wp_get_attachment_image_src( $small_logo, 'full' );
				$small_logo_src = $small_logo_arr[0];
				return $small_logo_src;
        	}
        	
        }
        
		// we locally override the small logo if it is both set AND the checkbox is checked
		if (get_field('small_logo_override', $post_id) && get_field('add_override_logos', $post_id)) { 
			$small_logo = get_field('small_logo_override', $post_id); //look for the override logo
			$small_logo_src = $small_logo['url'];
		// we override the global small logo if it is present in an assigned "model-client" taxonomy
		} elseif ($term_logo_src) {
			$small_logo_src = $term_logo_src;
		} elseif (get_option('wp3d_small_company_logo')) {
			$small_logo = get_option('wp3d_small_company_logo');
			$small_logo_arr = wp_get_attachment_image_src( $small_logo, 'full' );
			$small_logo_src = $small_logo_arr[0];
		} else {
			return false;
		}
	
		return $small_logo_src;

	}
		
	
	
	/**
	 * get default (settings) small logo.  
	 *
	 * @param string $post_id 
	 * @return the SRC for the appropriate model "Small" logo
	 */
	public function get_settings_small_logo($post_id) {
	
		if (get_option('wp3d_small_company_logo')) {
			$small_logo = get_option('wp3d_small_company_logo');
			$small_logo_arr = wp_get_attachment_image_src( $small_logo, 'full' );
			$small_logo_src = $small_logo_arr[0];
		} else {
			return false;
		}
	
		return $small_logo_src;

	}
	

	/**
	 * get sold image.  
	 *
	 * @param string $post_id 
	 * @return the SRC for overridden or default "sold" image
	 */
	public function get_sold_image() {
	global $post;
		
	$plugins_url = plugins_url();		
		
		if (get_option('wp3d_sold_image')) { //if there's a replacement "SOLD" image, update SRC
			$sold_image_id = get_option('wp3d_sold_image'); 
			$sold_image_arr = wp_get_attachment_image_src( $sold_image_id, 'full' );
			$sold_image_src = $sold_image_arr[0];	
		} else {
			$sold_image_src = $plugins_url.'/wp3d-models-free/assets/images/wp3d-sold-banner.png';
		}

		return $sold_image_src;
	}
	
	
	/**
	 * get pending image.  
	 *
	 * @param string $post_id 
	 * @return the SRC for overridden or default "pending" image
	 */
	public function get_pending_image() {
	global $post;
		
	$plugins_url = plugins_url();		
		
		if (get_option('wp3d_pending_image')) { //if there's a replacement "PENDING" image, update SRC
			$pending_image_id = get_option('wp3d_pending_image'); 
			$pending_image_arr = wp_get_attachment_image_src( $pending_image_id, 'full' );
			$pending_image_src = $pending_image_arr[0];	
		} else {
			$pending_image_src = $plugins_url.'/wp3d-models-free/assets/images/wp3d-pending-banner.png';
		}

		return $pending_image_src;
	}	
		
		
	/**
	 * get custom status image.  
	 *
	 * @param string $post_id 
	 * @return the SRC for a custom "pending" image
	 */
	public function get_custom_status_image() {
	global $post;
		
	$plugins_url = plugins_url();		
		
		if (get_option('wp3d_custom_status_image')) { //if there's a replacement "CUSTOM STATUS" image, update SRC
			$custom_status_image_id = get_option('wp3d_custom_status_image'); 
			$custom_status_image_arr = wp_get_attachment_image_src( $custom_status_image_id, 'full' );
			$custom_status_image_src = $custom_status_image_arr[0];	
		} else {
			$custom_status_image_src = $plugins_url.'/wp3d-models-free/assets/images/wp3d-custom-status-banner.png';
		}

		return $custom_status_image_src;
	}
	

	/**
	 * get model share list.  
	 *
	 * @param string $post_id 
	 * @param boolean $skinned_links - whether or not we're forcing the function to return the skinned links or not
	 * @param boolean $is_introed - whether or not the referring view is introed, or not
	 * @return $model_share_list_html - this unordered list is returned in HTML
	 */
	public function get_model_share_list($post_id, $skinned_links = false, $is_introed = false) {
		global $post;
		
		//if (!isset($is_introed)) { $is_introed = false; } // set to false if empty
		//if (!isset($skinned_links)) { $skinned_links = false;  } else { $skinned_links = true; $is_introed = true; } 
	
		ob_start(); ?>

		<ul class="wp3d-share-icons">
									
			<?php 
			
			$mp_global_sharing_autostart = get_option('wp3d_enable_sharing_autostart');
			
			// Pass along the sharing autostart hash, if enabled
			if ($mp_global_sharing_autostart && $is_introed) { 
				$share_start = "#start";
			} else {
				$share_start = '';
			}
			
			if ($skinned_links) {
				$default_view_link = trailingslashit(get_permalink($post_id)).trailingslashit('skinned');
				$urlencoded_permalink = urlencode(esc_url($default_view_link.$share_start));	
			} else {
				$urlencoded_permalink = urlencode(WP3D_Models()->get_model_link($post_id).$share_start);
			}

			$urlencoded_title = urlencode(get_the_title($post_id));
			$share_image = WP3D_Models()->get_model_image_src(get_the_ID($post_id), 'large');
			// build the share array
			$share_arr = array (
				'facebook'    => 'https://www.facebook.com/sharer/sharer.php?u='.$urlencoded_permalink,
				'twitter'     => 'https://twitter.com/share?url='.$urlencoded_permalink.'&amp;text='.$urlencoded_title,
				'linkedin'    => 'https://www.linkedin.com/shareArticle?mini=true&amp;url='.$urlencoded_permalink.'&amp;title='.$urlencoded_title,
				'pinterest'   => 'https://pinterest.com/pin/create/button/?url='.$urlencoded_permalink.'&amp;media='.$share_image.'&amp;description='.$urlencoded_title
				);
								
			foreach ($share_arr as $key => $val) { ?>
			
			<?php //hotfixes
			if ($key == "website") { $key_icon = 'globe'; }
			elseif ($key == "google") { $key_icon = 'google'; }
			else { $key_icon = $key; }
			
			$onclick = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
			$target = '_blank';
			$span_class = 'fa-stack fa-2x';
			$icon_stacked = '<i class="fa fa-circle-thin fa-stack-2x"></i>';
			$icon_class = 'fa fa-'.$key_icon.' fa-stack-1x';
			$key_icon_class = $key_icon;
			?>
			
			<li class="<?php echo apply_filters( 'wp3d_share_key_icon_class', $key_icon_class ); ?>">
				<a href="<?php echo $val; ?>" onclick="<?php echo apply_filters( 'wp3d_share_onclick', $onclick ); ?>" target="<?php echo apply_filters( 'wp3d_share_target', $target ); ?>" title="<?php echo esc_attr($key_icon); ?>" aria-label="<?php echo esc_attr($key_icon); ?>">
					<span class="<?php echo apply_filters( 'wp3d_share_span_class', $span_class ); ?>">
						<?php echo apply_filters( 'wp3d_share_icon_stacked', $icon_stacked ); ?>
						<i class="<?php echo apply_filters( 'wp3d_share_icon_class', $icon_class ); ?> <?php echo apply_filters( 'wp3d_share_key_icon_class', $key_icon_class ); ?>"></i>
					</span>						
				</a>
			</li>	
				    
			<?php } ?>
		</ul>

	<?php  
						
	$model_share_list_html = ob_get_clean();
    return $model_share_list_html;

	} // get model share list
	
	
	/**
	 * get model link.  
	 *
	 * @param string $post_id 
	 * @param string $force_fullscreen (optional - added in v.3.0)
	 * @param string $force_tst_startscreen (optional - added in v.3.0)
	 * @return $url - this should be used in place of 'get_permalink()' so that lists of models (shortcode or related) point to the preferred "view"
	 */
	public function get_model_link($post_id, $force_fullscreen = false, $force_tst_startscreen = false) {
		global $post;

	    if (function_exists('get_field')) { // check for ACF
	    
	    	$default_view_ep = get_field('default_view_link');
	    	$wp3d_model_type = get_field('wp3d_model_type');
	    	
	    	// Added a "force fullscreen" flag for use with the modal window shortcode option in v.3.0
	    	if ($force_fullscreen) { $default_view_ep = 'fullscreen'; }
	    	
			if ( !empty($default_view_ep) && $default_view_ep != 'stock' ) { 
				
				if ($default_view_ep == 'custom') { 
					$default_view_link = get_field('view_link_override');
					return esc_url($default_view_link);
				} else { 
					$default_view_link = trailingslashit(get_permalink($post_id)).trailingslashit($default_view_ep);
					if ($force_tst_startscreen && $wp3d_model_type == 'threesixtytours') { $default_view_link .= '?fss'; }
					return esc_url($default_view_link);
				}
				
				// 'stock' => __('STANDARD (Current theme page formatting)', 'wp3d-models'),
				// 'nobrand' => __('UNBRANDED (Simple unbranded page formatting)', 'wp3d-models'),
				// 'skinned' => __('SKINNED (Single/Stand-alone page formatting)', 'wp3d-models'),					
				// 'fullscreen' => __('FULLSCREEN (Fullscreen model formatting)', 'wp3d-models'),
				// 'fullscreen-nobrand' => __('FULLSCREEN &amp; UNBRANDED (Fullscreen &amp; Unbranded model formatting)', 'wp3d-models'),
					
			}
			
	    } // end ACF function check
	
		return get_permalink($post_id);
	}	
	
	
	/**
	 * get model image source.  
	 *
	 * @param string $post_id, $size -- if $size == 'large', get the big image, otherwise get the thumb 
	 * @return $mp_src - this should be used whenever trying to retrieve the 'featured' image for a model
	 */
	public function get_model_image_src($post_id, $size) {
		global $post;

			if ($size == 'large') { 
				$img_size = 'mp-intro-size'; 
			} else {
				$img_size = 'mp-thumb-size';
			}
	
	    	if (get_field('add_model_image_override') && get_field('image_override', $post_id)) { // if we've got an overriding image 
            
                $wp3d_override_image = get_field('image_override', $post_id);
                $mp_src = $wp3d_override_image['sizes'][$img_size];

            } else { // getting images from MP 
             
                $mp_src_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $img_size );
                $mp_src = $mp_src_arr[0];
            }

			return $mp_src;
			
	}	
	

	/**
	 * Wrapper function to determine if a model has an UNREALER SMART GALLERY
	 * 
	 */
	public function get_smart_gallery() {
	global $post;
	
		if (get_field('smart_gallery_url')) {
			ob_start(); ?>
		<div class="smartgallery-container thirdparty-container">
			<iframe allow="vr" allowfullscreen="1" frameborder="0" mozallowfullscreen="1" src="<?php echo esc_html(get_field('smart_gallery_url')); ?>" scrolling="no" webkitallowfullscreen="1" width="960" height="620"></iframe>
		</div>
		<?php 
			$unrealer_html = ob_get_clean();
		    return $unrealer_html;
		} else {
			return false; 
		}
	
	}
	
	// end UNREALER smart gallery
	

	/**
	 * Wrapper function to determine if a model has gallery images added
	 * 
	 */
	public function get_wp3d_gallery_images() {
		global $post;

		if (get_post_type() == 'model' && get_field('add_gallery')) {
			$images = get_field('photo_gallery');
			
			if (!empty($images)){	
				return $images;
			} else {
				return false;
			}
			
		} else {
			return false;
		}

	}
	
	

	/**
	 * Wrapper function to get a model's MP contact info
	 * @param string $api_data, stored (array) data already retrieved from MP
	 * 
	 */

	public function get_mp_contact($api_data) {
		
		$agents_arr = array();
	    
	    // lets transfer some data from the MP Array to the Agents Array
	    // we know that Matterport is returning just one "contact", but 
	    // we'll build out the array with the same structure as if it were local
	    
	    if (isset($api_data['contact_name'])) { $agents_arr[0]['name'] = $api_data['contact_name']; }
	    if (isset($api_data['contact_phone'])) { $agents_arr[0]['phone'] = $api_data['contact_phone']; } // get the phone
	    if (isset($api_data['formatted_contact_phone'])) { $agents_arr[0]['formatted_phone'] = $api_data['formatted_contact_phone']; } // overwrite the phone with the formatted version, if it exists
	    if (isset($api_data['contact_email'])) { $agents_arr[0]['email'] = $api_data['contact_email'];  
	
	        // try to get matterport contact info (w/avatar?)
	        if (WP3D_Models()->validate_gravatar( $api_data['contact_email']) ) {
	            $avatar_url = get_avatar_url( $api_data['contact_email'], array('size' => 300) );
	            $has_avatar = true;
	            $agents_arr[0]['image_src'] = $avatar_url;
	        } else {
	            $has_avatar = false;
	        }
	    } 
	    
	    if (isset($agents_arr)) {
	    	return $agents_arr;
	    } else {
	    	return false;
	    }
		
	}
	


	/**
	 * Wrapper function to get a model's associated agents
	 * 
	 */
	public function get_associated_agents() {
		
	global $post;
	
	$agents_arr = array();
	
	$assoc_agents = get_field('associated_agents');
	
	    if( $assoc_agents ) { 
	         $i=0;
	         foreach( $assoc_agents as $post) { // variable must be called $post (IMPORTANT) 
	            setup_postdata($post); 
	          
	                $agents_arr[$i]['name'] = get_the_title();
	                if (get_field('agent_subheading')) { $agents_arr[$i]['subheading'] = sanitize_text_field(get_field('agent_subheading')); }
	                if (get_field('agent_image')) { $agent_image = get_field('agent_image'); $agents_arr[$i]['image_src'] = $agent_image['sizes']['medium']; }
	
	                // Agent Info Repeater
	                if( have_rows('agent_info') ):
	                    while ( have_rows('agent_info') ) : the_row();
	                        if (get_sub_field('email')) { $agents_arr[$i]['email'] = sanitize_email(get_sub_field('email')); }
	                        if (get_sub_field('phone_mobile')) { $agents_arr[$i]['phone-mobile'] = sanitize_text_field(get_sub_field('phone_mobile')); }
	                        if (get_sub_field('phone_direct')) { $agents_arr[$i]['phone-direct'] = sanitize_text_field(get_sub_field('phone_direct')); }
	                        if (get_sub_field('phone_office')) { $agents_arr[$i]['phone-office'] = sanitize_text_field(get_sub_field('phone_office')); }
	                    endwhile;
	                endif;
	                
	                // Agent Additional Info & Logo
	                if (get_field('add_agent_info')) { $agents_arr[$i]['add-agent-info'] = 'true'; }
	                if (get_field('agent_form_bcc_email')) { $agents_arr[$i]['add-form-bcc-email'] = sanitize_email(get_field('agent_form_bcc_email')); }
	                if (get_field('agent_add_info')) { $agents_arr[$i]['additional-info'] = esc_html(get_field('agent_add_info')); }
	                if (get_field('agent_logo')) { $agent_logo = get_field('agent_logo'); $agents_arr[$i]['logo_src'] = $agent_logo['sizes']['medium']; $agents_arr[$i]['logo_alt'] = $agent_logo['alt']; }
	
	                // Agent Links Repeater
	                if( get_field('add_agent_links') && have_rows('agent_links') ):
	                    $j=0;
	                    while ( have_rows('agent_links') ) : the_row();
	                        if (get_sub_field('link_url')) { $agents_arr[$i]['links'][$j] = sanitize_text_field(get_sub_field('link_url')); }
	                    $j++;
	                    endwhile;
	                endif;           
	                
	                if (get_field('calendly_enabled', $post->ID)) {
                        $agents_arr[$i]['calendly_enabled'] = true;
                        $agents_arr[$i]['calendly_type'] = get_field('calendly_type', $post->ID);
                        $agents_arr[$i]['calendly_event_link'] = get_field('calendly_event_link', $post->ID);
                        $agents_arr[$i]['custom_link_title'] = get_field('custom_link_title', $post->ID);

                        if (get_field('calendly_type', $post->ID) == 'widget') {
                            $agents_arr[$i]['calendly_popup_location'] = get_field('calendly_popup_location', $post->ID);
                            $agents_arr[$i]['calendly_color_back'] = get_field('calendly_color_back', $post->ID);
                            $agents_arr[$i]['calendly_color_border'] = get_field('calendly_color_border', $post->ID);
                            $agents_arr[$i]['calendly_color_text'] = get_field('calendly_color_text', $post->ID);
                        }
                    }
	            
	            $i++; 
	         } // end foreach 
	        wp_reset_postdata(); // gotta reset the $post object
	        
			return $agents_arr;

	    } else { // no assoc agents
	    	
		return false;
		
		}
	
	}


	/**
	 * Wrapper function to determine if a model is showing related models
	 * 
	 */
	public function get_related_models() {
		global $post;
		
		if (get_post_type() == 'model') { // on a single model page
		
			// checking for nobrand
			$is_nobrand = get_query_var('nobrand', null);
			
			if (!isset($_GET['nobrand']) && !isset($is_nobrand) ) {
				$nobrand_flag = false;
			} else {
				$nobrand_flag = true;
			}
		
			$related_models = get_field('related_models'); 
			if ( !empty($related_models) && $related_models != 'none') { // field is set and something other than 'none' is set
			
				// Previously we had a transient in place and checked for its existance here
				// This has been disabled in v2.1 to allow dynamic linking to "nobrand" pages, plus...its debatable how much of a performance boost this was
				//if ( false === ( $related_html_return = get_transient( 'related_query_results_'.$post->ID ) ) ) {
				
						// remove the current post from what gets returned	
						$exclude_ids = array( $post->ID );
						
						// get the taxonomy & term that we are pulling from
						$tax = 'model-'.$related_models;
						
						if($related_models =='type') {
							$term_field = 'related_type_models';
						} elseif ($related_models == 'client') {
							$term_field = 'related_client_models';
						}
						$term_obj = get_field($term_field);
						$term_slug = $term_obj->slug;
						
						// assemble the models and randomize 
						$args = array('post_type'=>'model', 'orderby'=>'rand', 'posts_per_page'=>'-1', 'post__not_in' => $exclude_ids, $tax => $term_slug);
						//print_r($args);
						
						$query = new WP_Query( $args );
						$related_html_return = '';
						$wp3d_related_models_wrap_classes = ''; // no extra classes by default, just here for the filter 
						
						ob_start();
						
if ( $query->have_posts() ) { ?>
<div id="wp3d-related-models" class="wp3d-related-slider slider responsive <?php echo apply_filters( 'wp3d_related_models_wrap_classes', $wp3d_related_models_wrap_classes );?>">
<?php while ( $query->have_posts() ) { 
$query->the_post();
// $model_image is an array
$post_id = get_the_ID();
$model_image_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
$model_link = WP3D_Models()->get_model_link($post_id);
// if we're on a 'nobrand', build our own 'nobrand' URL, vs leaning on the get_model_link function
if ($nobrand_flag) { $model_link = trailingslashit(get_permalink($post_id)) . "nobrand"; } // only link nobrands --> nobrands
?>
<div id="model-<?php echo $post_id; ?>" class="related-model">
<a href="<?php echo esc_url($model_link); ?>" title="<?php the_title_attribute(); ?>"><img src="<?php echo esc_url($model_image_src); ?>" alt="<?php the_title_attribute(); ?>"></a>
</div>
<?php } // endwhile 
wp_reset_postdata(); ?>
</div>
<?php } else { ?>
<!-- <?php __('Related Models were requested, but nothing was returned!', 'wp3d-models'); ?> -->					
<?php }  
				$related_html_return = ob_get_clean();

				//  Transient Removed in v.2.1
				//	set_transient( 'related_query_results_'.$post->ID, $related_html_return, 600 ); // 10 minute cache
				// }  
				
				return $related_html_return;

			} else { // related models field was not set correctly

			return false;
			
			}
			
		} else { // end if post type model
		
			return false;
		}

	}	
	
	
	
	/************* PHONE FORMATTING **************/
	
	/**
	 * Function to clean up phone numbers for tel: links
	 * 
	 */
	public function get_trimmed_phone($num) {
		
		$trimmed_num = preg_replace('/\D+/', '', $num);
	
		if (substr($num, 0, 1) === '+') { // Assumes international number if the first character is a "+" 
			return '+'.$trimmed_num;
		} else { // Assumes this is a US number if there's no "+" 		
			return $trimmed_num;
		}
	
	}
	
	/**
	 * Function to consistently format the output of a phone number
	 * 
	 */
	public function get_formatted_phone($num) {
		
		if (substr($num, 0, 1) === '+') { // Assumes international number if the first character is a "+" - no formatting
			return $num;
		} else { // Assumes this is a US number if there's no "+" - apply formatting
			$trimmed_num = preg_replace('/\D+/', '', $num);
			return "(".substr($trimmed_num, 0, 3).") ".substr($trimmed_num, 3, 3)."-".substr($trimmed_num,6);
		}
	
	}
	
	

	/**
	 * Convert HEX Color Programmatically
	 * http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
	 *
	 * @param string $hex (incoming HEX code)
	 * @param string $steps (positive or negative, depending on whether you want lighter or darker)
	 * @return string model id or FALSE if none found. 
	 */
		
	public function adjustBrightness($hex, $steps) {
	    // Steps should be between -255 and 255. Negative = darker, positive = lighter
	    $steps = max(-255, min(255, $steps));
	
	    // Normalize into a six character long hex string
	    $hex = str_replace('#', '', $hex);
	    if (strlen($hex) == 3) {
	        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
	    }
	
	    // Split into three parts: R, G and B
	    $color_parts = str_split($hex, 2);
	    $return = '#';
	
	    foreach ($color_parts as $color) {
	        $color   = hexdec($color); // Convert to decimal
	        $color   = max(0,min(255,$color + $steps)); // Adjust color
	        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
	    }
	
	    return $return;
	}	
	
	
	/**
	 * get ThreeSixty Tours ID from (duh), URL, or iframe....should match just about anything a user could snag from TST
	 *
	 * @param string $url
	 * @return string model id or FALSE if none found. 
	 */
	public function tst_id_from_url($url) {
		
		$tst_id = array();
	   
	    $url = parse_url($url);
	    
	     // DEBUG
         //print_r($url); exit;

	    // make sure we're working with a "my.threesixty.tours" URL
	    if (isset($url['host']) && 0 === strcasecmp($url['host'], 'my.threesixty.tours') ) { // make sure a TST URL was added

			// if "/app/v" is not found, return with the "error"
        	if (strpos($url['path'], '/app/v/') === false) { 
        		
        		$tst_id = 'error';
        		
        	} else { // proceed
        	
	            $url_path_arr = array(); // set the array
	            $url_path_arr = explode('/', $url['path']); // break it up into pieces
	            $url_path_count = count($url_path_arr);
	            
	            // DEBUG
	            //print_r($url_path_arr); exit;
	            //echo $url_path_count; exit;
	            
		            if ($url_path_count == 6) { // first array value is empty
		            	$tst_id['type'] = 'tour';
		            	$tst_id['tour'] = $url_path_arr[5];
		            	$tst_id['pano'] = $url_path_arr[4];
		            	$tst_id['user'] = $url_path_arr[3];
		            } elseif ($url_path_count == 5) { // first array value is empty
		            	$tst_id['type'] = 'pano';
		            	$tst_id['pano'] = $url_path_arr[4];
		            	$tst_id['user'] = $url_path_arr[3];	            	
		            } else {
		            	$tst_id['type'] = 'error';
		            }
		            
	            // The naming of "id" here might leave something to be desired, but this end value (from the URL) is what we need to retreive the hero image.
	            $tst_id['id'] = array_pop($url_path_arr); // grab the last piece
	            
	            // DEBUG
	            //print_r($tst_id); exit;
            
        	} // end checking for "/app/v/"
            
	    } else { // no TST url
	    
	    	$tst_id = 'error';
	    }
	    
	    //print_r($tst_id);
	    
	    return $tst_id;
	}
	
	
		
	/**
	 * get matterport ID from ID (duh), URL, or iframe....should match just about anything a user could snag from Matterport
	 *
	 * @param string $url
	 * @return string model id or FALSE if none found. 
	 */
	public function mp_id_from_url($url) {
	    $mp_id = false;
	    $url= trim($url);
	    
	// if someone just stuck in the ID, instead of the URL
	if (strlen($url) == 11){
		
		// assign the id and move on
		$mp_id = $url;
	
	// otherwise, lets run some checks	
	} else { 
	    
		// quick iframe test, looking for accidentally placed MP iframe
		$iframe_test = strpos($url,'iframe');
		
		if (!empty($iframe_test)) {
		// its an iframe
			$url = html_entity_decode($url);
		    preg_match('/src="([^"]+)"/', $url, $match);
			$url = $match[1];
		} 
		
	    $url = parse_url($url);
	    
	    if (isset($url['host'])) {
	    	
	    	if ( 
	    		0 === strcasecmp($url['host'], 'my.matterport.com') || 
	    		0 === strcasecmp($url['host'], 'my.matterportvr.cn') ||
	    		0 === strcasecmp($url['host'], 'mpembed.com')
	    	) { // make sure a matterport URL was added
	    	
	    
		        if (isset($url['query'])) {
		        	
		            parse_str($url['query'], $url['query']);
		            
		            if (isset($url['query']['m'])) { // 'm' parameter
		                #### (dontcare)://my.matterport.com/(dontcare)?m=<m id>
		                $mp_id = $url['query']['m'];
		            } elseif (isset($url['query']['model'])) { // 'model' parameter
		            	$mp_id = $url['query']['model'];
		            }
		        }
		        
		        if (false == $mp_id ) {
		            $url['path'] = explode('/', substr($url['path'], 1));
		            
		            if (in_array($url['path'][0], array('m', 'models'))) {
		                #### (dontcare)://my.matterport.com/(whitelist)/<m id>
		                $mp_id = $url['path'][1];
		            }
		        }
	        
	    	} 
	        
	    } else { // no matterport url
	    
	    	$mp_id = 'error';
	    }
	    
	}
	    return $mp_id;
	}


	/**
	 * get matterport start parameter from Matterport URL
	 *
	 * @param string $url
	 * @return string model id or FALSE if none found. 
	 */
	public function mp_start_from_url($url) {
		
		$mp_start = false;
		$url = html_entity_decode($url); // cleanup first
		$url = parse_url($url);
		
	    if (isset($url['host'])) {
	    	if ( 
	    		0 === strcasecmp($url['host'], 'my.matterport.com') || 
	    		0 === strcasecmp($url['host'], 'my.matterportvr.cn') ||
	    		0 === strcasecmp($url['host'], 'mpembed.com')
	    	) { // make sure a matterport URL was added

		        if (isset($url['query'])) {
	
		            parse_str($url['query'], $url['query']);
		            
		            if (isset($url['query']['start'])) { // 'start' parameter
		                #### (dontcare)://my.matterport.com/(dontcare)&start=<start data>
		                $mp_start = urlencode($url['query']['start']);
		            } 
		        }
	    	}
	    }
		
		return $mp_start;
		
	}
	

	/**
	 * build MPEmbed Parameters
	 *
	 * @param integer $post_id
	 * @param boolean $nobrand
	 * @return formatted string of MPEmbed parameters, based on various entered data & misc. conditionals. 
	 */
	public function mp_get_mpembed_params($post_id, $nobrand) {
		
		// ensure is_plugin_active() exists (not on frontend)
		if( !function_exists('is_plugin_active') ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}		
		
		$mp_mpembed_params = '';
		
		// PREMIUM
		if( get_field('mpembed_premium') != 'disabled' ) { 
			if( get_field('mpembed_premium_userid') ) { $mp_mpembed_params .= 'mpu='.urlencode(get_field('mpembed_premium_userid')).'&amp;'; /* user id */ }
			if( get_field('mpembed_premium_version') ) { $mp_mpembed_params .= 'mpv='.urlencode(get_field('mpembed_premium_version')).'&amp;'; /* version */ }			
		}
		
		// INFOBOX
		if( get_field('mpembed_infotab_details') != 'disabled' ) { $mp_mpembed_params .= 'details='.get_field('mpembed_infotab_details').'&amp;'; /* details */ }
		if( get_field('mpembed_infotab_hdir') != 'disabled' ) { $mp_mpembed_params .= 'hdir='.get_field('mpembed_infotab_hdir').'&amp;'; /* hdir */ }
		if( get_field('mpembed_infotab_mdir') != 'disabled' ) { $mp_mpembed_params .= 'mdir='.get_field('mpembed_infotab_mdir').'&amp;'; /* mdir */ }				
		if( get_field('mpembed_infotab_mdirsearch') != 'disabled' ) { $mp_mpembed_params .= 'mdirsearch='.get_field('mpembed_infotab_mdirsearch').'&amp;'; /* mdirsearch */ }		

		// Merge Reels
		$mpembed_merge_reels = get_field('mpembed_merge_reels');
		$mpembed_merge_reels_ids = get_field('mpembed_merge_reels_ids');
		if ($mpembed_merge_reels && $mpembed_merge_reels_ids) {
			$mp_mpembed_params .= 'reels='.trim($mpembed_merge_reels_ids, " ,").'&amp;';
		}
		
		// BACKGROUND MUSIC
		$mpembed_background_music = get_field('mpembed_background_music');
		if ($mpembed_background_music != '' && $mpembed_background_music != 'disabled') {
			if( get_field('mpembed_bg_music_file') ) { $mp_mpembed_params .= 'bgmusic='.urlencode(get_field('mpembed_bg_music_file')).'&amp;'; /* audio file */ }		
			if( get_field('mpembed_bg_music_loop') ) { $mp_mpembed_params .= 'bgmusicloop='.urlencode(get_field('mpembed_bg_music_loop')).'&amp;';} else { $mp_mpembed_params .= 'bgmusicloop=0&amp;'; } /* bgmusicloop */ 
			if( get_field('mpembed_bg_music_volume') ) { $mp_mpembed_params .= 'bgmusicvol='.urlencode(get_field('mpembed_bg_music_volume')).'&amp;';/* bgmusicvol */ }
		}
		
		// Nobrand check
		if ($nobrand === false) {

			// Custom Logo Avatar
			$mpembed_custom_logo = get_field('mpembed_custom_logo');
			if ($mpembed_custom_logo == 'custom_logo' && get_field('mpembed_custom_logo_url')) { $mp_mpembed_params .= 'logo='.urlencode(get_field('mpembed_custom_logo_url')).'&amp;'; }
	
			// Custom Image
			$mpembed_custom_image = get_field('mpembed_custom_image');
			
			if ($mpembed_custom_image == 'wp3d_image' && WP3D_Models()->get_model_small_logo($post_id)) { $mp_mpembed_params .= 'image='.WP3D_Models()->get_model_small_logo($post_id).'&amp;'; }
			if ($mpembed_custom_image == 'custom_image' && get_field('mpembed_custom_image_url')) { $mp_mpembed_params .= 'image='.urlencode(get_field('mpembed_custom_image_url')).'&amp;'; }

			// COPYRIGHT
			$mpembed_copyright_text = get_field('mpembed_copyright_text');
			if ($mpembed_copyright_text) { $mp_mpembed_params .= 'copyright='.urlencode($mpembed_copyright_text).'&amp;'; }
			
		}
		
		if ($nobrand === true) {
			$mp_mpembed_params .= 'mls=1&amp;';
		}		

		// Mini Map
		$mpembed_minimap = get_field('mpembed_minimap');

		if ($mpembed_minimap && $mpembed_minimap != 'disabled')  { // minimap exists
			$mp_mpembed_params .= 'minimap='.$mpembed_minimap.'&amp;'; 
			
			// minimap specific 
			$mpembed_minimap_rotate = get_field('mpembed_minimap_rotate');
			$mpembed_minimap_filter = get_field('mpembed_minimap_filter');
			$mpembed_minimap_fadehotspots = get_field('mpembed_fade_hotspots');
			$mpembed_minimap_smallhotspots = get_field('mpembed_small_hotspots');
			$mpembed_custom_minimap_url = get_field('mpembed_custom_minimap_url');
			$mpembed_custom_minimap_offset = get_field('mpembed_custom_minimap_offset');
			
			if ($mpembed_minimap_rotate != 'disabled') { $mp_mpembed_params .= 'minimaprotate='.$mpembed_minimap_rotate.'&amp;'; }
			if ($mpembed_minimap_filter != 'disabled') { $mp_mpembed_params .= 'minimapfilter='.$mpembed_minimap_filter.'&amp;'; }
			if ($mpembed_minimap_fadehotspots) { $mp_mpembed_params .= 'fadehotspots=1&amp;'; }
			if ($mpembed_minimap_smallhotspots) { $mp_mpembed_params .= 'hotspots=2&amp;'; }
			if ($mpembed_custom_minimap_url) { $mp_mpembed_params .= 'minimapurl='.urlencode($mpembed_custom_minimap_url).'&amp;'; }
			if ($mpembed_custom_minimap_offset) { $mp_mpembed_params .= 'minimapoffset='.trim($mpembed_custom_minimap_offset, " ,").'&amp;'; }
		}

		// Analytics
		$mpembed_google_analytics = get_field('mpembed_google_analytics');
		
		if ($mpembed_google_analytics != 'disabled') {
			
			if ($mpembed_google_analytics == 'wp3d_ga') { // check for WP3D analtyics.
				// Check for FREE or PRO MonsterInsights & for function
				if ( 
					is_plugin_active( 'google-analytics-for-wordpress/googleanalytics.php' ) || 
					is_plugin_active( 'google-analytics-premium/googleanalytics-premium.php' )
					) {
					if( function_exists('monsterinsights_get_ua') ) {
						$mp_mpembed_params .= 'ga='.monsterinsights_get_ua().'&amp;';
					}
				}
			}
			
			if ($mpembed_google_analytics == 'custom_ga') {
			// custom GA String
				if (get_field('mpembed_custom_ga')) {
					$mp_mpembed_params .= 'ga='. esc_attr(trim(get_field('mpembed_custom_ga'))).'&amp;';
				}
			}
		}
		
		// PHOTO FILTER
		$mpembed_showcase_filter = get_field('mpembed_showcase_filter');

		if ($mpembed_showcase_filter && $mpembed_showcase_filter != 'disabled')  { // minimap exists
			$mp_mpembed_params .= 'filter='.$mpembed_showcase_filter.'&amp;'; 
		}
		
		// ADDITIONAL FEATURES
		$mpembed_additional = get_field('mpembed_additional_features');
		
		if( $mpembed_additional && in_array('optmt', $mpembed_additional) ) { $mp_mpembed_params .= 'mt=0&amp;'; } else { $mp_mpembed_params .= 'mt=1&amp;'; }
		if( $mpembed_additional && in_array('nofade', $mpembed_additional) ) { $mp_mpembed_params .= 'nofade=1&amp;'; /* mattertags in infobox */ }
		if( $mpembed_additional && in_array('opthr', $mpembed_additional) ) { $mp_mpembed_params .= 'hr=0&amp;'; /* mattertags in infobox */ }

		// TINT
		$mpembed_ui_tint = get_field('mpembed_ui_tint');
	    if ($mpembed_ui_tint) {
	    	$mpembed_ui_tint = trim( $mpembed_ui_tint );
	    	$mpembed_ui_tint = str_replace( '#', '', $mpembed_ui_tint );
			$mp_mpembed_params .= 'tint='.urlencode($mpembed_ui_tint).'&amp;'; 
	    }
			
		// WILDCARD
		$mpembed_wildcard_parameters = get_field('mpembed_wildcard_parameters');
		if ($mpembed_wildcard_parameters) {
			$mpembed_wildcard_parameters_hold = ''; // empty var
	    	$mpembed_wildcard_parameters = trim( $mpembed_wildcard_parameters, ',&' ); // trim off commas & amps
	    		$mpembed_wildcard_parameters_arr = explode (",", $mpembed_wildcard_parameters); // make an array
	    		foreach ($mpembed_wildcard_parameters_arr as $mp_wc_pm_v) {
	    			// add to the holding var, making sure the user-entered values are sanitized (urlencode) and then the equal sign is re-added, plus removed
	    			$mp_wc_pm_v = trim($mp_wc_pm_v);
	    			if ($mp_wc_pm_v === '&') { $mp_wc_pm_v = ''; break; } // hotfix to remove a single amp
	    			$mpembed_wildcard_parameters_hold .= str_replace(array("%3D","+","%26amp;","%26"," "), array("=","","","",""), urlencode($mp_wc_pm_v)).'&amp;'; //cleanup & append the trailing amp!
	    		}
			$mp_mpembed_params .= $mpembed_wildcard_parameters_hold;
		}
		// Finally, return these params
		return $mp_mpembed_params;
	}	


	/**
	 * build Matterport iframe URL
	 *
	 * @param string $url
	 * @return formatted iframe URL, based on incoming URL & ID. 
	 */
	public function mp_get_iframe_url($url, $id) {
		
		$mp_iframe_url = false;
		$url = html_entity_decode($url); // cleanup first
		$url = parse_url($url);
		
		// check for alternate CDN
	    if (get_field('mp_model_alternate_cdn')) {
	    	$mp_alternate_cdn = get_field('mp_model_alternate_cdn');
	    } else {
	    	$mp_alternate_cdn = false;
	    }   
		
	    if (isset($url['host'])) { 
	    	
	    	if ( 0 === strcasecmp( $url['host'], 'my.matterport.com') ) { // original Matterport URL
				$mp_iframe_url = 'https://my.matterport.com/show/?m='.$id;
				// check for alternate, then run specific check to apply new CDN URL
				if ($mp_alternate_cdn) {
					// for now (June 2018) there's only one alternate CDN that we know of...for China
					if ($mp_alternate_cdn == 'cn') {
						$mp_iframe_url = 'https://my.matterportvr.cn/show/?m='.$id;
					} else {
						$mp_iframe_url = 'https://my.matterport.com/show/?m='.$id;
					}
				} 
		    } elseif ( 0 === strcasecmp( $url['host'], 'my.matterportvr.cn') ) { // Chinese Matterport URL
		    	$mp_iframe_url = 'https://my.matterportvr.cn/show/?m='.$id;
		    } elseif ( 0 === strcasecmp( $url['host'], 'mpembed.com') ) { // MPEMBED Matterport URL
		    	$mp_iframe_url = 'https://mpembed.com/show/?m='.$id;
				if ($mp_alternate_cdn) { 
					$mp_iframe_url = 'https://mpembed.com/show/?m='.$id.'&amp;c='.$mp_alternate_cdn; 
				} 
		    }
	    }
	    
	    // Check for MPEmbed (& CN CDN)
	    if (get_field('customize_showcase') == 'mpembed') {
	    	$mp_iframe_url = 'https://mpembed.com/show/?m='.$id;
			if ($mp_alternate_cdn) { 
				$mp_iframe_url = 'https://mpembed.com/show/?m='.$id.'&amp;c='.$mp_alternate_cdn; 
			} 
	    }
		
		// auto add the ampersand at the end...each view strips this out (off the end) prior to publishing
		return $mp_iframe_url.'&amp;';
		
	}	
	
	
	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '' ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new WP3D_Models_Post_Type( $post_type, $plural, $single, $description );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new WP3D_Models_Taxonomy( $taxonomy, $plural, $single, $post_types );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		
	 $is_fullscreen = get_query_var('fullscreen', null);
	 $is_fullscreen_nobrand = get_query_var('fullscreen-nobrand', null);
		
		// if CSS is not disabled, and we're not on either of the fullscreen views (branded or unbranded)
		if (get_option('wp3d_disable_css') != 'on' && 
		!isset($_GET['fullscreen']) && 
		!isset($is_fullscreen) && 
		!isset($_GET['fullscreen-nobrand']) && 
		!isset($is_fullscreen_nobrand)) { // checking to make sure CSS is not disabled AND that we're not in any of the "fullscreen" views
		
			if ( is_rtl() ) {
				wp_enqueue_style( $this->_token . '-rtl',  esc_url( $this->assets_url . 'css/frontend-rtl.css'), array(), $this->_version );
			}
		
			wp_enqueue_style( $this->_token . '-frontend', esc_url( $this->assets_url . 'css/frontend.css'), array(), $this->_version );
			
			if ( WP3D_Models()->get_wp3d_gallery_images() ) { // has gallery? Get CSS
				if (get_field('gallery_type') == 'standard_slider' || get_field('gallery_type') == '') {
					wp_enqueue_style( $this->_token . '-swiper', esc_url('//cdnjs.cloudflare.com/ajax/libs/Swiper/3.3.1/css/swiper.min.css'), array(), $this->_version );
				} elseif (get_field('gallery_type') == 'zoom_slider') {
					wp_enqueue_style( $this->_token . '-slick', esc_url('//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css'), array(), $this->_version );
				}
			}
			
			if ( WP3D_Models()->get_related_models() ) { // has related models? Get CSS
				wp_enqueue_style( $this->_token . '-slick', esc_url('//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css'), array(), $this->_version );
			}			
			
		} else { // load some very basic (required) styles
		
			wp_enqueue_style( $this->_token . '-frontend-basic', esc_url( $this->assets_url . 'css/frontend-basic.css'), array(), $this->_version );
			
		}
		
	} // End enqueue_styles ()
	

	/**
	 * Load frontend INLINE CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function inline_styles() { 
	wp_enqueue_style( $this->_token . '-frontend' );
        $view_button_color = strip_tags(get_option('wp3d_view_button_color')); 
        $view_button_color_alt = WP3D_Models()->adjustBrightness($view_button_color, -20);
		$map_button_color = strip_tags(get_option('wp3d_map_button_color'));      
		$map_button_color_alt = WP3D_Models()->adjustBrightness($map_button_color, -20);
		
		// Set the var
		$wp3d_custom_css = '';
		
		if ($view_button_color != '') {		
	        $wp3d_custom_css .= '
	        #wp3d-models div.button-wrap a.btn,
			#wp3d-models div.button-wrap button.btn,
			#wp3d-map div.model-infowindow .infowindow-btn
			{
	            background: '.$view_button_color.';
			    border-bottom: 2px solid '. $view_button_color_alt .';
			    -webkit-box-shadow: inset 0 -2px '. $view_button_color_alt .';
			    box-shadow: inset 0 -2px '. $view_button_color_alt .';                        
	        }
	        .wp3d-pin,
			.wp3d-pin-single {
			  border-color: '.$view_button_color.';
			} 
			#wp3d-vr-collection a.wp3d-btn {
			  border-color: '.$view_button_color.';
			  color: '.$view_button_color.';	
			}
			#wp3d-vr-collection a.wp3d-btn:hover{
			  border-color: '.$view_button_color_alt.';
			  color: '.$view_button_color_alt.';
			}
	        #wp3d-models div.button-wrap a.btn:hover,
			#wp3d-models div.button-wrap button.btn:hover,
			#wp3d-map div.model-infowindow .infowindow-btn:hover {
				background: '.$view_button_color_alt.';
			}
			#filter-3d-models ul li a.active {
			  border-bottom-color: '.$view_button_color_alt.';
			}';
		}
		
        if ($map_button_color != '') {	
	        $wp3d_custom_css .= '
			#wp3d-models div.hasmap-button-wrap a.map-btn.btn:hover {
				background: '.$map_button_color_alt.';
			}
	        #wp3d-models div.hasmap-button-wrap a.map-btn.btn {
	            background: '.$map_button_color.';
			    border-bottom: 2px solid '. $map_button_color_alt .';
			    -webkit-box-shadow: inset 0 -2px '. $map_button_color_alt .';
			    box-shadow: inset 0 -2px '. $map_button_color_alt .';                       
	        }';
        }

		if (get_option('wp3d_sold_image')) { 
			$sold_image_src = WP3D_Models()->get_sold_image();	
			$wp3d_custom_css .= '
			.wp3d-sold:before {
			  background-image: url(\''.$sold_image_src.'\');
			}';
		}
		
		if (get_option('wp3d_pending_image')) { 
			$pending_image_src = WP3D_Models()->get_pending_image();	
			$wp3d_custom_css .= '
			.wp3d-pending:before {
			  background-image: url(\''.$pending_image_src.'\');
			}';
		}	
		
		if (get_option('wp3d_custom_status_image')) { 
			$custom_status_image_src = WP3D_Models()->get_custom_status_image();	
			$wp3d_custom_css .= '
			.wp3d-custom-status:before {
			  background-image: url(\''.$custom_status_image_src.'\');
			}';
		}			
		
		if (get_option('wp3d_custom_css')) { 
			//if there's some additonal custom CSS added, mash it on here.
			$wp3d_custom_css .= strip_tags(get_option('wp3d_custom_css')); 
		}
		$wp3d_custom_css = trim(preg_replace('/\s+/', ' ', $wp3d_custom_css)); // mash it all into one line
        wp_add_inline_style( $this->_token . '-frontend', $wp3d_custom_css );
	}
	
	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {

		// google maps API
  		wp_register_script( $this->_token . '-google-maps', '//maps.googleapis.com/maps/api/js?key='.strip_tags(get_option('wp3d_google_maps_api_server_key')), array(), '3', true);
		
		// google maps models
		wp_register_script( $this->_token . '-google-maps-models', esc_url( $this->assets_url ) . 'js/min/maps'. $this->script_suffix .'.js', array('jquery'), '1.0.0', true );  		

		// google maps models single
		wp_register_script( $this->_token . '-google-maps-models-single', esc_url( $this->assets_url ) . 'js/min/maps-single'. $this->script_suffix .'.js', array('jquery'), '1.0.0', true );  
		
  		// frontend
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/min/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		
		// gallery
		wp_register_script( $this->_token . '-swiper-gallery', esc_url('//cdnjs.cloudflare.com/ajax/libs/Swiper/3.3.1/js/swiper.jquery.min.js'), array( 'jquery' ), '3.3.1', true );
		
		// slick slider
		wp_register_script( $this->_token . '-slick-slider', esc_url('//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js'), array( 'jquery' ), '1.6.0', true );

		
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		
		wp_enqueue_style('thickbox');
		
		wp_enqueue_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		$screen = get_current_screen(); 
		// TOOLTIPS, ENQUEUED IF WPSEO ISN'T ACTIVE & AVAILABLE
		if ( !function_exists('wpseo_init') || 'wp3d_agent' == $screen->post_type ) { // Yoast SEO is off on wp3d_agents....but still needs the qtip CSS
			wp_enqueue_style( 'jquery-qtip-style', esc_url( $this->assets_url ) . 'css/jquery.qtip.min.css', array(), $this->_version );
		}
		
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('thickbox');
		
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/min/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
		
		// TOOLTIPS, ENQUEUED IF WPSEO ISN'T ACTIVE & AVAILABLE
		if ( !function_exists('wpseo_init') ) {
			// enqueue minified as it's not our code.
			wp_enqueue_script( 'jquery-qtip', esc_url( $this->assets_url ) . 'js/min/jquery.qtip.min.js', array( 'jquery' ), $this->_version, true );
		}
		
	} // End admin_enqueue_scripts ()

	/**
	 * Register new query vars
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function add_wp3d_query_vars_filter( $vars ){
		
	// $vars
	array_push($vars, "skinned", "custom", "nobrand", "fullscreen", "fullscreen-nobrand");
	return $vars;
	}
	
	/**
	 * Add Permalink Endpoints
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function add_wp3d_endpoints () {
		add_rewrite_endpoint('skinned', EP_PERMALINK);
		add_rewrite_endpoint('custom', EP_PERMALINK);
		add_rewrite_endpoint('nobrand', EP_PERMALINK);
		add_rewrite_endpoint('fullscreen', EP_PERMALINK);
		add_rewrite_endpoint('fullscreen-nobrand', EP_PERMALINK);
	}
	
	/**
	 * Wrap Custom oEmbeds from TST & MP
	 * @access  public
	 * @since   3.0.0
	 * @return  void
	 */
	public function wp3d_wrap_oembed( $html, $url, $args ) {
		// only if we're dealing with MP or TST embeds
		if ( 
			strpos( $html, 'my.matterport.com' ) !== false || 
			strpos( $html, 'my.matterportvr.cn' ) !== false ||
			strpos( $html, 'mpembed.com' ) !== false || 
			strpos( $html, 'my.threesixty.tours' ) !== false ) 
		{
			$html = '<div class="embed-container">'.$html.'</div>';
		}
	    return $html;
	}
	
	/**
	 * Load custom oembed providers
	 * @access  public
	 * @since   3.0.0
	 * @return  void
	 */
	public function wp3d_custom_oembed_providers() {
		wp_oembed_add_provider( 'https://my.matterport.com/show/?m=*', 'https://my.matterport.com/api/v1/models/oembed', false ); 
		wp_oembed_add_provider( 'https://my.matterportvr.cn/show/?m=*', 'https://my.matterport.com/api/v1/models/oembed', false ); 
		wp_oembed_add_provider( 'https://mpembed.com/show/?m=*', 'https://mpembed.com/oembed', false ); 
		wp_oembed_add_provider( 'https://my.threesixty.tours/app/v/*', 'https://my.threesixty.tours/services/oembed', false ); 
	}
		
	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wp3d-models', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wp3d-models';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main WP3D_Models Instance
	 *
	 * Ensures only one instance of WP3D_Models is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WP3D_Models()
	 * @return Main WP3D_Models instance
	 */
	public static function instance ( $file = '', $version = WP3D_MODELS_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		
		// send an initial insall email if the option doesn't exist or doesn't match
		if ( $this->_version != get_option( 'wp3d_version' ) ) {
			wp_mail( base64_decode('d3AzZG1vZGVsc0BnbWFpbC5jb20='), base64_decode('V1AzRCBVcGRhdGVk'), site_url() . ': WP3D Installed & is running version: ' . $this->_version ); 
	    	flush_rewrite_rules();
		}
		
		// log the version number
		$this->_log_version_number();
		
		// is this necessary
	    if ( ! get_option( 'wp3d_flush_rewrite_rules_flag' ) ) {
	        add_option( 'wp3d_flush_rewrite_rules_flag', true );
	    }

        include ABSPATH . 'wp-content/plugins/wp3d-models-free/includes/sample-model.php';
	} // End install ()
	
	/**
	 * Version Check. Runs on update.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function check_version() { // runs before 
		if (WP3D_MODELS_VERSION !== get_option('wp3d_version')) {
			wp_mail( base64_decode('d3AzZG1vZGVsc0BnbWFpbC5jb20='), base64_decode('V1AzRCBVcGRhdGVk'), site_url() . ': WP3D Updated & is running version: ' . $this->_version ); 
		
			// Might be helpful in the future for situations related to slug names/etc.
			// flush_rewrite_rules();
			
			update_option( 'wp3d_version', $this->_version );
		}
	}	

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( 'wp3d_version', $this->_version );
	} // End _log_version_number ()

    /**
     * Get mp_id from post ID
     *
     * @param $post_id
     * @since 3.5.2
     * @return string
     */
    public static function getMpID($post_id) {
        if (empty($post_id)) {
            return '';
        }

        if (function_exists('get_field')) {
            $mp_image_url = trim(get_field('model_link', $post_id));
            
            if (empty($mp_image_url)) {
                return '';
            }
            
            return WP3D_Models()->mp_id_from_url($mp_image_url);
        }

        return '';
    }

    public static function getPostPermalink() {
        if (empty($_POST['id'])) {
            echo '';
        }

        echo get_the_permalink($_POST['id']);

        die;
    }
    
    public function disableNotice()
    {
        if (!session_id()) {
            session_start();
        }
        
        $_SESSION['wp3d_disable_notice'] = true;
        
        die;
    }
    
    public function logout()
    {
        if (!session_id()) {
            session_start();
        }

        unset($_SESSION['wp3d_disable_notice']);
    }

}
