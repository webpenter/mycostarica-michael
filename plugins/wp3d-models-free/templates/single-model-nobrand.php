<?php
/**
 * The template for displaying all model "single" posts (with no branding)
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

// BUFFERING TO STRIP ALL (POTENTIALLY CONFLICTING) THEME JS & CSS FROM HEADER/FOOTER

function wp3d_end_ob_header_callback($buffer) {
$new_buffer = '';
$header_arr = explode("\n", $buffer);
foreach ($header_arr as $header_line) {
    // first see if we match a WP3D specific plugin file (custom to NOBRAND)
    if (strpos($header_line,'/wp-content/plugins/wp3d-models-free/') == true) { 
    	$new_buffer .= $header_line."\n";
    	}
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
        // if any of the following match, add to the final output buffer (slick & swiper script checks are custom to NOBRAND)
        if (strpos($footer_line,'/wp-content/plugins/wp3d-models-free/assets/') == true || strpos($footer_line,'maps.googleapis.com') == true || strpos($footer_line,'slick.min') == true || strpos($footer_line,'swiper.jquery.min') == true  ) {
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
 
?><!doctype html>

<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<?php 
		// Passing just the title attribute here on purpose..."NO BRANDING, remember?"
		// Additionally, we don't want search engines picking up this version of the page.  Setting canonical.
		?>
		<title><?php the_title_attribute(); ?></title>
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

		// SLICK SLIDER - RELATED MODELS
		if ( WP3D_Models()->get_related_models() && !get_option('wp3d_disable_slick_js') ) { // related models are present AND slick is NOT disabled
		   wp_enqueue_script( WP3D_Models()->_token . '-slick-slider' );
		}
		
		// GALLERY TYPE JS
		$wp3d_gallery_type = get_field('gallery_type');	
		
		if ($wp3d_gallery_type == 'standard_slider' || $wp3d_gallery_type == '') { // SWIPER
			if ( WP3D_Models()->get_wp3d_gallery_images() && !get_option('wp3d_disable_swiper_js') ) { // if there are gallery images
				wp_enqueue_script( WP3D_Models()->_token . '-swiper-gallery' );
			}
		} elseif ( $wp3d_gallery_type == 'zoom_slider' ) { // SLICK
			if ( WP3D_Models()->get_wp3d_gallery_images() && !get_option('wp3d_disable_slick_js') ) { // if there are gallery images
				wp_enqueue_script( WP3D_Models()->_token . '-slick-slider' ); 
			}
		}
		
		?>

	</head>

	<body <?php body_class('nobrand'); ?> itemscope itemtype="http://schema.org/WebPage">

	<div id="wp3d-single-model" class="no-brand wp3d-content-area">
		<main id="main" class="site-main" role="main">
			
<?php if ( function_exists('get_field') && !post_password_required() ) { // true if ACF function 'get_field' exists && model is NOT password protected ?>	
			
		<header class="entry-header wp3d-entry-header">
			<?php the_title( '<h1 class="wp3d-entry-title">', '</h1>' ); ?>
			<?php if(get_field('model_subtitle')) { ?><h2><?php the_field('model_subtitle'); ?></h2><?php } ?>
		</header><!-- .entry-header -->

		<?php
		// PLUGINS DIR
		$plugins_url = plugins_url();		
		
		// DEFAULT INTRO STATEMENT
		$default_intro_statement = __('START TOUR', 'wp3d-models');	
		
		// Start the loop.
		while ( have_posts() ) : the_post();

		// LETS GET SOME MODEL DATA
		$post_id = get_the_ID(); 
		$api_data = get_field('_matterport_api_data'); // hidden custom field stores any retrieved Matterport data in an array
		$showcase_type = get_field('showcase_branding');
		
		// ########### INTRO IMAGE CHECK ########### 
		// Calling the 'mp-intro-size' (1920 x 1080) in case the image that was uploaded was bigger
		$intro_src_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'mp-intro-size' );
        $intro_src = $intro_src_arr[0];	
		
		// ########### MODEL TYPE CHECK ########### 
		$wp3d_model_type = get_field('wp3d_model_type');  // matterport, static_image, etc.
		if ($wp3d_model_type == "static_image") { 
			$is_static_image = true;
		} else {
			$is_static_image = false;
		}	
		
		// Default Schema Type
		$content_schema_type = __('MediaObject', 'wp3d-models'); 		
		
		// ########### INTRO CHECK ########### 
		$intro_arr = array('intro-unbranded', 'intro-presented', 'intro-branded', 'intro-cobranded');
		if (in_array($showcase_type, $intro_arr)) {  //if the selected showcase type is in our "intro" array, she's introed
			$is_introed = true;
		} else {
			$is_introed = false;
		}
		
		// ########## PlAY BUTTON ###########
		$play_button_html = array(
		  'i' => array( 'class' => array() ),
		  'img' => array( 'class' => array(), 'src' => array() ),
		);
		$play_button_tag = '<i class="fa fa-play-circle"></i>';
		
		// ########### CONTENT/FEATURE CHECKS ########### 
		if (get_field('add_property_info_details')) { $has_property_info = true; }		
		if (WP3D_Models()->get_wp3d_gallery_images()) { $has_gallery = true; } else { $has_gallery = false; }
		if (WP3D_Models()->get_related_models()) { $has_related_models = true; } else { $has_related_models = false; }		

		// PRELOAD
		$wp3d_preload = get_field('preload_model');	
		
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
		
// MATTERPORT MODEL
if ($wp3d_model_type == 'matterport') { 		
		
		// get the ID!
		$mp_incoming = trim(get_field('model_link'));
		$mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
		$mp_start = WP3D_Models()->mp_start_from_url($mp_incoming); // potential deep-link start
		
		// Update Intro Text
		$default_intro_statement = __('START TOUR', 'wp3d-models');	
		
		// Branding checks/flags
		if (get_option('wp3d_disable_nobrand_links')) {
			$mp_global_disable_links = true;
		} else { 
			$mp_global_disable_links = false;
		}
		$is_branded = false;
		$is_cobranded = false;
		
		// parameter checks
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
			// Hack to fix MP Bug (No need for VR on an iPad)
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
		
		
		if ( $mp_autoplay || 
			 $mp_multifloor || 
			 $mp_force_help || $mp_force_help == 0 || 
		     $mp_no_guided_tour_panning || 
		     $mp_looped_guided_tour || 
		     $mp_no_guided_tour_path || 
		     $mp_show_highlight_reel || $mp_show_highlight_reel == 0 || 
		     $mp_autostart_guided_tour ||
 			 $mp_disable_mouse_arrows ||
 			 $mp_enable_quickstart ||
 			 // $mp_disable_vr ||  ####### always disabled on nobrand
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
	    	//$wp3d_iframe_src_url = 'https://my.matterport.com/show/?m='.$mp_id.'&amp;play=1&amp;brand=0&amp;vr=0';  
			$wp3d_iframe_src_url = WP3D_Models()->mp_get_iframe_url($mp_incoming, $mp_id).'play=1&amp;brand=0&amp;vr=0&amp;';
		} else {
			//$wp3d_iframe_src_url = 'https://my.matterport.com/show/?m='.$mp_id.'&amp;brand=0&amp;vr=0';
			$wp3d_iframe_src_url = WP3D_Models()->mp_get_iframe_url($mp_incoming, $mp_id).'brand=0&amp;vr=0&amp;';
		}
		
		// Additional Param Check
		//if ($mp_params){ $wp3d_iframe_src_url .= '&amp;'; }
		
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
		// if ($mp_disable_vr) { $wp3d_iframe_src_url .=  'vr=0&amp;'; }	####### always disabled on nobrand	
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

        // MPEmbed params gets added last 
        if (get_field('customize_showcase') == 'mpembed') {
           $wp3d_iframe_src_url .= WP3D_Models()->mp_get_mpembed_params($post->ID, true); 
        }   

	    // Parameter Cleanup
	    $wp3d_iframe_src_url = preg_replace('/&amp;$/', '', $wp3d_iframe_src_url);
	    
	    // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;
        $wp3d_id = $mp_id;
	    
} // END MATTERPORT CHECK

// THREESIXTY TOURS MODEL
if ($wp3d_model_type == 'threesixtytours') {		
			
        $tst_incoming = trim(get_field('tst_link'));
        $tst_id = WP3D_Models()->tst_id_from_url($tst_incoming); // array
        
		// Update Intro Text
		$default_intro_statement = __('START 360 TOUR', 'wp3d-models');	
        
        // When a Model is saved, we look to TST for any saved data and copy it back 
        //$api_data = get_field('_tst_api_data'); // hidden custom field holds retrieved data

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
        $tst_startscreen = get_field('tst_startscreen'); 
        // branding removed 
        //$tst_branding = get_field('tst_branding');         
        
        //UNLIKE MATTERPORT, WE ATTACH ALL PARAMETERS...SO WE APPEND THE "?" REGARDLESS
        $wp3d_iframe_src_url .=  '?brand=false&amp;'; // always no brand here
        
        // build the URL
        if ($tst_header) { $wp3d_iframe_src_url .=  'header='.$tst_header.'&amp;'; } else { $wp3d_iframe_src_url .=  'header=transparent&amp;'; }
        if ($tst_footer) { $wp3d_iframe_src_url .=  'footer='.$tst_footer.'&amp;'; } else { $wp3d_iframe_src_url .=  'footer=true&amp;'; }
        if ($tst_title) { $wp3d_iframe_src_url .=  'title='.$tst_title.'&amp;'; } else { $wp3d_iframe_src_url .=  'title=false&amp;'; }   
        if ($tst_tournav) { $wp3d_iframe_src_url .=  'tournav='.$tst_tournav.'&amp;'; } else { $wp3d_iframe_src_url .=  'tournav=delayclose&amp;'; }
        if ($tst_mousewheel) { $wp3d_iframe_src_url .=  'mousewheel='.$tst_mousewheel.'&amp;'; } else { $wp3d_iframe_src_url .=  'mousewheel=true&amp;'; }
        if ($tst_socialshare) { $wp3d_iframe_src_url .=  'socialshare='.$tst_socialshare.'&amp;'; } else { $wp3d_iframe_src_url .=  'socialshare=false&amp;'; }
        
        // Startscreen logic
        if (!$is_introed) { // no reason to ever add a startscreen for "introed" content
        	if ($tst_startscreen == "true") { 
        		$wp3d_iframe_src_url .=  'startscreen='.$tst_startscreen.'&amp;'; 
        	}
        }
        
        // branding removed 
        //if ($tst_branding) { $wp3d_iframe_src_url .=  'brand='.$tst_branding.'&amp;'; } else { $wp3d_iframe_src_url .=  'brand=false&amp;'; }
        
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
        
        $base_youtube_url = get_field('base_youtube_unbranded_video_link');
        
        if ($base_youtube_url == '') { // if no unbranded, grab the main
        	$base_youtube_url = get_field('base_youtube_video_link');	
        }
        $wp3d_iframe_src_url = WP3D_Models()->youtube_embed_from_url($base_youtube_url, true).'&amp;autoplay=1';;
        
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;         
        
    } 
    
    if (get_field('base_video_type') == 'vimeo') {
        
        $base_vimeo_url = get_field('base_vimeo_unbranded_video_link');
        	
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
		
		// widened FOV check
		if (get_field('widened_field_of_view')) {
			$widened_fov = true;
			$fov_class = ' widened-fov';
		} else {
			$widened_fov = false;
			$fov_class = '';
		}
		
		?>
		
		<?php // MODEL TYPE & INTROS 
		
		if ($is_static_image) { // JUST AN IMAGE ?>
		
			<div class="wp3d-embed-wrap<?php echo $status_class; ?>">
				<div id="wp3d-intro" style="background-image: url('<?php echo $intro_src; ?>');"></div>
			</div>
			
		<?php } else { // NOT A STATIC IMAGE 		
		
			if ($is_introed) { // add intro image & enable autoplay, but don't pass the src to the iframe just yet ?>
			
			<div class="wp3d-embed-wrap<?php echo $status_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">
				
				<div id="wp3d-intro" class="is-loading" style="background-image: url('<?php echo $intro_src; ?>');">
					<a href="#">
						<div class="wp3d-start">
							<div class="play-button">
	    						<?php echo wp_kses(apply_filters( 'wp3d_play_button', $play_button_tag ), $play_button_html); ?>
							</div>						
							<div class="message"><?php echo apply_filters( 'wp3d_default_intro_statement', $default_intro_statement ); ?></div>
						</div>
	
					</a>
				</div>
				<?php echo WP3D_Models()->get_content_schema($post_id, $wp3d_id, $wp3d_iframe_src_url); ?>
				<iframe id="mp-iframe" src="" <?php if ($wp3d_preload) { echo 'data-preload="true" '; } ?>data-src="<?php echo $wp3d_iframe_data_src; ?>" data-allow="<?php echo $allow; ?>" frameborder="0" allow="vr<?php echo $allow; ?>" allowfullscreen></iframe>		
				
			</div>
			
			<?php } else { // just print out the stock iframe, with options ?>
			
			<div class="wp3d-embed-wrap<?php echo $status_class; ?><?php echo $fov_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">	
				<?php echo WP3D_Models()->get_content_schema($post_id, $wp3d_id, $wp3d_iframe_src_url); ?>
				<iframe src="<?php echo $wp3d_iframe_src_url; ?>" frameborder="0" allow="vr<?php echo $allow; ?>" allowfullscreen></iframe>		
			</div>	
			
			<?php } // end introed check 
			
		} // END MODEL TYPE CHECK 
		
		// get address info
	    $address = WP3D_Models()->get_model_address_info($post_id);
        
        // checking to see if we've got a map
        if (isset($address['lat']) && isset($address['lng'])) {  
            $has_map = true;
            $address_lat = $address['lat']; 
            $address_lng = $address['lng'];
            $nomarker = false;
        } else {
            $has_map = false;
            $address_lat = '';
            $address_lng = '';  
            $nomarker = true;
        } 
		
		if ($has_map) { // show a map 
		
		if(get_option('wp3d_single_page_map_zoom')) { $map_zoom = get_option('wp3d_single_page_map_zoom'); } else { $map_zoom = '6'; } // override zoom
		if(get_option('wp3d_single_page_map_type')) { $map_type = get_option('wp3d_single_page_map_type'); } else { $map_type = 'ROADMAP'; } // override map type		
		if(get_option('wp3d_marker_type')) { $marker_type = get_option('wp3d_marker_type'); } else { $marker_type = 'stock'; } // marker type
		?>
		
		<?php do_action( 'wp3d_single_map_before', get_the_ID() ); ?>
		<div id="wp3d-single-map" data-map-zoom="<?php echo $map_zoom; ?>" data-map-type="<?php echo $map_type; ?>" data-latlng="<?php echo $address_lat;?>,<?php echo $address_lng;?>" data-marker-type="<?php echo $marker_type; ?>"></div>
		<?php do_action( 'wp3d_single_map_after', get_the_ID() ); ?>
		<?php } // end has map ?>
		
		
<?php // ################# BEGIN Property Info ################# //
		if (isset($has_property_info)) { 
		    
		    if( have_rows('property_info_details') ) {
		    $rows = get_field('property_info_details');
		    $search_text = '<i class="fa';
		    $has_fa = false;
		    $row_has_icon = false;
		
		        foreach ($rows as $row) {
		            if (strpos($row['title'], $search_text) !== false) {
		                $has_fa = true;
		                break;
		            }
		        }
		?>
        <?php do_action( 'wp3d_single_property_info_before', get_the_ID() ); ?>
        <div id="property-info">
            <ul class="property-info">
            <?php while( have_rows('property_info_details') ): the_row(); 
            $title = trim(get_sub_field('title'));
            $text = trim(get_sub_field('value'));
            if (0 === strpos($title, $search_text)) { $row_has_icon = true; } else { $row_has_icon = false; }
            ?>
                <li><?php if ( $has_fa && $row_has_icon == false ) { ?><i class="fa fa-fw"></i>&nbsp;<?php } ?><?php if ( $title ) { ?><?php echo $title; ?><?php } // title ?> <?php if ( $text ) { ?><em><?php echo $text; ?></em><?php } // text ?></li>
            <?php $row_has_icon = false; endwhile; ?>
            </ul>                        
        </div>
        <?php do_action( 'wp3d_single_property_info_after', get_the_ID() ); ?>
                    
<?php 
        
    } // end have rows

} // ################# END Property Info ################# // ?>

<?php // ################# BEGIN Content ################# // ?>		
		<?php do_action( 'wp3d_single_content_before', get_the_ID() ); ?>
		<div class="entry-content wp3d-entry-content">
		<?php if (get_the_content()) { 
			the_content();
		} elseif (get_field('model_content')) { 
			the_field('model_content');
		} elseif (isset($api_data['summary'])) { ?>					
			<p><?php echo $api_data['summary']; ?></p>
		<?php } ?>
		</div>
		<?php do_action( 'wp3d_single_content_after', get_the_ID() ); ?>
<?php // ################# END Content ################# // ?>

<?php // ################# BEGIN Tabs ################# //
    if (get_field('add_property_text_tabs')) { ?>
    <?php do_action( 'wp3d_single_tabs_before', get_the_ID() ); ?>
    <div id="wp3d-property-tabs" role="navigation">
        <?php 
        
        if( have_rows('property_text_tabs') ) { // FIRST FOR THE TABS ?>
        <!-- Nav tabs -->
        <ul class="propnav wp3d-prop-tabs wp3d-models-clearfix" role="tablist">
        <?php $rows = get_field('property_text_tabs');
            $i = 0;
            foreach ($rows as $row) { ?>
            <li role="presentation"<?php if ($i=='0'){ echo ' class="active"'; } ?>>
            	<a href="#tab-<?php echo $i; ?>" aria-controls="profile" role="tab"><?php echo $row['tab_title']; ?></a>
            </li>
            <?php $i++; } ?>
        </ul>
        <?php } // end if have rows
        
       if( have_rows('property_text_tabs') ) { // FIRST FOR THE TABS ?>
        <!-- Tab panes -->
        <div class="wp3d-tab-content">
        <?php $rows = get_field('property_text_tabs');
    
            $i = 0;
            foreach ($rows as $row) { ?>
    
            <div role="tabpanel" class="tab-pane<?php if ($i=='0'){ echo ' active'; } ?>" id="tab-<?php echo $i; ?>">
                <?php echo $row['tab_wysiwyg']; ?>
            </div>
    
            <?php $i++; } ?>
        </div>
        <?php } // end if have rows ?>
    </div>
    <?php do_action( 'wp3d_single_tabs_after', get_the_ID() ); ?>
<?php } // ################# END Tabs ################# //  ?>     
    
<?php // ################# BEGIN Video ################# //
if (get_field('add_video')) { 
    
    $has_video = get_field('add_video');
    $youtube_unbranded_url = get_field('youtube_unbranded_video_link');
    $vimeo_unbranded_url = get_field('vimeo_unbranded_video_link');    
    
    if( $youtube_unbranded_url || $vimeo_unbranded_url ) {
?>    
	<?php do_action( 'wp3d_single_video_before', get_the_ID() ); ?>
    <div id="wp3d-video">
	<?php if ($has_video == "youtube") {
		echo WP3D_Models()->youtube_embed_from_url( $youtube_unbranded_url );
	} elseif ($has_video == "vimeo") {
		echo WP3D_Models()->vimeo_embed_from_url( $vimeo_unbranded_url );
	} ?>
    </div>
    <?php do_action( 'wp3d_single_video_after', get_the_ID() ); ?>
<?php  } // end if youtube or vimeo isn't empty
} 
// ################# END Video ################# // ?>  
		
<?php do_action( 'wp3d_single_gallery_before', get_the_ID() ); ?>

<!--Gallery Section-->	
		
<?php // ################# BEGIN Gallery ################# //
if ($has_gallery) { 

    $images = WP3D_Models()->get_wp3d_gallery_images();
    
    if( $images ) { 
    
		if ($wp3d_gallery_type == 'standard_slider' || $wp3d_gallery_type == '') {    
?>    
    <section id="wp3d-gallery">
        
        <div class="wp3d-swiper-container">
            <div class="swiper-wrapper">
                <?php foreach( $images as $image ): ?>
                    <?php /* <div class="swiper-slide" style="background-image:url(<?php echo $image['sizes']['mp-intro-size']; ?>);"> */ ?>
                    <div class="swiper-slide" data-src="<?php echo $image['sizes']['mp-intro-size']; ?>">
                		<?php if ( $image['caption'] ) { ?>

                        <span class="wp3d-caption-open fa-stack fa-lg">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-info fa-stack-1x fa-inverse"></i>
                        </span>
                        
                        <div class="wp3d-gallery-caption">
                            
                            <span class="wp3d-caption-close fa-stack fa-lg">
                              <i class="fa fa-circle fa-stack-2x fa-inverse"></i>
                              <i class="fa fa-times fa-stack-1x"></i>
                            </span>

                            <?php /* if ($image['title']) { ?><p class="caption-title"><?php echo $image['title']; ?></p><?php } */ ?>
                    		<?php if ($image['caption']) { ?><p><?php echo $image['caption']; ?></p><?php } ?>
                            
                        </div>
                        
                        <?php } ?>		                    	
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination swiper-pagination-white"></div>
            <div class="swiper-button-prev swiper-button-white"></div>
            <div class="swiper-button-next swiper-button-white"></div>
        </div>         
        
    </section>
		    
<?php } elseif ( $wp3d_gallery_type == 'zoom_slider' ) {  ?>
		    
    <section id="wp3d-zoom-gallery" class="gallery-loading">
        
        <div class="wp3d-zoom-slider">

            <?php foreach( $images as $image ): ?>
                <div>
                    <a href="<?php echo $image['sizes']['wp3d-gallery-size']; ?>">
                        <img data-lazy="<?php echo $image['sizes']['wp3d-gallery-size']; ?>" alt="<?php echo esc_attr($image['caption']); ?>">
                    </a>
                    <?php if ( $image['caption'] ) { ?>
                    <span class="wp3d-caption-open fa-stack fa-lg">
                      <i class="fa fa-circle fa-stack-2x"></i>
                      <i class="fa fa-info fa-stack-1x fa-inverse"></i>
                    </span>
                    
                    <div class="wp3d-gallery-caption">
                        
                        <span class="wp3d-caption-close fa-stack fa-lg">
                          <i class="fa fa-circle fa-stack-2x fa-inverse"></i>
                          <i class="fa fa-times fa-stack-1x"></i>
                        </span>
                        
                        <?php if ($image['caption']) { ?><p><?php echo $image['caption']; ?></p><?php } ?>
                    </div>
                    <?php } ?>
                    <div class="slick-counter"></div>
                </div>
            <?php endforeach; ?>
            
        </div>
        
        <div class="slick-loading"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
        
    </section>

<?php } // end the elseif 		    
		    
	} // has images
	
} // $has_gallery

// ################# END Gallery ################# // ?> 

<?php do_action( 'wp3d_single_gallery_after', get_the_ID() ); ?>
		
<?php // ################# BEGIN Smart Gallery ################# //
if (get_field('add_smart_gallery') && get_field('smart_gallery_url')) { ?>
	<?php do_action( 'wp3d_single_smartgallery_before', get_the_ID() ); ?>
	<?php echo WP3D_Models()->get_smart_gallery(); ?>
	<?php do_action( 'wp3d_single_smartgallery_after', get_the_ID() ); ?>
<?php } // ################# END Smart Gallery ################# // ?>		

<?php // ################# BEGIN Floorplans ################# //
$floorplan_images = get_field('floorplan_gallery');
$floorplan_image_label = __('Image Link', 'wp3d-models');

if( $floorplan_images && get_field('add_floorplan') ) { ?>
	<?php do_action( 'wp3d_single_floorplan_before', get_the_ID() ); ?>
    <section id="floorplans">
	<?php do_action( 'wp3d_single_floorplan_inside_before', get_the_ID() ); ?>
    <ul id="wp3d-floorplan-images">
        <?php $i=0; foreach( $floorplan_images as $image ) { ?>
            <li>
                <a href="#" title="<?php echo esc_attr($image['title']); ?>" class="floorplan-thumb" style="background-image: url('<?php echo $image['sizes']['thumbnail']; ?>');" data-featherlight="#floorplan-<?php echo $i; ?>"></a>
            </li>
        <?php $i++; } // end foreach ?>
    </ul>
    
    <?php $i=0; foreach( $floorplan_images as $image ) { ?> 
    <div class="floorplan-modal" id="floorplan-<?php echo $i; ?>" role="dialog">
        <img src="" data-src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>">
        <?php if(isset($image['title'])) { ?><h3><?php echo $image['title']; ?></h3><?php } ?>
        <?php if(isset($image['caption'])) { ?><p><?php echo $image['caption']; ?></p><?php } ?>
        <div class="wp3d-button-wrap"><a href="<?php echo $image['url']; ?>" class="btn" target="_blank"><?php echo apply_filters( 'wp3d_floorplan_image_label', $floorplan_image_label ); ?> &nbsp;<i class="fa fa-external-link"></i></a></div>
    </div>
    <?php $i++; } // end foreach ?>
    
    </section>
    <?php do_action( 'wp3d_single_floorplan_after', get_the_ID() ); ?>
<?php } // end if has $floorplan_images 
// ################# END Floorplans ################# // ?> 
		
<?php // ################# BEGIN Related Models ################# //
	$has_related_models = WP3D_Models()->get_related_models();
	if ($has_related_models) { 
		// okay, we've got related models, lets get a label
		$related_models_heading = __('RELATED MODELS:', 'wp3d-models'); 			
	?>
	<?php do_action( 'wp3d_single_related_before', get_the_ID() ); ?>
	<div id="wp3d-related-models-wrap">
		<h3><?php echo apply_filters( 'wp3d_related_models_heading', $related_models_heading ); ?></h3>
		<?php echo $has_related_models; ?>
	</div>
	<?php do_action( 'wp3d_single_related_after', get_the_ID() ); ?>
<?php } // ################# END Related Models ################# // ?>
		
<?php // ################# BEGIN Sharing ################# //
	if (get_option('wp3d_enable_sharing') && $single_sharing_enabled && $global_sharing_enabled) { 
		// okay, sharing is enabled, lets get a label
		$share_heading = __('SHARE THIS PAGE:', 'wp3d-models');			
	?>
	<?php do_action( 'wp3d_single_sharing_before', get_the_ID() ); ?>
	<div class="wp3d-share-icons-wrap">
		<h3><?php echo apply_filters( 'wp3d_share_heading', $share_heading ); ?></h3>
		<?php do_action( 'wp3d_share_list_before', get_the_ID() ); ?>
		<?php echo WP3D_Models()->get_model_share_list($post_id, false, $is_introed); ?>
		<?php do_action( 'wp3d_share_list_after', get_the_ID() ); ?>
	</div>
	<?php do_action( 'wp3d_single_sharing_after', get_the_ID() ); ?>
<?php } // ################# END Sharing ################# // ?>  
	
<?php // ################# BEGIN Disclaimer ################# //
	if (get_option('wp3d_disclaimer_text')) { ?>
	<?php do_action( 'wp3d_single_disclaimer_before', get_the_ID() ); ?>
	<div id="wp3d-disclaimer-text" class="no-brand">
        <?php 
        echo strip_tags(get_option('wp3d_disclaimer_text'), '<a><img>');
        ?>
	</div>
	<?php do_action( 'wp3d_single_disclaimer_after', get_the_ID() ); ?>
<?php } // ################# END Disclaimer ################# // ?> 
		
		<?php // End the loop.
		endwhile;
		?>
		
<?php } else {  // didn't pass the ACF & password check ?>

		<?php if (post_password_required()) { // password protection in place ?>
		
		<div class="wp3d-password-protected">
			<?php the_content(); // this is here to show the password field ?>
		</div>
		
		<?php } else { // no ACF function 'get_field' ?>

		<div class="entry-content">
			<div class="wp3d-alert wp3d-alert-error"><strong><?php _e( 'BUGGER!', 'WP3D_Models' ); ?></strong>  <?php _e( 'WP3D Models', 'WP3D_Models' ); ?> <a href="/wp-admin/plugins.php" target="_blank"><?php _e( 'needs additional plugins installed', 'WP3D_Models' ); ?></a> <?php _e( 'in order to work correctly!', 'WP3D_Models' ); ?></div>
		</div>
		
		<?php } ?>
		
<?php } // end checking for ACF function 'get_field' && password protection ?>

		</main>
	</div><!-- end #wp3d-single-model -->
	<?php wp_footer(); ?>

	</body>

</html> <!-- end of site. what a ride! -->
