<?php
/**
 * The template for displaying all model "single" posts (fullscreen)
 *
 */
 
remove_action( 'wp_head', 'rsd_link');
remove_action( 'wp_head', 'wp_shortlink_wp_head');
remove_action( 'wp_head', 'wlwmanifest_link');
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 ); 
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_theme_support( 'custom-background');

// Can likely go away
// WP3D_Models()->apply_remove_actions();

// BUFFERING TO STRIP ALL (POTENTIALLY CONFLICTING) THEME JS FROM HEADER/FOOTER

function wp3d_end_ob_header_callback($buffer) {
$new_buffer = '';
$header_arr = explode("\n", $buffer);
foreach ($header_arr as $header_line) {
    // the header is more challenging because we still need SEO/Analytics code, so stripping out makes more sense
    // if any of the following match, strip that entire line from the final output buffer
    if (strpos($header_line,'/wp-content/themes/') == false && strpos($header_line,'/wp-content/plugins/') == false) {
        $new_buffer .= $header_line."\n";
    }
}
$mod_buffer = "<!-- header buffer start -->"."\n";
$mod_buffer .= $new_buffer;
$mod_buffer .= "<!-- header buffer end -->";
return $mod_buffer;
}

function wp3d_end_ob_footer_callback($buffer) {
$new_buffer = '';
$footer_arr = explode("\n", $buffer);
    foreach ($footer_arr as $footer_line) {
        // if any of the following match, add to the final output buffer
        if (strpos($footer_line,'/wp-content/plugins/wp3d-models-free/assets/') == true || strpos($footer_line,'maps.googleapis.com') == true) {
        $new_buffer .= $footer_line."\n";
    }
}
$mod_buffer = "<!-- footer buffer start -->"."\n";
$mod_buffer .= $new_buffer;
$mod_buffer .= "<!-- footer buffer end -->";
return $mod_buffer;
}

add_action('wp_head', 'wp3d_start_header_ob', 1);
function wp3d_start_header_ob() {
    ob_start("wp3d_end_ob_header_callback");
}

add_action('wp_head', 'wp3d_end_header_ob', 1000);
function wp3d_end_header_ob() {
    ob_end_flush();
}

add_action('wp_footer', 'wp3d_start_footer_ob', 1);
function wp3d_start_footer_ob() {
    ob_start("wp3d_end_ob_footer_callback");
}

add_action('wp_footer', 'wp3d_end_footer_ob', 1000);
function wp3d_end_footer_ob() {
    ob_end_flush();
}

// END BUFFERING 

function wp3d_excerpt_more( $more ) {
	return ' <a class="read-more" href="'. get_permalink( get_the_ID() ) . '" target="_blank">' . __('...', 'wp3d-models') . '</a>';
}
add_filter( 'excerpt_more', 'wp3d_excerpt_more' );
 
?><!doctype html>

<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<?php 
		// Additionally, we don't want search engines picking up this version of the page.  Setting canonical.
		?>
		<title><?php the_title(); ?></title>
		<?php /* <link rel="canonical" href="<?php the_permalink(); ?>" /> */ ?>

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		
		<?php
		
		if (post_password_required()) { $has_password = true; } else { $has_password = false; } // checking for a password
		if ( function_exists('get_field') ) { // checking for ACF function 'get_field'
		
			// PLUGINS DIR
			$plugins_url = plugins_url();
			
			// DEFAULT INTRO STATEMENT
			$default_intro_statement = __('START TOUR', 'wp3d-models');			
			
			// LET'S GET SOME VARS
			$has_acf = true; // HAS ACF!  We'll use this later
			$wp3d_view_link = false; // also set to false to start things off
			
			// MODEL LINK
			// if this file is being required by the "fullscreen nobrand" template....then set to "unbranded" and move on, otherwise...get the value from the model
			if (isset($fullscreen_nobrand)) {
				$wp3d_view_link = esc_url(get_permalink().'nobrand/');
				$showcase_type = 'intro-unbranded'; 
			} else {
				$wp3d_view_link = WP3D_Models()->get_model_link(get_the_ID());
				// showcase type(s) = 'stock','intro-unbranded','intro-branded','intro-cobranded'
				$showcase_type = get_field('showcase_branding');
			}
			
			// BUTTON COLORS 
			$button_color = strip_tags(get_option('wp3d_view_button_color')); 
	        $button_color_alt = WP3D_Models()->adjustBrightness($button_color, -20);
			
			// DON'T LINK FULLSCREEN "MORE" TO ITSELF
			$findme   = '/fullscreen';
			$pos = strpos($wp3d_view_link, $findme);
			if ($pos !== false) { $wp3d_view_link = esc_url(get_permalink()); } // if get_model_link is "fullscreen", force the link to "standard"
			
			// Default Schema Type
			$content_schema_type = __('MediaObject', 'wp3d-models'); 
			
			// ########### INTRO CHECK ########### 
			$intro_arr = array('intro-unbranded', 'intro-presented', 'intro-branded', 'intro-cobranded');
			$branding_arr = array('intro-branded', 'intro-cobranded');
			$primary_logo_set = false;
			$small_logo_set = false;
			
			// ########### AV CHECK ########### 
			$has_av_description_content = false;
			
			// ########### MODEL TYPE CHECK ########### 
			$is_static_image = false;
			
			$wp3d_model_type = get_field('wp3d_model_type');  
			// static_image | threesixtytours | video | generic | matterport
			if ($wp3d_model_type == "static_image") { 
				$is_static_image = true;
			} 
			if ($wp3d_model_type == '') { // older Models did not have this value set
			    $wp3d_model_type = 'matterport';
			}
			
			// PLAY BUTTON
			$play_button_html = array(
			  'i' => array( 'class' => array() ),
			  'img' => array( 'class' => array(), 'src' => array() ),
			);
			$play_button_tag = '<i class="fa fa-play-circle"></i>';			
			
			// Branding checks/flags
			if (get_option('wp3d_disable_nobrand_links') && isset($fullscreen_nobrand)) {
				$mp_global_disable_links = true;
			} else { 
				$mp_global_disable_links = false;
			}			
			
			// Calling the 'mp-intro-size' (1920 x 1080) in case the image that was uploaded was bigger
			$intro_src_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'mp-intro-size' );
	        $intro_src = $intro_src_arr[0];	
			
			if (in_array($showcase_type, $intro_arr) || $is_static_image) {
				$is_introed = true;
				$close_overlay_label = __('Close Overlay Content', 'wp3d-models');
				
				// Calling the 'mp-intro-size' (1920 x 1080) in case the image that was uploaded was bigger
				// $mp_src_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'mp-intro-size' );
				// $mp_src = $mp_src_arr[0];	
		        
				// play button only
				if (get_field('play_button_only')) {
					$play_button_only = true;
				} else {
					$play_button_only = false;
				}
				
				// custom copyright
				if (get_field('custom_copyright')) {
					$custom_copyright = true;
				} else {
					$custom_copyright = false;
				}
				
				// audio/visual description content
				// This can only be enabled via filter & add-on
				$has_av_description_content = apply_filters( 'wp3d_add_av_description_content', $has_av_description_content );
				
				// disable header bar
				if (get_field('disable_header_bar') && $has_av_description_content == false) { // can't be disabled if AV content is present
					$disable_header_bar = true;
					$header_bar_class = " header-bar-disabled";
				} else {
					$disable_header_bar = false;
					$header_bar_class = "";
				}
				
		        // global sharing check (applies to branded or unbranded)		
				if (get_option('wp3d_enable_sharing')) {
					$global_sharing_enabled = true;
				} else {
					$global_sharing_enabled = false;
				}		
				
				// single sharing status
				if (get_field('disable_model_sharing')) { 
					$single_sharing_enabled = false;
				} else {
					$single_sharing_enabled = true;
				}
		
		        // sharing check (applies to branded or unbranded)		
				if ($global_sharing_enabled && $single_sharing_enabled) {
					$sharing_enabled = true;
				} else {
					$sharing_enabled = false;
				}
				
				// single connect status
				if (get_field('disable_model_connect')) { 
					$single_connect_enabled = false;
				} else {
					$single_connect_enabled = true;
				}				
				
				// cobrand check
				if ($showcase_type == 'intro-cobranded') {
					$is_cobranded = true;
				} else {
					$is_cobranded = false;
				}
		        
			} else {
				$is_introed = false;
				$sharing_enabled = false;
				$is_cobranded = false;
				$has_profiles = false;
			}
			
			// ########### BRANDING CHECK ########### 
			
			// OVERRIDE LOGO CHECK
			if (get_field('add_override_logos')) { 
				$has_override_logos = true; 
			} else { 
				$has_override_logos = false;
			}
			
			if (in_array($showcase_type, $branding_arr)) {
				$is_branded = true;

				// LARGE/PRIMARY LOGO 
				$large_logo_src = WP3D_Models()->get_model_large_logo(get_the_ID()); 
					if ($large_logo_src) {
						$primary_logo_set = true;
					} 
				
				// SMALL LOGO 
				$small_logo_src = WP3D_Models()->get_model_small_logo(get_the_ID()); 
					if ($small_logo_src) {
						$small_logo_set = true;
					}				
		        
		        // OVERLAY LOGO CHECK
				if (get_field('logo_overlay') && $small_logo_set) { // are we overlaying a logo
					$overlay_logo_set = true;
				} else {
					$overlay_logo_set = false;
				}

				// company URL checks
				$company_urls = array();
				if (get_option('wp3d_company_website')) { $company_urls['website'] = get_option('wp3d_company_website'); }
				if (get_option('wp3d_company_facebook')) { $company_urls['facebook'] = get_option('wp3d_company_facebook'); }
				if (get_option('wp3d_company_twitter')) { $company_urls['twitter'] = get_option('wp3d_company_twitter'); }
				if (get_option('wp3d_company_linkedin')) { $company_urls['linkedin'] = get_option('wp3d_company_linkedin'); }
				if (get_option('wp3d_company_youtube')) { $company_urls['youtube'] = get_option('wp3d_company_youtube'); }
				if (get_option('wp3d_company_vimeo')) { $company_urls['vimeo'] = get_option('wp3d_company_vimeo'); }
				if (get_option('wp3d_company_pinterest')) { $company_urls['pinterest'] = get_option('wp3d_company_pinterest'); }
				if (get_option('wp3d_company_instagram')) { $company_urls['instagram'] = get_option('wp3d_company_instagram'); }
				if (get_option('wp3d_company_google')) { $company_urls['google'] = get_option('wp3d_company_google'); }
				
				if (!empty($company_urls)) {
					$has_profiles = true;
				} else {
					$has_profiles = false;
				}
				
				// custom title check 
				if (get_option('wp3d_custom_title')) {
					$model_title = get_option('wp3d_custom_title');
				} else {
					$model_title = get_bloginfo('name');
				}	
					
			} else {
				$is_branded = false;
				$overlay_logo_set = false;
			}
			
			// description check
			if (get_the_content() != '' || isset($api_data['summary'])) {
				$has_description = true;
			} else {
				$has_description = false;
			}
			
			// agents check
			if (get_field('model_contact_information') == 'add_associated_agents') { // Local Data
				$has_agents = true;
				$agents_arr = WP3D_Models()->get_associated_agents();
			} elseif (get_field('model_contact_information') == 'matterport_contact')  {  // Matterport Data
			    $has_agents = true;
			    $agents_arr = WP3D_Models()->get_mp_contact($api_data);
			} else { 
			    $has_agents = false;
			    $agents_arr = false;
			}			
			
			// sold status check
			$sold_image_src = WP3D_Models()->get_sold_image();
			$pending_image_src = WP3D_Models()->get_pending_image();
			$custom_status_image_src = WP3D_Models()->get_custom_status_image();    

			// MODEL STATUS 
			$model_status = get_field('model_status');
			if ($model_status) {
			    
			    if ($model_status == 'sold') { 
			        $status_class = ' wp3d-sold';
			    } elseif ($model_status == 'pending') { 
			        $status_class = ' wp3d-pending'; 
			    } elseif ($model_status == 'custom') {
			        $status_class = ' wp3d-custom-status';
			    } else {
			        $status_class = '';
			    }
			    
			// SOLD (LEGACY)
			} else {
			
			    if (get_field('mark_sold')) { 
			        $status_class = ' wp3d-sold';
			    } elseif (get_field('mark_pending')) {
			        $status_class = ' wp3d-pending';    
			    } else {  
			        $status_class = ''; 
			    }
			
			}

			$wp3d_preload = get_field('preload_model');
			
			// // ########### NOBRAND ########### (via 'single-model-fullscreen-nobrand.php' usage/require)
			if (isset($fullscreen_nobrand)) { 
				$wp3d_nobranding = true; 
			} else { 
				$wp3d_nobranding = false; 
			} 			
				
			// ########### PRELOAD ########### 
			$wp3d_preload = get_field('preload_model');
			if ($wp3d_model_type == "video") { $wp3d_preload = false; } // force false preload on video
						
// MATTERPORT MODEL
if ($wp3d_model_type == 'matterport') { 			

		// get the ID!
		$mp_incoming = trim(get_field('model_link'));
		$mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
		$mp_start = WP3D_Models()->mp_start_from_url($mp_incoming); // potential deep-link start
		
		// Update Intro Text
		$default_intro_statement = __('START TOUR', 'wp3d-models');	
		
		// ########### API DATA ########### 
		$api_data = get_field('_matterport_api_data'); // hidden custom field stores any retrieved Matterport data in an array
			
		// matterport parameter checks
		$mp_autoplay = get_field('model_autoplay');
		$mp_multifloor = get_field('model_hide_multifloor');
		$mp_force_help = get_field('model_force_help');
		$mp_no_showcase_branding = get_field('showcase_no_branding');
		$mp_no_guided_tour_panning = get_field('disable_model_tour_panning');
		$mp_looped_guided_tour = get_field('enable_model_tour_loop');
		$mp_no_guided_tour_path = get_field('disable_model_tour_path');
		$mp_show_highlight_reel = get_field('force_show_highlight_reel');
		$mp_autostart_guided_tour = get_field('autostart_guided_model_tour');
		$mp_disable_mouse_arrows = get_field('disable_space_scroll');
		$mp_disable_vr = get_field('disable_model_vr');
			// Hack to fix MP Bug (no need for VR on an iPad)
			if (strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) { $mp_disable_vr = true; }
		$mp_no_showcase_branding_links = get_field('showcase_no_branding_links');
            // Hack to fix 'null' bug
            if ($mp_no_showcase_branding_links == 'null') { $mp_no_showcase_branding_links = '0'; }		
		$mp_guided_tour_transition = get_field('model_guided_tour_transition');
		$mp_model_zoom = get_field('model_zoom');
        $mp_model_pin = get_field('model_pin');
        $mp_model_portal = get_field('model_portal');		
		$mp_title_panel = get_field('showcase_title_panel');
		$mp_showcase_tour_cta = get_field('showcase_tour_cta');
        $mp_vr_limited_mode = get_field('vr_limited_mode');
        $mp_dollhouse = get_field('dollhouse_view');
        $mp_mattertags = get_field('mattertag_content');
		
		// Quickstart
		$mp_global_quickstart = get_option('wp3d_enable_global_quickstart');
		$mp_enable_quickstart = get_field('enable_model_quickstart');
		if ($mp_global_quickstart && $is_introed) { 
	    	$global_quickstart = true; 
	    } else { 
	    	$global_quickstart = false; 
	    }
		
		// Language params
		$mp_showcase_language = '';
		$mp_default_language = get_option('wp3d_mp_default_lang'); // look for global
		if ($mp_default_language !== 'en') { $mp_showcase_language = $mp_default_language; } // check against English default
		if (get_field('mp_model_lang')) { $mp_showcase_language = get_field('mp_model_lang'); } // override with Model-specific value if necessary
		
		// Tour Delay
		$guided_tour_seconds = '';
		$guided_tour_seconds_option = (intval(get_option('wp3d_mp_guided_tour_seconds')));
		$guided_tour_seconds_model = get_field('model_guided_tour_seconds'); 
	
			// assign the default value, if it exists
			if ($guided_tour_seconds_option > 1) { // check to see if the global option is set and greater than the default
				$guided_tour_seconds = $guided_tour_seconds_option;
			} 
			// override the default value with a local value, if it exists AND is greater than 1
			if (intval($guided_tour_seconds_model) > 1) { // if this is set && an integer & greater than the default
				$guided_tour_seconds = $guided_tour_seconds_model;	
			} else { // fall back to the default
				$guided_tour_seconds = 1; // set to default
			} 
		
		// Highlight Time
		$showcase_highlight_time = '';
		$showcase_highlight_time_default = 3500;
		$showcase_highlight_time_option = (intval(get_option('wp3d_mp_highlight_time')));
		$showcase_highlight_time_model = get_field('showcase_highlight_time');
		
		
			// assign the default value, if it exists
			if ($showcase_highlight_time_option) { // check to see if the global option is set and greater than the default
				$showcase_highlight_time = $showcase_highlight_time_option;
			} 
			// override the default value with a local value, if it exists AND is an integer
			if (intval($showcase_highlight_time_model)) { // if this is set && an integer & greater than the default
				$showcase_highlight_time = intval($showcase_highlight_time_model);	
			} 			
		
		// URL Params
		if ( $mp_autoplay || 
			 $mp_multifloor || 
			 $mp_force_help || $mp_force_help == 0 || 
			 $wp3d_nobranding || 
			 $mp_no_showcase_branding || 
	     	 $mp_no_guided_tour_panning || 
	     	 $mp_looped_guided_tour || 
	     	 $mp_no_guided_tour_path || 
	     	 $mp_show_highlight_reel || $mp_show_highlight_reel == 0 || 
	     	 $mp_autostart_guided_tour ||
	 		 $mp_disable_mouse_arrows ||
	 		 $mp_enable_quickstart ||
	 		 $mp_disable_vr || 
	 		 $mp_no_showcase_branding_links ||
	 		 $mp_global_disable_links ||     			 
	 		 $showcase_highlight_time ||
	 		 $mp_guided_tour_transition ||
	 		 $mp_model_zoom ||
 			 $mp_model_pin ||
 			 $mp_model_portal ||	 		 
	 		 $mp_title_panel || $mp_title_panel == 0 ||
             $mp_showcase_tour_cta || $mp_showcase_tour_cta == 0 ||
             $mp_vr_limited_mode ||
             $mp_dollhouse ||
             $mp_mattertags ||
	 		 $mp_showcase_language 
	 		 ) { 
				$mp_params = true; 
			} else { 
				$mp_params = false; 
			}
			
		// Build the URL and assemble the PARAMS
		if ($is_introed) {
	    	//$wp3d_iframe_src_url = 'https://my.matterport.com/show/?m='.$mp_id.'&amp;play=1';  
	    	$wp3d_iframe_src_url = WP3D_Models()->mp_get_iframe_url($mp_incoming, $mp_id).'play=1&amp;'; 
		} else {
			//$wp3d_iframe_src_url = 'https://my.matterport.com/show/?m='.$mp_id;
			$wp3d_iframe_src_url = WP3D_Models()->mp_get_iframe_url($mp_incoming, $mp_id);
		}
		
		// Additional Param Check
	    //if ($mp_params){ $wp3d_iframe_src_url .= '&amp;'; }
	    
	    // Branding & No-Link Check (VR is also disabled)
	    if ($wp3d_nobranding || $mp_no_showcase_branding) { 
	    	$wp3d_iframe_src_url .= 'brand=0&amp;vr=0&amp;'; 
	    } else {
	    	$wp3d_iframe_src_url .= 'brand=1&amp;';
				// test for VR if unbranded
				if ($mp_disable_vr) { $wp3d_iframe_src_url .=  'vr=0&amp;'; }	    	
	    }
	
		// Branding No-Link Check
		// Slightly more sophisticated now to accommodate the additional 'mls' parameter value of '2'
		if ($mp_no_showcase_branding_links || $mp_global_disable_links) { 
			if ($mp_no_showcase_branding_links == '2') {
				$wp3d_iframe_src_url .= 'mls=2&amp;'; 
			} else {
				$wp3d_iframe_src_url .= 'mls=1&amp;';
			}
		}
	
	    // Quickstart Check (only added if there is no "HELP")
	    if ($mp_enable_quickstart || $global_quickstart) { // user wants quickstart
	    	if (!$mp_force_help) { // No sign of HELP, go ahead and add the quickstart
	    		$wp3d_iframe_src_url .=  'qs=1&amp;'; 
	    	}
	    }  
	
		// All other Params
		if ($mp_autoplay && !$is_introed) { $wp3d_iframe_src_url .= 'play=1&amp;'; } 
		if ($mp_multifloor) { $wp3d_iframe_src_url .=  'f=0&amp;'; }
	    if ($mp_no_guided_tour_panning) { $wp3d_iframe_src_url .=  'kb=0&amp;'; }
	    if ($mp_looped_guided_tour) { $wp3d_iframe_src_url .=  'lp=1&amp;'; }
	    if ($mp_no_guided_tour_path) { $wp3d_iframe_src_url .=  'guides=0&amp;'; }
	    if ($mp_show_highlight_reel != '') { $wp3d_iframe_src_url .=  'hl='.$mp_show_highlight_reel.'&amp;'; }
	    if ($mp_autostart_guided_tour) { $wp3d_iframe_src_url .=  'ts='.$guided_tour_seconds.'&amp;'; }
	    if ($mp_force_help != '') { $wp3d_iframe_src_url .= 'help='.$mp_force_help.'&amp;'; }
	    if ($mp_disable_mouse_arrows) { $wp3d_iframe_src_url .=  'wh=0&amp;'; } 
	 	if ($showcase_highlight_time) { $wp3d_iframe_src_url .=  'st='.$showcase_highlight_time.'&amp;'; }
	 	if ($mp_guided_tour_transition) { $wp3d_iframe_src_url .=  'mf=0&amp;'; }
	 	if ($mp_model_zoom) { $wp3d_iframe_src_url .=  'nozoom=1&amp;'; }	
        if ($mp_model_pin) { $wp3d_iframe_src_url .=  'pin=0&amp;'; } 
        if ($mp_model_portal) { $wp3d_iframe_src_url .=  'portal=0&amp;'; } 	 	
	 	if ($mp_title_panel != '') { $wp3d_iframe_src_url .=  'title='.$mp_title_panel.'&amp;'; }
	    if ($mp_showcase_tour_cta != '') { $wp3d_iframe_src_url .=  'tourcta='.$mp_showcase_tour_cta.'&amp;'; }
        if ($mp_vr_limited_mode != '') { $wp3d_iframe_src_url .=  'vrcoll='.$mp_vr_limited_mode.'&amp;'; }
        if ($mp_dollhouse != '') { $wp3d_iframe_src_url .=  'dh='.$mp_dollhouse.'&amp;'; }
        if ($mp_mattertags != '') { $wp3d_iframe_src_url .=  'mt='.$mp_mattertags.'&amp;'; }
	
	    // Language
	    if ($mp_showcase_language) { $wp3d_iframe_src_url .=  'lang='.$mp_showcase_language.'&amp;'; }
	    
	    // Custom (deep-link) Start
	    if ($mp_start) { $wp3d_iframe_src_url .=  'start='.$mp_start.'&amp;'; }            
	
		// tour highlight overlay logo adjust
		if ($mp_show_highlight_reel != '') {
			$overlay_highlight_class = " has-highlight-reel";
		} else {
			$overlay_highlight_class = '';
		}	
		
        // MPEmbed params gets added last 
        if (get_field('customize_showcase') == 'mpembed') {
            if (isset($fullscreen_nobrand)) {
                $mpembed_nobrand = true;
            } else {
                $mpembed_nobrand = false;
            }

            $wp3d_iframe_src_url .= WP3D_Models()->mp_get_mpembed_params($post->ID, $mpembed_nobrand);
        }
	
	    // Parameter Cleanup (Remove trailing ampersand)
	    $wp3d_iframe_src_url = preg_replace('/&amp;$/', '', $wp3d_iframe_src_url);
	
		// Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;
        $wp3d_id = $mp_id;

} // END MATTERPORT CHECK

// THREESIXTY TOURS MODEL
if ($wp3d_model_type == 'threesixtytours') {		
			
        $tst_incoming = trim(get_field('tst_link'));
        $tst_id = WP3D_Models()->tst_id_from_url($tst_incoming); // array
        
        // When a Model is saved, we look to TST for any saved data and copy it back 
        //$api_data = get_field('_tst_api_data'); // hidden custom field holds retrieved data
        
		// Update Intro Text
		$default_intro_statement = __('START 360 TOUR', 'wp3d-models');	

        $wp3d_iframe_src_url = "https://my.threesixty.tours/app/v/";
        
        // Rebuild the URL
        if ($tst_id['type'] == 'tour') {
            $wp3d_iframe_src_url .= $tst_id['user'].'/'.$tst_id['pano'].'/'.$tst_id['tour'];
        } elseif ($tst_id['type'] == 'pano') {
            $wp3d_iframe_src_url .= $tst_id['user'].'/'.$tst_id['pano'];
        }
        
        // TST parameter checks
        $tst_header = get_field('tst_header');
        $tst_footer = get_field('tst_footer');
        $tst_title = get_field('tst_title');
        $tst_tournav = get_field('tst_tournav');
		$tst_mousewheel = get_field('tst_mousewheel');
        $tst_socialshare = get_field('tst_socialshare');
        $tst_branding = get_field('tst_branding');  
        $tst_startscreen = get_field('tst_startscreen'); 

         //UNLIKE MATTERPORT, WE ATTACH ALL PARAMETERS...SO WE APPEND THE "?" REGARDLESS
        $wp3d_iframe_src_url .= '?'; 
        
        // build the URL
        if ($tst_header) { $wp3d_iframe_src_url .=  'header='.$tst_header.'&amp;'; } else { $wp3d_iframe_src_url .=  'header=transparent&amp;'; }
        if ($tst_footer) { $wp3d_iframe_src_url .=  'footer='.$tst_footer.'&amp;'; } else { $wp3d_iframe_src_url .=  'footer=true&amp;'; }
        if ($tst_title) { $wp3d_iframe_src_url .=  'title='.$tst_title.'&amp;'; } else { $wp3d_iframe_src_url .=  'title=false&amp;'; }   
        if ($tst_tournav) { $wp3d_iframe_src_url .=  'tournav='.$tst_tournav.'&amp;'; } else { $wp3d_iframe_src_url .=  'tournav=delayclose&amp;'; }
        if ($tst_mousewheel ) { $wp3d_iframe_src_url .=  'mousewheel='.$tst_mousewheel.'&amp;'; } else { $wp3d_iframe_src_url .=  'mousewheel=true&amp;'; }
        if ($tst_socialshare) { $wp3d_iframe_src_url .=  'socialshare='.$tst_socialshare.'&amp;'; } else { $wp3d_iframe_src_url .=  'socialshare=false&amp;'; }
        
    	// Branding logic
	    if ($wp3d_nobranding) { 
	    	$wp3d_iframe_src_url .= 'brand=false&amp;'; 
	    } elseif ($tst_branding) { 
	    	$wp3d_iframe_src_url .=  'brand='.$tst_branding.'&amp;'; 
	    } else { 
	    	$wp3d_iframe_src_url .=  'brand=false&amp;'; 
	    }
	    
	    // Startscreen logic
	    // PSV hiccups in modal iframes with no intial size.  Force the startscreen if there's no intro
	    if (!$is_introed) { // no reason to ever add a startscreen for "introed" content
	    	if (isset($_GET['fss']) || $tst_startscreen == "true" ) {
	    		$wp3d_iframe_src_url .=  'startscreen=true&amp;'; 
	    	}
	    } 
	
        // Parameter Cleanup
        $wp3d_iframe_src_url = preg_replace('/&amp;$/', '', $wp3d_iframe_src_url);
        
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url; 
        $wp3d_id = $tst_id;
		
}

// VIDEO MODEL
if ($wp3d_model_type == 'video') { 
	
	// Update Intro Text
	$default_intro_statement = __('START VIDEO TOUR', 'wp3d-models');	
    
    if (get_field('base_video_type') == 'youtube') {
        
        if ($wp3d_nobranding) { // look for the unbranded video first
        	$base_youtube_url = get_field('base_youtube_unbranded_video_link');
        }
         if ($base_youtube_url == '') { // if no unbranded, grab the main
        	$base_youtube_url = get_field('base_youtube_video_link');	
        }
        $wp3d_iframe_src_url = WP3D_Models()->youtube_embed_from_url($base_youtube_url, true).'&amp;autoplay=1';;
        
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;         
        
    } 
    
    if (get_field('base_video_type') == 'vimeo') {
        
        if ($wp3d_nobranding) { // look for the unbranded video first
        	$base_vimeo_url = get_field('base_vimeo_unbranded_video_link');
        } 
        if ($base_vimeo_url == '') { // if no unbranded, grab the main
        	$base_vimeo_url = get_field('base_vimeo_video_link');
        }
        $wp3d_iframe_src_url = WP3D_Models()->vimeo_embed_from_url($base_vimeo_url, true).'&amp;autoplay=1';
        
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;          
        
    }
    
}

// GENERIC MODEL
$allow = '';
if ($wp3d_model_type == 'generic') { 
    $generic_iframe = trim(get_field('generic_iframe'));
    $wp3d_iframe_src_url = WP3D_Models()->get_iframe_src($generic_iframe);
    $allow = WP3D_Models()->get_iframe_allow($generic_iframe);
    if (!empty($allow)) {
        $allow = '; ' . $allow;
    }
    
    // Variable re-assigning
    $wp3d_iframe_data_src = $wp3d_iframe_src_url;     
    
}

// BODY CLASSES
$wp3d_model_types = get_the_terms($post->ID, 'model-type');
$wp3d_model_clients = get_the_terms($post->ID, 'model-client');
$body_classes = array();

if ($wp3d_model_types) {
  foreach ($wp3d_model_types as $wp3d_model_type) {
    $body_classes[] = $wp3d_model_type->slug;
  }
}

if ($wp3d_model_clients) {
  foreach ($wp3d_model_clients as $wp3d_model_client) {
    $body_classes[] = $wp3d_model_client->slug;
  }
}

$filtered_body_classes = array_unique(array_filter($body_classes)); // cleanup

if (!empty($filtered_body_classes)) {
    $body_taxonomy_classes = implode (" ", $filtered_body_classes);
} else {
    $body_taxonomy_classes = false;
}

$body_taxonomy_classes = apply_filters( 'wp3d_fullscreen_body_taxonomy_classes', $body_taxonomy_classes );


		} else {
			$has_acf = false; // BOO!
		}
		
		?>
		
		<style>
		/* These inline styles apply to this template only - minimal CSS needed - keeping 'er trim!' */
			html {
				height: 100%;
				width: 100%;
				overflow: hidden;
				margin: 0;
			}
			
			body {
				margin: 0;
				padding: 0;
				width: 100%;
    			height: 100%;
			    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;    			
			}
			
			.sr-only {
				position: absolute;
				width: 1px;
				height: 1px;
				margin: -1px;
				padding: 0;
				overflow: hidden;
				clip: rect(0,0,0,0);
				border: 0;
			}
			
			.wp3d-hidden {
				display: none!important;
			}
			
			div.password-protected {
				width: 100%;
				text-align: center;
				padding: 2em;
			}
			
			a {
				color: #999999;
			}
			
			a:hover {
				color: #ECECEC;
			}
			
			.overlay a {
				color: #BBBBBB;
			}
			
			.overlay a:hover {
				color: #FFFFFF;
			}
			
			#wp3d-iframe-wrap, #wp3d-header-branding, #wp3d-intro {
				display: block;
			    position: fixed;
			    top: 0;
			    right: 0;
			    bottom: 0;
			    left: 0;
			    background: #212121;
			    font-size: 15px;
			}

			iframe {
			    width: 100%;
			    height: 100%;
			    min-height: 100%;
			    border: none;
			    display: block;
			    margin: 0;
			}
			
			.embed-container { position: relative; height: 75%; overflow: hidden; max-width: 100%; } 
			.embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
			
			
			#wp3d-header-branding a#description
			{
				float: left;
			}
			
			#wp3d-header-branding a#fullscreen {
				display:none;
				float: right;
				line-height: 38px;
				margin-left: 10px;
				text-decoration: none;
				color: #777777;
				outline: none;				
			}
			
			#wp3d-header-branding a#fullscreen.framed {
				display: inline-block;
			}
			
			.wp3d-sold:before {
		        content: '';
		        display: block;
		        width: 100px;
		        height: 100px;
		        position: absolute;
		        top: 0;
		        right: 0;
		        background-image: url('<?php echo $sold_image_src; ?>');
		        background-size: cover;
		        background-position: 0 0;
		        z-index: 999;
		        pointer-events: none;
   			 }
   			 
			.wp3d-pending:before {
		        content: '';
		        display: block;
		        width: 100px;
		        height: 100px;
		        position: absolute;
		        top: 0;
		        right: 0;
		        background-image: url('<?php echo $pending_image_src; ?>');
		        background-size: cover;
		        background-position: 0 0;
		        z-index: 999;
		        pointer-events: none;
   			 }   
   			 
			.wp3d-custom-status:before {
		        content: '';
		        display: block;
		        width: 100px;
		        height: 100px;
		        position: absolute;
		        top: 0;
		        right: 0;
		        background-image: url('<?php echo $custom_status_image_src; ?>');
		        background-size: cover;
		        background-position: 0 0;
		        z-index: 999;
		        pointer-events: none;
   			 }      			 
   			 
   			 .custom-footer {
				display:none;
			}
			
		/* Phones */
		@media (max-width: 767px) {
				.wp3d-sold:before,
				.wp3d-pending:before,
				.wp3d-custom-status:before {
		        width: 60px;
		        height: 60px;
   			 }
		}
			
<?php // if 'introed', add intro styles 
if ($is_introed) { ?>
			#wp3d-intro,
			#wp3d-iframe-wrap.branded {
				top: 40px;	
			}
			
			#wp3d-intro.header-bar-disabled,
			#wp3d-iframe-wrap.branded.header-bar-disabled {
				top: 0px;	
			}
			
			#wp3d-iframe-wrap .iframe-logo-overlay {
				position: absolute;
				width: auto;
				height: 50px;
				left: 15px;
				bottom: 90px;
			}

			#wp3d-iframe-wrap.has-highlight-reel .iframe-logo-overlay {
				bottom: 220px;
			}
						
			#wp3d-header-branding {
				height: 40px;
			    bottom: auto;
			    color: #888;
			    line-height: 38px;
			    white-space: nowrap;
			    padding: 0 10px 0 5px;
			    overflow: hidden;
			}
			
			#wp3d-header-branding a {
				text-decoration: none;
				color: #777777;
				display: inline-block;
				outline: none;
			}
			
			#wp3d-header-branding a:hover {
				color: #444444;
			}
			
			#wp3d-header-branding #header-brand {
				display: block;
				float: right;
			}
			
			#wp3d-intro {
				z-index: 999;
				background: #212121 url('<?php echo $intro_src; ?>') center center no-repeat;
				background-size: cover;
			}
			
			#wp3d-intro.is-loading *, #wp3d-intro.is-loading *:before, #wp3d-intro.is-loading *:after {
				-moz-animation: none !important;
				-webkit-animation: none !important;
				-ms-animation: none !important;
				animation: none !important;
				-moz-transition: none !important;
				-webkit-transition: none !important;
				-ms-transition: none !important;
				transition: none !important;
			} 			
			
			body.overlaid #wp3d-intro {
				-webkit-filter: blur(10px);
			    -moz-filter: blur(10px);
			    -o-filter: blur(10px);
			    -ms-filter: blur(10px);
			    filter: blur(10px);
			}
			
			body.overlaid #wp3d-header-branding,
			body.overlaid .wp3d-sold:before,
			body.overlaid .wp3d-pending:before {
				display: none;
			}
			
			body.overlaid #wp3d-intro, 
			body.overlaid #wp3d-iframe-wrap.branded {
				top: 0;
			}
			
			#wp3d-intro a {
				display: table;
				width: 100%;
				height: 100%;
				outline: none;
				background-color: rgba(0,0,0,0.25);
				text-decoration: none;
			}
			
			body.overlaid #wp3d-intro a {
				display: none;
			}
			
			#wp3d-intro a.default-cursor {
				cursor: default;
			}
			
			div.wp3d-start {
				display: table-cell;
				vertical-align: middle;
				padding: 0 5%;
				text-align: center;
				font-size: 150px;
				line-height: 1.1;
				color: #FFFFFF;
				text-shadow: -1px 1px 8px #212121, 1px -1px 40px #212121;
				z-index: 1000;
			    opacity: 1;
			    -moz-transform: scale(1);
			    -webkit-transform: scale(1);
			    -ms-transform: scale(1);
			    transform: scale(1);
			    -moz-transition: -moz-transform 0.5s ease, opacity 0.5s ease;
			    -webkit-transition: -webkit-transform 0.5s ease, opacity 0.5s ease;
			    -ms-transition: -ms-transform 0.5s ease, opacity 0.5s ease;
			    transition: transform 0.5s ease, opacity 0.5s ease;
			}

			#wp3d-intro.is-loading div.wp3d-start {
			    -moz-transform: scale(0.95);
			    -webkit-transform: scale(0.95);
			    -ms-transform: scale(0.95);
			    transform: scale(0.95);
			    opacity: 0; 				
			}
			
			body.loaded-delay div.wp3d-start > img,
			body.loaded-delay .overlay-logo-large,
			body.loaded-delay .overlay-logo-small {
				opacity: 1;
			}
			
			div.wp3d-start h1 {
				position: relative;
				text-transform: uppercase;
				letter-spacing: 3px;
				font-size: 40px;
				line-height: 1.1;
				font-weight: normal;
				color:#fff;
				margin: 0 0 15px;
				text-shadow: 1px 2px 2px rgba(0,0,0,.6);
				opacity: 1;
				-moz-transform: scale(1);
				-webkit-transform: scale(1);
				-ms-transform: scale(1);
				transform: scale(1);
				-moz-transition: -moz-transform 0.5s ease, opacity 0.5s ease;
				-webkit-transition: -webkit-transform 0.5s ease, opacity 0.5s ease;
				-ms-transition: -ms-transform 0.5s ease, opacity 0.5s ease;
				transition: transform 0.5s ease, opacity 0.5s ease;
			}
			
		    #wp3d-intro.is-loading div.wp3d-start h1 {
			    -moz-transform: scale(0.95);
			    -webkit-transform: scale(0.95);
			    -ms-transform: scale(0.95);
			    transform: scale(0.95);
			    opacity: 0;    	
		    }
		    
		    div.wp3d-start h1 span {
				position: relative;
				display: inline-block;
				padding: 10px 0;
		    }	
		    	
		    div.wp3d-start h1 span:before,
		    div.wp3d-start h1 span:after {
			    position: absolute;
			    content: '';
			    display: block;
			    height: 2px;
			    width: 100%;	    
			    -moz-transition: width 0.85s ease;
			    -webkit-transition: width 0.85s ease;
			    -ms-transition: width 0.85s ease;
			    transition: width 0.85s ease;
			    -moz-transition-delay: 0.25s;
			    -webkit-transition-delay: 0.25s;
			    -ms-transition-delay: 0.25s;
			    transition-delay: 0.25s;
			    background: #fff;
				-webkit-box-shadow: 1px 2px 2px 0px rgba(0,0,0,0.6);
				-moz-box-shadow: 1px 2px 2px 0px rgba(0,0,0,0.6);
				box-shadow: 1px 2px 2px 0px rgba(0,0,0,0.6);
		    }
		    
		    div.wp3d-start h1 span:before {
				top: 0;
				left: 0;    	
		    }
		    
		    div.wp3d-start h1 span:after {
			    bottom: 0;
			    right: 0;    	
		    }
		    
		    #wp3d-intro.is-loading div.wp3d-start h1 span:before,
		    #wp3d-intro.is-loading div.wp3d-start h1 span:after {
			    width: 0;    	
		    }	    

			.overlay {
				display: none;
				position: fixed;
				background-color: #000000;
				background-color: rgba(0,0,0,0.80);
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				z-index: 1001;
				text-align: center;
				padding: 5%;
				overflow-x: hidden;
				overflow-y: auto;
				-webkit-overflow-scrolling: touch;			      
			}
			
			.overlay .model-summary {
				padding: 3%;
				color: #EFEFEF;
				font-size: 16px;
				line-height: 1.4em;
				max-height: 50%;
				overflow-y: auto;
				background-color: rgba(0,0,0,0.80);
				border: 1px solid #333333;
				border-bottom-width: 0;
				margin-top: 2%;
			}
			
			.overlay .btn {
			    display: block;
			    position: relative;
			    vertical-align: top;
			    height: 40px;
			    line-height: 40px;
			    padding: 0;
			    font-size: 16px;
			    color: white;
			    text-align: center;
			    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
<?php if ($button_color) { // if a color has been set, update this style block ?>
			    background: <?php echo $button_color; ?>;
			    border-bottom: 2px solid <?php echo $button_color_alt; ?>;
			    -webkit-box-shadow: inset 0 -2px <?php echo $button_color_alt; ?>;
			    box-shadow: inset 0 -2px <?php echo $button_color_alt; ?>;  
<?php } else { ?>			  
			    background: #f39c12;
			    border-bottom: 2px solid #e8930c;
			    webkit-box-shadow: inset 0 -2px #e8930c;
			    box-shadow: inset 0 -2px #e8930c;
<?php } ?>
			    border: 0;
			    cursor: pointer;
			    text-decoration: none;
			    webkit-border-radius: 0px;
			    -moz-border-radius: 0px;
			    border-radius: 0px;
		  	 }			
			
			.overlay .btn:hover {
			    outline: none;
			    -webkit-box-shadow: none;
			    box-shadow: none;
<?php if ($button_color) { // if a color has been set, update this style block ?>
			    background: <?php echo $button_color_alt; ?>;
<?php } else { ?>
			    background: #e8930c;
<?php } ?>
		  	 }
		  	 
			.overlay h2 {
				font-size: 20px;
				line-height: 1em;
				padding: 5% 0;
				color: #FFFFFF;
			}
			
			#description-overlay.overlay h2 {
				padding: 4% 0;
			}
			
			.overlay .close-overlay {
				display: block;
				position: absolute;
				top: 10px;
				right: 10px;
				outline: none;
				color: #FFFFFF;
				font-size: 40px;
				line-height: 38px;
				width: 40px;
				height: 40px;
				text-align: center;
			}
			
			.overlay.show {
				display: block;
			}
			
			div.logo {
				display: block;
				width: 200px;
			}
			
			div.message {
				display: block;
				font-size: 15px;
				line-height: 20px;
			}
			
			div.message .presented-by {
				display: block;
			}			
			
			.overlay-logo-large {
				width: 100%;
				margin: 0 auto 15px;
				max-width: 200px;
				height: auto;
				max-height: 200px;
				/*opacity: 0;*/
			}
			
			.overlay-logo-small {
				width: 100%;
				margin: 0 auto 15px;
				max-width: 150px;
				height: auto;
				max-height: 60px;
				/*opacity: 0;*/
			}
			
			div.cobrand {
				display: block;
				position: absolute;
				bottom: 5%;
				right: 5%;
				width: 130px;
			}			
			
			div.cobrand img {
				width: 100%;
				margin: 0 auto;
				max-width: 130px;
				height: auto;
				display: block;
			}
			
			.connect-icons-wrap,
			.wp3d-share-icons-wrap,
			.agents-wrap{
				padding: 0;
			}
			
			.connect-icons-wrap h3,
			.wp3d-share-icons-wrap h3,
			.agents-wrap h3 {
				font-size: 15px;
				color: #FFFFFF;
				padding-bottom: 3%;
				opacity: 0.5;
			}
			
			ul.connect-icons,
			ul.wp3d-share-icons {
				text-align: center;
				margin: 0;
				padding: 0;
			}
			
			ul.connect-icons li,
			ul.wp3d-share-icons li{
				display: inline-block;
			}
			
			ul.connect-icons li a,
			ul.wp3d-share-icons li a {
				color: #FFFFFF;
				padding: 1%;
				outline: none;
				display: inline-block;
			}
			
			/* Agents Contact */
			
			ul.agents-call {
				display: inline-block;
				margin: 0;
				padding: 0;
				list-style: none;
				max-width: 400px;
			}
			
			ul.agents-call a {
				text-decoration: none;
			}
				
			ul.agents-call > li {
				margin: 35px 0 0 0;
				padding: 0;
				width: 100%;
				display: table;
				vertical-align: middle;
			}
			
			ul.agents-call > li .tc {
				display: table-cell;
				vertical-align: middle;
			}
					
			ul.agents-call > li span.fn {
				display: block;
				font-size: 24px;
				line-height: 1.1;
				margin: 10px 0 0 8px;
			}
					
			ul.agents-call > li .agent-photo {
				width: 120px;
				height: 120px;
				margin: 0 auto;
				position: relative;
			    background: #fff;
				overflow: hidden;
			    padding: 5px;
			    border-radius: 50%;
			    border: 1px solid #ddd;
			    z-index: 2;	
			}
			
			ul.agents-call > li .agent-photo .agent-img {
		    	background-position: top center;
		    	background-size: cover;
		    	border-radius: 50%;
		    	height: 100%;
		    }
			
			ul.agents-call > li .agent-contact {
				font-size: 18px;
				color: #fff;
				margin: 0;
			    padding: 20px;
			    text-align: left;
			    width: 100%;
			}
					
			ul.agents-call > li .agent-meta {
				display: inline-block;
				text-align: left;
				margin-top: 15px;
				margin-bottom: 10px;
				font-size: 16px;
			}
	
			ul.agents-call > li .agent-meta > li {
				margin: 0 0 10px 0;
			}
		
			ul.agents-call > li .agent-meta > li .type {
				text-transform: uppercase;
				font-size: 14px;
				font-weight: 300;
				color: #777;
			}
		

			
/* Phones */
@media (max-width: 767px) {
	.overlay-logo-large { display: none; }
	.overlay-logo-small { display: block; }	
	
	div.wp3d-start {
		font-size: 90px;
	}	

	div.wp3d-start.has-small-logo,
	div.wp3d-start.has-primary-logo { 
		font-size: 150px;	
	}
	
	div.wp3d-start h1 {
		font-size: 30px;
	}
	
	.overlay .model-summary {
		line-height: 1.2em;	
		font-size: 13px;
	}
	
	.overlay h2 {
		font-size: 18px;
		padding: 2% 0;
	}
	
	#description-overlay.overlay h2 {
		padding: 2% 0;
	}
	
	#wp3d-header-branding #header-brand {
		font-size: 12px;
		line-height: 40px;
	}
	
	div.cobrand {
    	bottom: 10px;
    	right: 10px;
    	width: 100px;
    }			
    
    div.cobrand img {
    	max-width: 100px;
    }
    
	ul.agents-call > li {
		margin: 20px 0 0 0;
	}
			
	ul.agents-call > li span.fn {
		font-size: 22px;
		margin: 5px 0 0 0;
	}
			
	ul.agents-call > li	.agent-photo {
		padding: 3px;
		width: 54px;
		height: 54px;
	}
	
	ul.agents-call > li	.agent-contact {
		padding: 5px;
	}
			
	ul.agents-call > li	.agent-meta {
		margin-top: 5px;
	}
				
	ul.agents-call > li	.agent-meta	> li {
		margin: 0 0 5px 0;
	}

}


@media (min-width: 320px) and (max-width:767px) {

	/*
	#agents-overlay.overlay {
		padding-top: 0;
	}
	*/
			
	ul.agents-call {
		text-align: center;
		width: 98%;
		max-width: auto;
	}
	
	ul.agents-call > li {
		display: inline-block;
		width: auto;
		min-width: 45%;
		margin: 10px 0 0 0;
	}
			
	ul.agents-call > li	.agent-photo-cell {
		display: none;
	}
			
	ul.agents-call > li	span.fn {
		line-height: 1em;
		margin: 2px 0 0 0;
	}
			
	ul.agents-call > li	.agent-photo {
		padding: 2px;
		width: 48px;
		height: 48px;
	}
	
	ul.agents-call > li	.agent-contact {
		width: auto;
		text-align: center;
		width: 100%;
    	display: block;
	}
			
	ul.agents-call > li	.agent-meta {
		text-align: center;
	}
				
	ul.agents-call > li .agent-meta.fa-ul {
		margin-left: 0;
	}
					
	ul.agents-call > li .agent-meta.fa-ul li i {
		display:none;
	}
				
	ul.agents-call > li .agent-meta	> li {
		margin: 0 0 3px 0;
	}

}


@media (max-width: 481px) {
	
	ul.agents-call > li {
		display: block;
	}
	
	ul.agents-call > li:first-child {
		margin-top: 0;
	}	
	
	ul.agents-call > li	.tc {
		display: block;
	}
			
	ul.agents-call > li	.agent-contact {
		text-align: center;
	}
			
	ul.agents-call > li	.agent-meta {
		text-align: center;
		margin-left: 0;
	}
				
	ul.agents-call > li	.agent-meta	.fa-li {
		position: relative;
		top: auto;
		left: auto;
	}
	
	.overlay ul.agents-call .agent-meta li.email a {
		max-width: 275px;
	    overflow: hidden;
	    display: block;
	}
	
	.overlay ul.agents-call .agent-meta .fa-li {
		display: none;
	}	
	
	div.wp3d-start h1 {
	    letter-spacing: 2px;
	}

}

/* Mobile landscape */
@media (min-width: 481px) and (max-width: 767px) and (orientation: landscape) {
	
	div.wp3d-start h1 {
		font-size: 25px;
		padding: 10px 0 0 0;
	}
	
	div.wp3d-start.has-small-logo,
	div.wp3d-start.has-primary-logo
	{ 
		font-size: 90px;
		line-height: 90px;
	}
	
	div.wp3d-start.has-small-logo h1,
	div.wp3d-start.has-primary-logo h1 {
		padding: 0;
		margin: 0.3em;
	}	

	.connect-icons-wrap,
	.wp3d-share-icons-wrap,
	.agents-wrap {
		padding: 3% 0 0 0;
	}	
	
	div.overlay { padding-top: 5%; }
	
	#agents-overlay.overlay {
		padding-top: 6%;
	}	
	
	#wp3d-header-branding {
		font-size: 14px;
	}
	#description-overlay.overlay h2 {
		padding: 1% 0;
	}
	
	.overlay-logo-small {
	    margin-bottom: 0;
	}
	
}

/* Tablet  */
@media (min-width: 768px) and (max-width: 1023px) {
	.overlay-logo-large { 
		display: block; 
		max-width: 140px;
		max-height: 140px;
	}
	.overlay-logo-small { display: none; }
	
	div.wp3d-start { font-size: 140px; }
}


/* Small Desktop & Tablet */
@media (min-width: 1024px) {
	.overlay-logo-large { display: block; }
	.overlay-logo-small { display: none; }
}

/* Tablet landscape-ish */
@media (min-width: 1024px) and (orientation: landscape) {
	div.overlay { padding: 5% 10%; }
	div.overlay h2 { font-size: 25px; }
}

/* Larger Desktop */
@media (min-width: 1200px) {
	div.overlay h2 { font-size: 30px; }
}

<?php } // end intro check ?>

<?php // if no acf, add alert styles
if (!$has_acf) { ?>
			
			.wp3d-alert {
			    padding: 8px 35px 8px 14px;
			    margin-bottom: 18px;
			    color: #c09853;
			    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
			    background-color: #fcf8e3;
			    border: 1px solid #fbeed5;
			    -webkit-border-radius: 4px;
			    -moz-border-radius: 4px;
			    border-radius: 4px;
			}
			
			.wp3d-alert-error {
			    color: #b94a48;
			    background-color: #f2dede;
			    border-color: #eed3d7;
			}
			
			.wp3d-alert-error a,
			.wp3d-alert-error a:visited {
			    color: #b94a48!important;
			}
			
<?php } // end acf check ?>	

<?php if( $has_av_description_content ) { 
	do_action( 'wp3d_fullscreen_css', get_the_ID() );
 } ?>

<?php if (get_option('wp3d_custom_css')) { 
        //if there's some additonal custom CSS added, mash it on here.
        $wp3d_custom_css = strip_tags(get_option('wp3d_custom_css')); 
        $add_inline_style = trim(preg_replace('/\s+/', ' ', $wp3d_custom_css)); // mash it all into one line
        echo $add_inline_style;
    }

?>
			
		</style>

	</head>
	
	<?php // assembling body classes
	$wp3d_fullscreen_body_class = 'wp3d-single-model-fullscreen';
	if ($body_taxonomy_classes) { $wp3d_fullscreen_body_class =  $wp3d_fullscreen_body_class." ".$body_taxonomy_classes; }
	?>

	<body <?php body_class($wp3d_fullscreen_body_class); ?> itemscope itemtype="http://schema.org/WebPage">

	<?php
		if ( $has_acf && !$has_password ) { // true if ACF function 'get_field' exists && model is NOT password protected
		
			// Start the loop.
			while ( have_posts() ) : the_post();
			// Snag some vars
			
		 if ($is_introed) { // add intro image & enable autoplay ?>
		 
			<?php if(!$disable_header_bar) { // display only if the header bar is NOT disabled ?>
			
			<div id="wp3d-header-branding">
				
				<?php if($has_av_description_content) { 
					do_action( 'wp3d_fullscreen_navigation_content', get_the_ID() );
				} ?>
				
				<?php if ($has_description) { 
					$more_info_label = __('More Information', 'wp3d-models');
				?>
				<a href="#" id="description" title="<?php echo esc_attr(apply_filters( 'wp3d_more_info_label', $more_info_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_more_info_label', $more_info_label )); ?>"> 
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x"></i>
				  <i class="fa fa-info fa-stack-1x fa-inverse"></i>
				</span>
				</a>
				<?php } ?>				
				
				<?php if ($sharing_enabled) { 
					$share_heading = __('Share this page', 'wp3d-models');	
					$fa_share = 'fa-share'; 
				?>
				<a href="#" id="share" title="<?php echo esc_attr(apply_filters( 'wp3d_share_heading', $share_heading )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_share_heading', $share_heading )); ?>"> 
				<span class="fa-stack">
				  <i class="fa fa-circle fa-stack-2x"></i>
				  <i class="fa <?php echo esc_attr(apply_filters( 'wp3d_fa_share', $fa_share )); ?> fa-stack-1x fa-inverse"></i>
				</span>
				</a>
				<?php } ?>
				
				<?php // IF IN IFRAME -- ADD DIRECT LINK 
				$fullscreen_heading = __('FULLSCREEN', 'wp3d-models');
					if (isset($fullscreen_nobrand)) { 
						$fullscreen_permalink = trailingslashit(get_permalink()).'fullscreen-nobrand'; 
					} else {
						$fullscreen_permalink = trailingslashit(get_permalink()).'fullscreen'; 
					}
				?>
				<a href="<?php echo $fullscreen_permalink; ?>" target="_blank" id="fullscreen" title="<?php echo esc_attr(apply_filters( 'wp3d_fullscreen_heading', $fullscreen_heading )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_fullscreen_heading', $fullscreen_heading )); ?>">
					<span class="fa-stack">
					  <i class="fa fa-circle fa-stack-2x"></i>
					  <i class="fa fa-arrows-alt fa-stack-1x fa-inverse"></i>
					</span>					
				</a>
				
				<?php 
				if ($is_branded) {  
				$fa_connect = 'fa-share-alt'; 
				?>
				
					<?php if ( $has_profiles && $single_connect_enabled && !$has_override_logos ) { // gotta have profiles, be enabled 
						
						$connect_label = __('CONNECT WITH US:', 'wp3d-models'); 
					?>
					<a href="#" id="connect" title="<?php echo esc_attr(apply_filters( 'wp3d_connect_label', $connect_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_connect_label', $connect_label )); ?>">
					<span class="fa-stack">
					  <i class="fa fa-circle fa-stack-2x"></i>
					  <i class="fa <?php echo esc_attr(apply_filters( 'wp3d_fa_connect', $fa_connect )); ?> fa-stack-1x fa-inverse"></i>
					</span>
					</a>
					<?php } ?>
					
					<?php if($has_agents && !empty($agents_arr)) { 
						$contact_label = __('Contact', 'wp3d-models'); 
					?>
					<a href="#" id="agents" title="<?php echo esc_attr(apply_filters( 'wp3d_contact_heading', $contact_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_contact_heading', $contact_label )); ?>">
					<span class="fa-stack">
					  <i class="fa fa-circle fa-stack-2x"></i>
					  <i class="fa fa-phone fa-stack-1x fa-inverse"></i>
					</span>
					</a>
					<?php } ?>
					
					<span id="header-brand"><?php if ($custom_copyright) { echo esc_html(get_field('custom_copyright')); } else { echo "&copy; ".esc_html($model_title); } ?></span>
					
				<?php } ?>
			
			</div>
			
			<?php } // header bar disable check ?>	
			
			<?php do_action( 'wp3d_fullscreen_intro_before', get_the_ID() ); ?>
			
			<div id="wp3d-intro" class="is-loading<?php echo $status_class; echo $header_bar_class; ?>">
				
				<?php // ONLY RUN THROUGH THIS IF WE'RE ON A STATIC IMAGE
				
				if ($is_static_image) {   ?>
				
					<a href="#" class="no-iframe default-cursor">
					
					<div class="wp3d-start <?php if ($primary_logo_set) { echo "has-primary-logo "; } if ($small_logo_set) { echo "has-small-logo "; } ?>">
						
						<?php if ($is_branded && $primary_logo_set) { ?>
						<img src="<?php echo $large_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-large">
						<?php } ?>
						
						<?php if ($is_branded && $small_logo_set) { ?>
						<img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
						<?php } ?>						
						
						<h1><span><?php the_title(); ?></span></h1>

					</div>	
					
					</a>
				
				<?php } else {  // IS NOT A STATIC IMAGE ?>
				
				<a href="#" class="load-iframe">
					
				<?php if ($play_button_only) { ?>
					
					<div class="wp3d-start">
						<div class="play-button">
	    					<?php echo wp_kses(apply_filters( 'wp3d_play_button', $play_button_tag ), $play_button_html); ?>
						</div>
					</div>
					
				<?php } else { // we need more than just a play button ?>
					
					<div class="wp3d-start <?php if ($primary_logo_set) { echo "has-primary-logo "; } if ($small_logo_set) { echo "has-small-logo "; } ?>">
						
						<?php if ($is_branded && $primary_logo_set) { ?>
						<img src="<?php echo $large_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-large">
						<?php } ?>
						
						<?php if ($is_branded && $small_logo_set) { ?>
						<img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
						<?php } ?>						
						
						<h1><span><?php the_title(); ?></span></h1>
						<div class="play-button">
	    					<?php echo wp_kses(apply_filters( 'wp3d_play_button', $play_button_tag ), $play_button_html); ?>
						</div>
						<div class="message">
						<?php  
						if (get_field('custom_showcase_statement') && $is_branded ) { // branded and has a custom statement
							echo esc_html(get_field('custom_showcase_statement')); 
						} else { // fallback
							echo apply_filters( 'wp3d_default_intro_statement', $default_intro_statement );
						} 
						// NOW LOOK FOR PRESENTED INFO
						if ($showcase_type == 'intro-presented' && isset($api_data['presented_by'])) { // set to pull data from MP & data exists
							$presented_by_label = __('Presented By:', 'wp3d-models');			
							echo '<span class="presented-by">'. apply_filters( 'wp3d_presented_by_label', $presented_by_label ) .' <strong>'.$api_data['presented_by'].'</strong></span>';
						}
						?>
						</div>
					</div>
					
				<?php } // end play button only check ?>

					<?php if ($is_cobranded) { ?>
					<div class="cobrand">
						<img src="<?php echo $plugins_url; 
						?>/wp3d-models-free/assets/images/powered-by-matterport-1color.png" alt="Powered by Matterport">
					</div>
					<?php } ?>
				</a>
				
				<?php } // END STATIC IMAGE CHECK ?>
			</div>
			
			<?php do_action( 'wp3d_fullscreen_intro_after', get_the_ID() ); ?>
		
			<?php if($has_av_description_content) {
			  do_action( 'wp3d_fullscreen_overlay_content', get_the_ID() );
			} ?>
			
			<?php if($has_description) { // HAS DESCRIPTION ?>
			<div id="description-overlay" class="overlay">
				
				<?php if ($is_branded && $primary_logo_set) { ?>
					<img src="<?php echo $large_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-large">
				<?php } ?>
				
				<?php if ($is_branded && $small_logo_set) { ?>
					<img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
				<?php } ?>				
				
				<h2><?php the_title(); ?></h2>
				<div class="model-summary">
	<?php if (get_the_excerpt()) { ?>						
					
					<?php 
					add_filter( 'excerpt_more', function() { return ' ...'; } ); 
					the_excerpt(); 
					?>
					
	<?php } elseif (isset($api_data['summary'])) { ?>					
					<p><?php echo $api_data['summary']; ?></p>
	<?php } ?>
				</div>
				
				<a href="<?php echo esc_attr($wp3d_view_link); ?>" class="btn summary-button" title="<?php echo apply_filters( 'wp3d_more_info_label', $more_info_label ); ?>" aria-label="<?php echo apply_filters( 'wp3d_more_info_label', $more_info_label ); ?>" target="_blank"><?php echo apply_filters( 'wp3d_more_info_label', $more_info_label ); ?> &nbsp;<i class="fa fa-angle-right"></i></a>

				<a href="#" class="close-overlay" title="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>"><i class="fa fa-times"></i></a>
			</div>	
			<?php } ?>
			
			<?php if ($sharing_enabled) { ?>
			<div id="share-overlay" class="overlay">
				
				<?php if ($is_branded && $primary_logo_set) { ?>
					<img src="<?php echo $large_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-large">
				<?php } ?>
				
				<?php if ($is_branded && $small_logo_set) { ?>
					<img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
				<?php } ?>					
				
					<div class="wp3d-share-icons-wrap">
				
						<h2><?php the_title(); ?></h2>
						<h3><?php echo apply_filters( 'wp3d_share_heading', $share_heading ); ?></h3>
						
						<?php do_action( 'wp3d_share_list_before', get_the_ID() ); ?>
						<?php echo WP3D_Models()->get_model_share_list(get_the_ID(), false, $is_introed); ?>
						<?php do_action( 'wp3d_share_list_after', get_the_ID() ); ?>

					</div>
					
				<a href="#" class="close-overlay" title="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>"><i class="fa fa-times"></i></a>
			</div>
			<?php } ?>
			
			<?php // CONNECT OVERLAY ?>
			<?php if ($is_branded && $has_profiles && $single_connect_enabled && !$has_override_logos ) { ?>
			<div id="connect-overlay" class="overlay">
				
				<?php if ($is_branded && $primary_logo_set) { ?>
					<img src="<?php echo $large_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-large">
				<?php } ?>
				
				<?php if ($is_branded && $small_logo_set) { ?>
					<img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
				<?php } ?>	
			
				<div class="connect-icons-wrap">

					<h3><?php echo apply_filters( 'wp3d_connect_label', $connect_label ); ?></h3>	
						
					<ul class="connect-icons">
					<?php 
										
					foreach ($company_urls as $key => $val) { ?>
					
					<?php //icon class translation
					if ($key == "website") { $key_icon = 'globe'; }
					elseif ($key == "google") { $key_icon = 'google'; }
					else { $key_icon = $key; }
					?>
					
					<li>
						<a href="<?php echo $val; ?>" title="<?php echo esc_attr($key); ?>" aria_label="<?php echo esc_attr($key); ?>" target="_blank">
							<span class="fa-stack fa-2x">
								<i class="fa fa-circle-thin fa-stack-2x"></i>
								<i class="fa fa-<?php echo $key_icon; ?> fa-stack-1x"></i>
							</span>						
						</a>
					</li>	
						    
					<?php } ?>
					</ul>
				
				</div>				
				
				<a href="#" class="close-overlay" title="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>"><i class="fa fa-times"></i></a>
			</div>
			<?php } ?>
			
			<?php if ($is_branded && $has_agents && !empty($agents_arr) ) { ?>
			
			<?php // CONNECT OVERLAY ?>
			<div id="agents-overlay" class="overlay">
				
				<div class="agents-wrap">

					<ul class="agents-call">
					<?php foreach ($agents_arr as $agent) { ?>
					
		    			<li>
		    			    <?php if (isset($agent['image_src'])) { ?>
		    			    <div class="agent-photo-cell tc">
		        			    <div class="agent-photo">
			                        <div class="agent-img" style="background-image: url('<?php echo esc_attr($agent['image_src']); ?>')">
			                            <span class="sr-only"><?php echo esc_attr($agent['name']); ?></span>
			                        </div>      
	        			    	</div>
		    			    </div>
		    			    <?php } ?>
		    			    <div class="agent-contact tc">
		                        <?php if( isset($agent['name'])) { ?><span class="fn" title="<?php echo esc_attr($agent['name']); ?>"><?php echo esc_html($agent['name']); ?></span><?php } ?>
		    			        <ul class="agent-meta fa-ul">
		                            <?php if( isset($agent['email'])) { ?><li class="email"><i class="fa-li fa fa-envelope"></i><a href="mailto:<?php echo esc_attr($agent['email']); ?>"><?php echo esc_html($agent['email']); ?></a></li><?php } ?>
		                            <?php if( isset($agent['phone'])) { // this one comes from Matterport ONLY, including the "formatted phone" that is part of this conditional ?><li class="tel"><i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone'])); ?>" class="value"><?php if (isset($agent['formatted_phone']) ) { echo esc_html($agent['formatted_phone']); } else { echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone'])); } ?></a></li><?php } ?>     
		                            <?php /* if( isset($agent['phone'])) { // this one comes from Matterport ONLY ?><li class="tel"><i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone'])); ?>" class="value"><?php echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone'])); ?></a></li><?php } */ ?>                            
		                            <?php if(isset($agent['phone-mobile'])) { ?><li class="tel"><i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone-mobile'])); ?>" class="value"><?php echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone-mobile'])); ?></a></li><?php } ?>
		                        </ul>
		                    </div>
		    			</li>
				
					<?php } ?>
					</ul>					
					
				</div>
				
				<a href="#" class="close-overlay" title="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>" aria-label="<?php echo esc_attr(apply_filters( 'wp3d_close_overlay_label', $close_overlay_label )); ?>"><i class="fa fa-times"></i></a>				
				
			</div>
			
			<?php } ?>
			
			<?php if (!$is_static_image) { ?>
			
			<div id="wp3d-iframe-wrap" class="branded<?php echo $status_class; echo $header_bar_class; echo $overlay_highlight_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">
				<?php if ($overlay_logo_set) { ?>
				<img class="iframe-logo-overlay" src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>">
				<?php } ?>
				
				<?php echo WP3D_Models()->get_content_schema($post->ID, $wp3d_id, $wp3d_iframe_src_url); ?>
				<iframe id="mp-iframe" src="" <?php if ($wp3d_preload) { echo 'data-preload="true" '; } ?>data-src="<?php echo $wp3d_iframe_data_src; ?>" data-allow="<?php echo $allow; ?>" frameborder="0" allow="vr<?php echo $allow; ?>" allowfullscreen></iframe>		
			</div>
			
			<?php } ?>
			

			
<?php } else { // no intro ?>

			<?php if (!$is_static_image) { ?>

			<div id="wp3d-iframe-wrap" class="unbranded<?php echo $status_class; echo $overlay_highlight_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">
				<?php echo WP3D_Models()->get_content_schema($post->ID, $wp3d_id, $wp3d_iframe_src_url); ?>
				<iframe src="<?php echo $wp3d_iframe_src_url; ?>" frameborder="0" allow="vr<?php echo $allow; ?>" allowfullscreen></iframe>				
			</div>
			
			<?php } ?>
			
<?php } // end intro check 
		
// End the loop.
endwhile;
			
		} else {  // didn't pass the ACF & password check ?>
		
			<?php if ($has_password) { // password protection in place ?>
			
			<div class="password-protected">
				<?php the_content(); // this is here to show the password field ?>
			</div>
			
			<?php } else { // no ACF function 'get_field' ?>
	
			<div class="entry-content">
				<div class="wp3d-alert wp3d-alert-error"><strong><?php _e( 'BUGGER!', 'WP3D_Models' ); ?></strong>  <?php _e( 'WP3D Models', 'WP3D_Models' ); ?> <a href="/wp-admin/plugins.php" target="_blank"><?php _e( 'needs additional plugins installed', 'WP3D_Models' ); ?></a> <?php _e( 'in order to work correctly!', 'WP3D_Models' ); ?></div>
			</div>
			
			<?php } ?>
		
		<?php } // end checking for ACF function 'get_field' && password protection ?>

	<?php do_action( 'wp3d_fullscreen_footer_before', get_the_ID() ); ?>
	
	<?php wp_footer(); ?>
	
	<?php do_action( 'wp3d_fullscreen_footer', get_the_ID() ); ?>
	
			<script>
			
			function detectIOS() {
			    var t = window.navigator.userAgent,
			        e = /iPad|iPhone|iPod/;
			    return e.test(t)
			}
			
			/*
			 * Get Viewport Dimensions
			 * returns object with viewport dimensions to match css in width and height properties
			 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
			*/
			function updateViewportDimensions() {
				var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
				return { width:x,height:y }
			}
			// setting the viewport width
			var viewport = updateViewportDimensions();	

			//adding fullscreen icon if we're embedded
			if (window!=window.top) {
				jQuery( "#fullscreen").addClass('framed');
			}
	
			jQuery(document).ready(function() {
				
				// check for iOS
				if ( detectIOS() ) {
					jQuery('body').addClass('is-ios');
				}
				
				<?php if( $has_av_description_content ) { 
					do_action( 'wp3d_fullscreen_js', get_the_ID() );
				 } ?>
				
				jQuery( "#description" ).click(function(e) {
					e.preventDefault();
					jQuery( "#description-overlay" ).addClass("show");
					jQuery( "body").addClass("overlaid");
				});	
				
				jQuery( "#share" ).click(function(e) {
					e.preventDefault();
					jQuery( "#share-overlay" ).addClass("show");
					jQuery( "body").addClass("overlaid");					
				});	
				
				jQuery( "#connect" ).click(function(e) {
					e.preventDefault();
					jQuery( "#connect-overlay" ).addClass("show");
					jQuery( "body").addClass("overlaid");					
				});	
				
				jQuery( "#agents" ).click(function(e) {
					e.preventDefault();
					jQuery( "#agents-overlay" ).addClass("show");
					jQuery( "body").addClass("overlaid");					
				});					
				
				jQuery( ".close-overlay" ).click(function(e) {
					e.preventDefault();
					jQuery(".overlay iframe").attr('src', '');
					jQuery( ".overlay" ).removeClass("show");
					jQuery( "body").removeClass("overlaid");					
				});
				
				jQuery(document).keyup(function(e) {
				  if (e.keyCode === 27) { jQuery('.close-overlay').click(); }   // esc
				});				
								
				/* Preload Functionality */
				var preloadModel = jQuery( "#mp-iframe").data('preload');
				if (preloadModel === true ) {
					console.log('preload');
					if( viewport.width >= 768 ) { // do the preload
						var mp_iframe_preload_src = jQuery( "#mp-iframe").data('src'); // get the iframe src
						// do the preload
						if (mp_iframe_preload_src !== '') {
							jQuery( "#mp-iframe").attr("src", mp_iframe_preload_src);
						}
					}
				} else {
					preloadModel = false;
				}   				
					
				jQuery( "#wp3d-intro a.no-iframe" ).click(function(e) {
					e.preventDefault();
				});
				
				jQuery( "#wp3d-intro a.load-iframe" ).click(function(e) {
					e.preventDefault();
					window.scrollTo(0,0);
					if (preloadModel === false || viewport.width < 768) { // only swap out the src if we are not preloading OR on mobile
						var mp_iframe_src = jQuery( "#mp-iframe").data('src');
						jQuery( "#mp-iframe").attr("src",mp_iframe_src);
					}					
		  			jQuery( "#wp3d-intro" ).fadeOut("slow");
	  				jQuery('#wp3d-iframe-wrap').removeClass('wp3d-sold wp3d-pending'); // if it exists, remove the sold/pending when actually viewing the model
					if( viewport.width < 768 ) { // fade out overlay on small screens
						jQuery('.iframe-logo-overlay').delay(3000).fadeOut();
					}
				});
				
				// Remove is-loading intro class
				window.setTimeout(function() {
					jQuery('#wp3d-intro').removeClass('is-loading');
				}, 500);				

				jQuery(window).load(function() {
					jQuery('body').addClass('loaded');
			        jQuery('body').delay( 800 ).addClass('loaded-delay');
			        
					/* If the #start hash is present in the skinned URL, jump right in */
					if (window.location.hash){
						var hash = window.location.hash.substring(1);
						if (hash == "start"){
							console.log ("WP3D Starting!");
						    jQuery( "#wp3d-intro a" ).click();
						}
					}			        
			        
			    })
								
		
			});
			
			</script>
			
	<?php do_action( 'wp3d_fullscreen_footer_after', get_the_ID() ); ?>
	</body>

</html> <!-- end of site. what a ride! -->
