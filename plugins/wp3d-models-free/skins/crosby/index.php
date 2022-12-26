<?php 
// SPECIFIC TO THE DEFAULT SKIN

// This is a little bit of cleanup needed.  We want these skins to be as trim as possible.

add_filter('show_admin_bar', '__return_false'); //admin bar code
remove_action('wp_head', '_admin_bar_bump_cb'); //admin bar css  

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

// remove the default title tag...but (see below) still use Yoast SEO custom Title, if present
// we do it this way to keep the skinned page title as focused as possible, and unrelated to the
// photographer/agent as possible by default, but still customizable via Yoast.
remove_action('wp_head','_wp_render_title_tag', 1);

// Hey Jetpack, beat it.
function wp3d_no_related_posts( $options ) {
    $options['enabled'] = false;
    $options['show_headline'] = false;
    $options['show_thumbnails'] = false;
    return $options;
}
add_filter( 'jetpack_relatedposts_filter_options', 'wp3d_no_related_posts' );

// BUFFERING TO STRIP ALL (POTENTIALLY CONFLICTING) THEME JS & CSS FROM HEADER/FOOTER

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
                     
global $post;

$cache_buster = filemtime(__FILE__);
$skin_path = plugins_url().'/wp3d-models-free/skins/crosby';

// Default Schema Type
$content_schema_type = __('MediaObject', 'wp3d-models'); 

// ########### INTRO CHECK ########### 
$showcase_type = get_field('showcase_branding');
$intro_arr = array('intro-unbranded', 'intro-presented', 'intro-branded', 'intro-cobranded');
if (in_array($showcase_type, $intro_arr)) {  //if the selected showcase type is in our "intro" array, she's introed
	$is_introed = true;
} else {
	$is_introed = false;
}

// PLAY BUTTON
$play_button_html = array(
  'i' => array( 'class' => array() ),
  'img' => array( 'class' => array(), 'src' => array() ),
);
$play_button_tag = '<i class="fa fa-play-circle"></i>';

// MODEL TYPE CHECK
$is_static_image = false;

$wp3d_model_type = get_field('wp3d_model_type'); 
// static_image | threesixtytours | video | generic | matterport
if ($wp3d_model_type == 'static_image') { 
    $is_static_image = true;
} 
if ($wp3d_model_type == '') { // older Models did not have this value set
    $wp3d_model_type = 'matterport';
}

// PRELOAD
$wp3d_preload = get_field('preload_model');
if ($wp3d_model_type == "video") { $wp3d_preload = false; } // force false preload on video

// MATTERPORT MODEL
if ($wp3d_model_type == 'matterport') { 
    
        $mp_incoming = trim(get_field('model_link'));
        $mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
        $mp_start = WP3D_Models()->mp_start_from_url($mp_incoming); // potential deep-link start
        
        // When a Model is saved, we look to Matterport for any saved data and copy it back
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
    
        // URL Params
        // if ( $mp_autoplay || 
        //      $mp_multifloor || 
        //      $mp_force_help || $mp_force_help == 0 ||
        //      $mp_no_showcase_branding || 
        //      $mp_no_guided_tour_panning || 
        //      $mp_looped_guided_tour || 
        //      $mp_no_guided_tour_path || 
        //      $mp_show_highlight_reel || $mp_show_highlight_reel == 0 ||
        //      $mp_autostart_guided_tour ||
        //      $mp_disable_mouse_arrows || get_option('wp3d_disable_skinned_scroll') || // skinned global option
        //      $mp_enable_quickstart ||
        //      $mp_disable_vr ||
        //      $mp_no_showcase_branding_links ||
        //      $showcase_highlight_time ||
        //      $mp_guided_tour_transition ||
        //      $mp_model_zoom ||
        //      $mp_model_pin ||
        //      $mp_model_portal ||
        //      $mp_title_panel || $mp_title_panel == 0 ||
        //      $mp_showcase_tour_cta || $mp_showcase_tour_cta == 0 ||
        //      $mp_vr_limited_mode ||
        //      $mp_dollhouse ||
        //      $mp_mattertags ||
        //      $mp_showcase_language      
        //  ) { 
        //     $mp_params = true; 
        //  } else { 
        //     $mp_params = false; 
        //  }
         
        // tour highlight overlay logo adjust
        if ($mp_show_highlight_reel != '') {
            $overlay_highlight_class = " has-highlight-reel";
        } else {
            $overlay_highlight_class = '';
        }        
            
        // Build the URL and assemble the PARAMS
        // We now check for MPEmbed and/or a separate CDN as part of the mp_get_iframe_url() function...just FYI (June, 2018)
        $wp3d_iframe_src_url = WP3D_Models()->mp_get_iframe_url($mp_incoming, $mp_id).'play=1&amp;'; 
        
        // Additional Param Check
        // if ($mp_params){ 
        //     $wp3d_iframe_src_url .= '&amp;'; 
        // }
        
        // Branding & No-Link Check
        if ($mp_no_showcase_branding) { $wp3d_iframe_src_url .= 'brand=0&amp;'; } 
        if (!empty($mp_no_showcase_branding_links)) { $wp3d_iframe_src_url .= 'mls='.$mp_no_showcase_branding_links.'&amp;'; } 
        
        // Quickstart Check (only added if there is no "HELP")
        if ($mp_enable_quickstart || $global_quickstart) { // user wants quickstart
            if (!$mp_force_help) { // No sign of HELP, go ahead and add the quickstart
                $wp3d_iframe_src_url .=  'qs=1&amp;'; 
            }
        }  
            
        // All other Params
        if ($mp_multifloor) { $wp3d_iframe_src_url .=  'f=0&amp;'; }
        if ($mp_no_guided_tour_panning) { $wp3d_iframe_src_url .=  'kb=0&amp;'; }
        if ($mp_looped_guided_tour) { $wp3d_iframe_src_url .=  'lp=1&amp;'; }
        if ($mp_no_guided_tour_path) { $wp3d_iframe_src_url .=  'guides=0&amp;'; }
        if ($mp_show_highlight_reel != '') { $wp3d_iframe_src_url .=  'hl='.$mp_show_highlight_reel.'&amp;'; } 
        if ($mp_autostart_guided_tour) { $wp3d_iframe_src_url .=  'ts='.$guided_tour_seconds.'&amp;'; }
        if ($mp_force_help != '') { $wp3d_iframe_src_url .= 'help='.$mp_force_help.'&amp;'; }
        if ($mp_disable_mouse_arrows || get_option('wp3d_disable_skinned_scroll')) { $wp3d_iframe_src_url .=  'wh=0&amp;'; } 
        if ($mp_disable_vr) { $wp3d_iframe_src_url .=  'vr=0&amp;'; }     
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
           $wp3d_iframe_src_url .= WP3D_Models()->mp_get_mpembed_params($post->ID, false); 
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
        $tst_mousewheel = get_option('wp3d_disable_skinned_scroll');
            // reset var value if global is set (for skinned only), otherwise check for local
            if ($tst_mousewheel) {$tst_mousewheel = "false"; } else { $tst_mousewheel = get_field('tst_mousewheel'); }
        $tst_socialshare = get_field('tst_socialshare');
        $tst_branding = get_field('tst_branding');   
        // No option for Startscreen on Skinned
        //$tst_startscreen = get_field('tst_startscreen'); 
        
         //UNLIKE MATTERPORT, WE ATTACH ALL PARAMETERS...SO WE APPEND THE "?" REGARDLESS
        $wp3d_iframe_src_url .= '?'; 
        
        // build the URL
        if ($tst_header) { $wp3d_iframe_src_url .=  'header='.$tst_header.'&amp;'; } else { $wp3d_iframe_src_url .=  'header=transparent&amp;'; }
        if ($tst_footer) { $wp3d_iframe_src_url .=  'footer='.$tst_footer.'&amp;'; } else { $wp3d_iframe_src_url .=  'footer=true&amp;'; }
        if ($tst_title) { $wp3d_iframe_src_url .=  'title='.$tst_title.'&amp;'; } else { $wp3d_iframe_src_url .=  'title=false&amp;'; }   
        if ($tst_tournav) { $wp3d_iframe_src_url .=  'tournav='.$tst_tournav.'&amp;'; } else { $wp3d_iframe_src_url .=  'tournav=delayclose&amp;'; }
        if ($tst_mousewheel || get_option('wp3d_disable_skinned_scroll') ) { $wp3d_iframe_src_url .=  'mousewheel='.$tst_mousewheel.'&amp;'; } else { $wp3d_iframe_src_url .=  'mousewheel=true&amp;'; }
        if ($tst_socialshare) { $wp3d_iframe_src_url .=  'socialshare='.$tst_socialshare.'&amp;'; } else { $wp3d_iframe_src_url .=  'socialshare=false&amp;'; }
        if ($tst_branding) { $wp3d_iframe_src_url .=  'brand='.$tst_branding.'&amp;'; } else { $wp3d_iframe_src_url .=  'brand=false&amp;'; }
        // No Startscreen on Skinned
        // if ($tst_startscreen) { $wp3d_iframe_src_url .=  'startscreen='.$tst_startscreen.'&amp;'; } else { $wp3d_iframe_src_url .=  'startscreen=false&amp;'; }
        
        // Parameter Cleanup
        $wp3d_iframe_src_url = preg_replace('/&amp;$/', '', $wp3d_iframe_src_url);
       
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url; 
        $wp3d_id = $tst_id;
}

// VIDEO MODEL
if ($wp3d_model_type == 'video') { 
    
    if (get_field('base_video_type') == 'youtube') {
        
        $base_youtube_url = get_field('base_youtube_video_link');
        $wp3d_iframe_src_url = WP3D_Models()->youtube_embed_from_url($base_youtube_url, true).'&amp;autoplay=1';
        
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;         
        
    } 
    
    if (get_field('base_video_type') == 'vimeo') {
        
        $base_vimeo_url = get_field('base_vimeo_video_link');
        $wp3d_iframe_src_url = WP3D_Models()->vimeo_embed_from_url($base_vimeo_url, true).'&amp;autoplay=1';
        
        // Variable re-assigning
        $wp3d_iframe_data_src = $wp3d_iframe_src_url;          
        
    }
    
}

// GENERIC MODEL
$allow = '';
if ($wp3d_model_type == 'generic') {
    $generic_iframe = trim(get_field('generic_iframe'));
    $wp3d_iframe_data_src = WP3D_Models()->get_iframe_src($generic_iframe);
    $allow = WP3D_Models()->get_iframe_allow($generic_iframe);
    if (!empty($allow)) {
        $allow = '; ' . $allow;
    }
}

// ADDRESS
// This function does all of the heavy lifting re: getting the correct address information for the model map.
// Depending on what has been selected, this address data may come from Matterport, or may be set locally, or may not exist at all.
$address = WP3D_Models()->get_model_address_info($post->ID);

// DEBUG
// print_r($address); exit;

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

// MAPPING
if (isset($address['lat']) && isset($address['lng'])) { $has_map = true; } else { $has_map = false; } 


// VIDEO BG
if (get_field('add_model_video_background') && get_field('youtube_video_background_link')) {
    $has_videobg = true;
    $youtube_bg_video_url = get_field('youtube_video_background_link');  
} else {
    $has_videobg = false;
}

// GALLERY CHECKS
if (WP3D_Models()->get_wp3d_gallery_images()) { $has_gallery = true; } else { $has_gallery = false; }
$wp3d_gallery_type = get_field('gallery_type');

// PROPERTY INFO
if (get_field('add_property_info_details')) { $has_property_info = true; } else { $has_property_info = false; }

// SHARING  
if (get_option('wp3d_enable_sharing')) { $global_sharing_enabled = true; } else { $global_sharing_enabled = false; } // global      
if (get_field('disable_model_sharing')) { $single_sharing_enabled = false; } else { $single_sharing_enabled = true; } // single
if ($global_sharing_enabled && $single_sharing_enabled) { 
    $sharing_enabled = true; 
    $share_heading = __('SHARE THIS PAGE', 'wp3d-models');
} else { 
    $sharing_enabled = false; 
} // absolute

// LARGE/PRIMARY LOGO 
$large_logo_src = WP3D_Models()->get_model_large_logo(get_the_ID()); 
    if (isset($large_logo_src) && $large_logo_src != '') {
        $primary_logo_set = true;
    } 

// SMALL LOGO 
$small_logo_src = WP3d_Models()->get_model_small_logo(get_the_ID()); 
    if (isset($small_logo_src) && $small_logo_src != '') {
        $small_logo_set = true;
    } else {
        $small_logo_set = false; 
    }
    
// SKINNED LOGO LINK CHECK
$wp3d_taxonomy = 'model-client';
$term_list = wp_get_post_terms($post->ID, $wp3d_taxonomy, array("fields" => "ids"));

if (array_key_exists(0, $term_list)) { // if there's a model-client assigned, we need to take a second and look to see if they also have a logo assigned.		
    $term_id = WP3D_Models()->get_primary_taxterm($post->ID, $wp3d_taxonomy, $term_list);
    $term_link = get_field('logo_link_client_override', $wp3d_taxonomy.'_'.$term_id);
} else {
    $term_id = false;
    $term_link = false;
}
$skinned_logo_link = false;

if (get_field('logo_link_override') && get_field('add_override_logos')) { // check for override
    $skinned_logo_link = esc_url(get_field('logo_link_override'));
} elseif ($term_link) { // if there's a model-client logo link
    $skinned_logo_link = $term_link; 
} elseif (get_option('wp3d_logo_link')) { // check for global 
    $skinned_logo_link = esc_url(home_url('/'));
} 

// OVERLAY LOGO CHECK
if (get_field('logo_overlay') && $small_logo_set && $is_introed) { // are we overlaying a logo
    $overlay_logo_set = true;
    
    // If we're force using the Settings/Default logo
    if (get_field('force_default_logo_overlay')) {
        $force_default_logo_overlay = true;
    } else {
        $force_default_logo_overlay = false;
    }
    
} else {
    $overlay_logo_set = false;
}

// CUSTOM TITLES 
if (get_option('wp3d_custom_title')) { $model_title = get_option('wp3d_custom_title'); } else { $model_title = get_bloginfo('name'); }

if (get_post_meta(get_the_ID(), '_yoast_wpseo_title', true)) { 
    $custom_seo_title = true;
    $custom_seo_title_val = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true);
} else {
    $custom_seo_title = false;
}

// AGENTS
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

// no logo, no share, no contact
if (!$small_logo_set && !$sharing_enabled && !$has_agents) { $html_class = ' collapse-head'; } else { $html_class = ''; }

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

$body_taxonomy_classes = apply_filters( 'wp3d_skinned_body_taxonomy_classes', $body_taxonomy_classes );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo $html_class; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    
    <title><?php if ($custom_seo_title) { echo $custom_seo_title_val; } else { the_title(); } ?></title>    
    <?php /* <link rel="canonical" href="<?php the_permalink(); ?>" /> */ ?>
    
    <!-- Favicons -->
    <?php 
    $fav_path = trailingslashit(content_url());
    $this_skin_path = 'plugins/wp3d-models-free/skins/crosby/'; // trailingslashed
    $assembled_fav_path = $fav_path.$this_skin_path;
    ?>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $assembled_fav_path; ?>favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="<?php echo $assembled_fav_path; ?>favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo $assembled_fav_path; ?>favicons/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="<?php echo $assembled_fav_path; ?>favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo $assembled_fav_path; ?>favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="<?php echo $assembled_fav_path; ?>favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?php echo $assembled_fav_path; ?>favicons/manifest.json">
    <link rel="mask-icon" href="<?php echo $assembled_fav_path; ?>favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="<?php echo $assembled_fav_path; ?>favicons/mstile-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <!--[if IE]><link rel="shortcut icon" href="<?php echo $assembled_fav_path; ?>favicons/favicon.ico"><![endif]-->
    
    <!-- Bootstrap -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"> 

    <!--Web Fonts-->
    <?php 
        $wp3d_web_font_1 = '//fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic';
        $wp3d_web_font_2 = '//fonts.googleapis.com/css?family=Oswald:300,400,600,700';
        $wp3d_web_font_1 = apply_filters( 'wp3d_skinned_font_1', $wp3d_web_font_1 ); 
        $wp3d_web_font_2 = apply_filters( 'wp3d_skinned_font_2', $wp3d_web_font_2 ); 
    ?>
    <?php if ($wp3d_web_font_1) { ?>
    <link href='<?php echo $wp3d_web_font_1; ?>' rel='stylesheet' type='text/css'>
    <?php } ?>
    <?php if ($wp3d_web_font_2) { ?>
    <link href='<?php echo $wp3d_web_font_2; ?>' rel='stylesheet' type='text/css'>
    <?php } ?>
    
    <!--Custom CSS-->
    <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo $skin_path; ?>/css/main.css?ver=<?php echo $cache_buster; ?>" rel="stylesheet">
    
<?php if ($has_gallery) { // first check for the gallery
    // then check the gallery type    
    if ($wp3d_gallery_type == 'standard_slider' || $wp3d_gallery_type == '') { ?>
    <!-- Swiper -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/Swiper/3.3.1/css/swiper.min.css">
    <?php  } elseif ( $wp3d_gallery_type == 'zoom_slider' ) { ?>
    <!-- Slick -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css">
<?php } // end the elseif 
    } // end the has gallery check 
?>
<?php if (is_rtl()) { ?>
    <link href="<?php echo $skin_path; ?>/css/main-rtl.css?ver=<?php echo $cache_buster; ?>" rel="stylesheet">
<?php } ?>
<?php
    // Inline style checks
    $add_inline_style_tag = false;
    $add_inline_style = '';

    if (get_option('wp3d_sold_image') && get_option('wp3d_sold_image') != '') { 
        $sold_image_src = WP3D_Models()->get_sold_image();  
        $add_inline_style .= '.wp3d-sold:before { background-image: url(\''.$sold_image_src.'\'); } ';
        $add_inline_style_tag = true;
    }
    
    if (get_option('wp3d_pending_image') && get_option('wp3d_pending_image') != '') { 
        $pending_image_src = WP3D_Models()->get_pending_image();    
        $add_inline_style .= '.wp3d-pending:before { background-image: url(\''.$pending_image_src.'\'); } ';
        $add_inline_style_tag = true;
    }  
    
    if (get_option('wp3d_custom_status_image') && get_option('wp3d_custom_status_image') != '') { 
        $custom_status_image_src = WP3D_Models()->get_custom_status_image();    
        $add_inline_style .= '.wp3d-custom-status:before { background-image: url(\''.$custom_status_image_src.'\'); } ';
        $add_inline_style_tag = true;
    }      
    
    if (get_option('wp3d_custom_css')) { 
        //if there's some additonal custom CSS added, mash it on here.
        $wp3d_custom_css = strip_tags(get_option('wp3d_custom_css')); 
        $add_inline_style .= trim(preg_replace('/\s+/', ' ', $wp3d_custom_css)); // mash it all into one line
        $add_inline_style_tag = true;
    }
    
    if ($add_inline_style_tag == true) { ?>
    <style><?php echo $add_inline_style; ?></style>
    <?php } ?>
    


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <?php 
    // wordpress head functions 
    wp_head(); ?> 
    
    <?php // checking to see if a form has been added and for the existance of reCAPTCHA keys
    if (get_field('add_form') && get_option('wp3d_recaptcha_sitekey') && get_option('wp3d_recaptcha_secretkey')) { ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php } ?>
    
</head>

<?php if (post_password_required()) { // password protection in place ?>

  <body class="password-protected is-loading<?php if (is_rtl()) { echo " rtl"; } ?>">
      
    <div class="entry-content">
        <?php the_content(); // this is here to show the password field ?>
    </div>

  </body>

<?php } else { // no ACF function 'get_field' ?>   

  <body class="wp3d-skinned-crosby model-<?php echo $post->ID; if (is_rtl()) { echo " rtl"; } if ($body_taxonomy_classes) { echo " ".$body_taxonomy_classes; } ?>">
      
<?php
    // ########### INTRO IMAGE CHECK ########### 
    $intro_src_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'mp-intro-size' );
    $intro_src = $intro_src_arr[0];
?>    
    
    <div class="navbar navbar-default navbar-fixed-top" data-spy="affix" data-offset-top="70">
        <div class="container">
            <?php if ($small_logo_set) { ?>
                <?php if ($skinned_logo_link) { ?><a href="<?php echo $skinned_logo_link; ?>" class="logo-link" target="_blank"><?php } ?>
                    <img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="pull-left">
                <?php if ($skinned_logo_link) { ?></a><?php } ?>
            <?php } ?>
          
                <div class="share pull-right">
                    
                    <?php if ($sharing_enabled) { 
                    $fa_share = 'fa-share'; ?>
                    <a href="#" id="share" title="<?php echo esc_attr(apply_filters( 'wp3d_share_heading', $share_heading )); ?>"> 
                        <span class="fa-stack">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa <?php echo esc_attr(apply_filters( 'wp3d_fa_share', $fa_share )); ?> fa-stack-1x fa-inverse"></i>
                        </span>
                    </a>
                    <?php } ?>
                    
                    <?php if(get_field('add_form')) { 
                        $contact_heading = __('CONTACT', 'wp3d-models'); 
                    ?>
                    <a href="#contact" id="email-form" title="<?php echo esc_attr(apply_filters( 'wp3d_contact_heading', $contact_heading )); ?>">
                        <span class="fa-stack">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-envelope fa-stack-1x fa-inverse"></i>
                        </span>
                    </a>
                    <?php } // end add form check ?>
                    <?php if($has_agents && !empty($agents_arr)) {  
                        $agents_heading = __('CONTACT', 'wp3d-models'); 
                    ?>
                    <a href="#" id="agents" title="<?php echo esc_attr(apply_filters( 'wp3d_agents_heading', $agents_heading )); ?>">
                        <span class="fa-stack">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-phone fa-stack-1x fa-inverse"></i>
                        </span>
                    </a>
                    <?php } // end add form check ?>
                </div>

        </div>
    </div>
    
    <div id="page-wrap">
        
        <div class="wp3d-embed-wrap<?php echo $status_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">
            
            <?php if ($has_videobg) { // has video BG ?>
            <div id="wp3d-intro" class="is-loading wp3d-videobg-player" data-property="{videoURL:'<?php echo $youtube_bg_video_url; ?>',containment:'self',autoPlay:true,mute:true,startAt:0,opacity:1,showControls:false}">
                <div id="wp3d-fallbackbg-img" style="background-image: url('<?php echo $intro_src; ?>');"></div>
            <?php } else { // no video BG ?>
            <div id="wp3d-intro" class="is-loading" style="background-image: url('<?php echo $intro_src; ?>');">
            <?php } // END VIDEO BG CHECK ?>
            
            <?php if ($is_static_image) { ?>
                <a href="#" class="no-iframe default-cursor">
                    <div class="wp3d-start">
                        <h1><span><?php the_title(); ?></span></h1>
                    </div>
                </a>            
            <?php } else {  // IS NOT A STATIC IMAGE ?>         
                <a href="#" class="load-iframe">
                    <div class="wp3d-start">
                        <h1><span><?php the_title(); ?></span></h1>
                        <div class="play-button">
	    					<?php echo wp_kses(apply_filters( 'wp3d_play_button', $play_button_tag ), $play_button_html); ?>
                        </div>
                        <div class="message">
                            <?php  
                            if (get_field('custom_showcase_statement') ) { // has a custom statement
                                echo esc_html(get_field('custom_showcase_statement')); 
                            } 
                            ?>
                        </div>
                    </div>
                </a>
            <?php } // END STATIC IMAGE CHECK ?>                
            </div> <!-- /wp3d-intro -->
            
            <?php if (!$is_static_image) { // no need to do all of this iframe genration for a static image ?>

                <?php if ($overlay_logo_set) { // check for the overlay logo
                    if ($force_default_logo_overlay) { // hijack the small logo source if the "Force Default Logo" is set
                        $small_overlay_logo_src = WP3D_Models()->get_settings_small_logo(get_the_ID()); 
                    } else { // use the regular small logo
                        $small_overlay_logo_src = $small_logo_src;
                    }
                ?>
                <img class="iframe-logo-overlay<?php echo $overlay_highlight_class ?>" src="<?php echo $small_overlay_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>">
                <?php } ?>  
               
                <?php echo WP3D_Models()->get_content_schema($post->ID, $wp3d_id, $wp3d_iframe_data_src); ?>
                    
                <iframe id="mp-iframe" src="" <?php if ($wp3d_preload) { echo 'data-preload="true" '; } ?>data-src="<?php echo $wp3d_iframe_data_src; ?>" data-allow="<?php echo $allow; ?>" frameborder="0" allow="vr" allowfullscreen></iframe>
                
            <?php } // end static image check ?>
    
        </div> <!-- /wp3d-embed-wrap -->       
    
    <!--Content Section-->
    
    <section id="content">
        <div class="container">
            <div class="row">
                
                

<?php if ($has_property_info) { ?>                 
                <div class="col-md-8 col-md-offset-0 col-md-push-4 content-wrapper">
<?php } else { ?>
                <div class="col-md-10 col-md-offset-1 content-wrapper">
<?php } ?>

                <?php setup_postdata( $post ); // because we're "outside" WordPress, the $post data needs to be setup
                $model_content = get_the_content(); // checking for local content
                
                // quick fix on 3.2 ACF Bug -- if no content exists, take a quick look at the "hidden/placeholder" WYSIWYG, in case it was stored there.
                if (!$model_content) { 
                    $temp_model_content = get_field('model_content'); 
                } else {
                    $temp_model_content = false;
                }
                
                if (!$model_content && isset($api_data['summary'])) { $summary_wrap = ' summary'; } 
                elseif ( !$model_content && !isset($api_data['summary'])) { $summary_wrap = ' empty-summary'; }
                else { $summary_wrap = ''; }
                if (!$has_property_info) { $summary_wrap .= ' centered-summary'; }
                ?>
                
                    <div class="property-content<?php echo $summary_wrap; ?>">
                    
                        <h2><?php the_title(); ?></h2>
                        <?php if(get_field('model_subtitle')) { ?><h3><?php the_field('model_subtitle'); ?></h3><?php } ?>
                    
                    <?php if ($model_content) { 
                        
                         the_content(); 
                        
                     } elseif ($temp_model_content) {  
                    
                         echo $temp_model_content; 
                        
                     } elseif (isset($api_data['summary'])) { ?>
                    
                        <p><?php echo $api_data['summary']; ?></p>
                    
                    <?php } wp_reset_postdata(); ?>
                
                    </div><!-- end property-content -->
                    
<?php // ################# BEGIN Tabs ################# //

if (get_field('add_property_text_tabs')) { ?>

                    <div id="property-tabs" role="navigation">
                        <?php 
                        
                        if( have_rows('property_text_tabs') ) { // FIRST FOR THE TABS ?>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                        <?php $rows = get_field('property_text_tabs');
                    
                            $i = 0;
                            foreach ($rows as $row) { ?>
                    
                            <li role="presentation"<?php if ($i=='0'){ echo ' class="active"'; } ?>><a href="#tab-<?php echo $i; ?>" aria-controls="tab-<?php echo $i; ?>" role="tab" data-toggle="tab"><?php echo $row['tab_title']; ?></a></li>

                    
                            <?php $i++; } ?>
                        </ul>
                        <?php } // end if have rows
                        
                       if( have_rows('property_text_tabs') ) { // FIRST FOR THE TABS ?>
                        <!-- Tab panes -->
                        <div class="tab-content">
                        <?php $rows = get_field('property_text_tabs');
                    
                            $i = 0;
                            foreach ($rows as $row) { ?>
                    
                            <div role="tabpanel" class="tab-pane<?php if ($i=='0'){ echo ' active'; } ?>" id="tab-<?php echo $i; ?>">
                                <?php echo $row['tab_wysiwyg']; ?>
                            </div><!-- /tab-pane -->
                    
                            <?php $i++; } ?>
                        </div><!-- end tab-content -->
                        <?php } // end if have rows ?>
                       
                    
                    </div><!-- end #property-tabs -->
                  

<?php } // ################# END Tabs ################# //  ?>                    
                     
           </div><!-- end content wrapper -->
<?php 

if ($has_property_info) { 
    
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

                <div class="col-md-4 col-md-pull-8 col-md-offset-0">
                     
                    <!--Property Info-->
                    
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
                    
                <?php  ?>

                </div><!-- end prop info (pull 8) -->
                 
<?php 
        
    } // end if have rows

} // end has_property_info 

?>

                </div><!-- end row -->
                 
            </div><!-- end container --> 
            
<?php do_action( 'wp3d_skinned_video_before', get_the_ID() ); ?>         
<?php // ################# BEGIN Video // ################# //
if (get_field('add_video')) { 
    
    $has_video = get_field('add_video');
    $youtube_url = get_field('youtube_video_link');
    $vimeo_url = get_field('vimeo_video_link');    
    
    if( $youtube_url || $vimeo_url ) {
?>    
    <div id="wp3d-video-container" class="container">
        <div id="wp3d-video">

<?php if ($has_video == "youtube") {
        echo WP3D_Models()->youtube_embed_from_url( $youtube_url );
} elseif ($has_video == "vimeo") {
        echo WP3D_Models()->vimeo_embed_from_url( $vimeo_url );
} ?>
        </div>
    </div>
    
<?php  } // end if youtube or vimeo isn't empty

}  // ################# END Video ################# // ?>
<?php do_action( 'wp3d_skinned_video_after', get_the_ID() ); ?>  
    </section>
    
 
<?php do_action( 'wp3d_skinned_map_before', get_the_ID() ); ?>  
<?php // ################# BEGIN Map // ################# //
if ($has_map) {
    
// DEBUG
// print_r($address); exit;

?>

    <!--Map Section-->
    
    <section id="wp3d-map-wrap">
        

    <?php // apply the returned $address data 

        if (!empty($address)){  
            $address_lat = $address['lat']; 
            $address_lng = $address['lng'];
        } else {
            $address_lat = '';
            $address_lng = '';
        }

        
        if ($address_lat != '' && $address_lng != '' ) { // final check to show a map 
            if(get_option('wp3d_single_page_map_zoom')) { $map_zoom = get_option('wp3d_single_page_map_zoom'); } else { $map_zoom = '6'; } // override zoom
            if(get_option('wp3d_single_page_map_type')) { $map_type = get_option('wp3d_single_page_map_type'); } else { $map_type = 'ROADMAP'; } // override map type
            if(get_option('wp3d_marker_type')) { $marker_type = get_option('wp3d_marker_type'); } else { $marker_type = 'circle'; } // marker type
        ?>
        
        <div id="wp3d-single-map" data-map-zoom="<?php echo $map_zoom; ?>" data-map-type="<?php echo $map_type; ?>" data-latlng="<?php echo $address_lat;?>,<?php echo $address_lng;?>" data-marker-type="<?php echo $marker_type; ?>"></div>
        
    <?php } // end final check ?>
        
    </section>  
<?php }  // ################# END Map ################# // ?>  
<?php do_action( 'wp3d_skinned_map_after', get_the_ID() ); ?>

<?php do_action( 'wp3d_skinned_gallery_before', get_the_ID() ); ?>
<?php // ################# BEGIN Gallery // ################# //
if ($has_gallery) { 

    $images = WP3D_Models()->get_wp3d_gallery_images();

    if( $images ) {
        
        if ($wp3d_gallery_type == 'standard_slider' || $wp3d_gallery_type == '') {
?> 

    <section id="gallery">
        
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php foreach( $images as $image ): ?>
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
                    
                    <div class="wp3d-zoom-gallery-caption">
                        
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
    
    } // end $images check

} // end $has_gallery

//  ################# END Gallery ################# // ?>
<?php do_action( 'wp3d_skinned_gallery_after', get_the_ID() ); ?>

<?php do_action( 'wp3d_skinned_thirdparty_before', get_the_ID() ); ?>
<?php // ################# BEGIN Third Party Content ################# //
if (get_field('add_smart_gallery') && get_field('smart_gallery_url')) {
    echo WP3D_Models()->get_smart_gallery(); 
} // ################# END Third Party Content ################# // ?> 
<?php do_action( 'wp3d_skinned_thirdparty_after', get_the_ID() ); ?>

<?php do_action( 'wp3d_skinned_floorplans_before', get_the_ID() ); ?>
<?php // ################# BEGIN Floorplans ################# //
$floorplan_images = get_field('floorplan_gallery');
$floorplan_image_label = __('Image Link', 'wp3d-models');

if( $floorplan_images && get_field('add_floorplan') ) { ?>

    <section id="floorplans">
        
    <?php do_action( 'wp3d_skinned_floorplans_inside_before', get_the_ID() ); ?>         

    <ul id="floorplan-images">
        <?php $i=0; foreach( $floorplan_images as $image ) { ?>
            <li>
                <a href="#" title="<?php echo esc_attr($image['title']); ?>" style="background-image: url('<?php echo $image['sizes']['thumbnail']; ?>');" data-toggle="modal" data-target=".floorplan-<?php echo $i; ?>"></a>
            </li>
        <?php $i++; } // end foreach ?>
    </ul>
    
    <?php $i=0; foreach( $floorplan_images as $image ) { ?> 
    
    <?php //print_r($image); ?>
        
    <div class="modal fade floorplan-<?php echo $i; ?>" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-heading clearfix">
              <a href="#" class="close-modal" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></a>
            </div>            
            <img src="" data-src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>">
            <?php if(isset($image['title'])) { ?><h3><?php echo $image['title']; ?></h3><?php } ?>
            <?php if(isset($image['caption'])) { ?><p><?php echo $image['caption']; ?></p><?php } ?>
          <div class="btn-grey"><a href="<?php echo $image['url']; ?>" target="_blank"><?php echo apply_filters( 'wp3d_floorplan_image_label', $floorplan_image_label ); ?> &nbsp;<i class="fa fa-external-link"></i></a></div>
        </div>
      </div>
    </div>
    
    <?php $i++; } // end foreach ?>
    
    </section>

<?php } // end if has $floorplan_images 
// ################# END Floorplans ################# // ?> 
<?php do_action( 'wp3d_skinned_floorplans_after', get_the_ID() ); ?> 

<?php do_action( 'wp3d_skinned_agents_before', get_the_ID() ); ?> 
<?php // ################# BEGIN Agents ################# //
 if($has_agents && !empty($agents_arr)) {  // if there is agent data, lets sift through it and return it
 //print_r($agents_arr);
 ob_start();
 ?>
    
    <section id="agents-section">
        <div class="container">
            <ul class="agents-list clearfix">
                
                <?php 
                if (count($agents_arr) == 1) { $agent_class = " mp-contact-info"; } else { $agent_class = ''; }
                
                foreach ($agents_arr as $agent) { ?>
                
                <li class="vcard agent" itemscope itemtype="http://schema.org/Person">
                    <?php if(isset($agent['image_src'])) { ?>
                    <meta itemprop="image" content="<?php echo esc_attr($agent['image_src']); ?>"></meta>
                    <div class="agent-photo">
                        <div class="agent-img" style="background-image: url('<?php echo esc_attr($agent['image_src']); ?>')">
                            <span class="sr-only"><?php echo esc_attr($agent['name']); ?></span>
                        </div>
                    </div>
                    <?php } else { $agent_class .= " no-image"; } ?>
                    <div class="agent-content<?php echo $agent_class; ?>">
                        <?php if (isset($agent['name'])) { ?><span class="fn" title="<?php echo esc_attr($agent['name']); ?>" itemprop="name"><?php echo esc_html($agent['name']); ?></span><?php } ?>
                        <?php if (isset($agent['subheading'])) { ?><span class="sub-heading"><?php echo esc_html($agent['subheading']); ?></span><?php } 
                        
                        if (isset($agent['add-agent-info'])) { // start add agent info check
                        
                            if (isset($agent['additional-info'])) { // Agent Address/WYSIWYG ?>
                                <div class="agent-additional-info">
                                <?php echo html_entity_decode($agent['additional-info']); ?>
                                </div>
                            <?php } 
                            
                            if (isset($agent['logo_src'])) { // Agent Logo ?>
                                <div class="agent-logo">
                                    <img src="<?php echo esc_attr($agent['logo_src']); ?>" alt="<?php echo esc_attr($agent['logo_alt']); ?>" />
                                </div>
                            <?php } 
                        
                        } // end add agent info check                        
                        
                        // checking for agent meta
                        if ( 
                            isset($agent['email']) || 
                            isset($agent['phone']) ||
                            isset($agent['phone-mobile']) || 
                            isset($agent['phone-direct']) || 
                            isset($agent['phone-office']) 
                            ) { 
                                $agent_meta = true; 
                            } else {
                                $agent_meta = false;
                            }
                    
                        if ($agent_meta) { ?>
                        
                        <ul class="agent-meta fa-ul">
                        <?php } ?>
                        
                            <?php if(isset($agent['email'])) { ?>
                            <li class="email">
                                <i class="fa-li fa fa-envelope"></i><a href="mailto:<?php echo sanitize_email($agent['email']); ?>" itemprop="email"><?php echo esc_html($agent['email']); ?></a>
                            </li>
                            <?php } ?>
                            <?php if(isset($agent['phone'])) { // this one comes from Matterport ONLY ?>
                            <li class="tel">
                                <i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone'])); ?>" class="value" itemprop="telephone"><?php if (isset($agent['formatted_phone']) ) { echo esc_html($agent['formatted_phone']); } else { echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone'])); } ?></a>
                            </li>
                            <?php } ?>                            
                            <?php if(isset($agent['phone-mobile'])) { 
                            $mobile_label = __('MOBILE', 'wp3d-models');    
                            ?>
                            <li class="tel">
                                <i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone-mobile'])); ?>" class="value" itemprop="telephone"><?php echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone-mobile'])); ?></a>
                                <?php // Show the "MOBILE" label if either of the other phone fields have values, otherwise, no label
                                if (isset($agent['phone-direct']) || isset($agent['phone-office'] )) { ?>
                                <span class="type">(<?php echo apply_filters( 'wp3d_mobile_label', $mobile_label ); ?>)</span>
                                <?php } // end only one phone number check ?>
                            </li>
                            <?php } ?>
                            <?php if(isset($agent['phone-direct'])) { 
                            $direct_label = __('DIRECT', 'wp3d-models');
                            ?>
                            <li class="tel">
                                <i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone-direct'])); ?>" class="value" itemprop="telephone"><?php echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone-direct'])); ?></a>
                                <span class="type">(<?php echo apply_filters( 'wp3d_direct_label', $direct_label ); ?>)</span>
                            </li>
                            <?php } ?>
                            <?php if(isset($agent['phone-office'])) { 
                            $office_label = __('OFFICE', 'wp3d-models');
                            ?>
                            <li class="tel">
                                <i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone-office'])); ?>" class="value" itemprop="telephone"><?php echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone-office'])); ?></a>
                                <span class="type">(<?php echo apply_filters( 'wp3d_office_label', $office_label ); ?>)</span>
                            </li>
                            <?php } ?>
                        <?php if ($agent_meta) { ?>
                        </ul>
                        <?php } ?>
                        
                        <?php
                            if (!empty($agent['calendly_enabled']) && $agent['calendly_type'] == 'text') :
                                $url = $agent['calendly_event_link'];
                                $text = !empty($agent['custom_link_title']) ? $agent['custom_link_title'] : 'Schedule Tour';
                        ?>
                            <div>
                                <a href="" class="wp3d_calendly_box_link" onclick="Calendly.initPopupWidget({url: '<?=$url?>'});return false;"><?=$text?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php // checking for share meta
                        if( isset($agent['links']) ) { 
                        $agent_links = true; 
                        ?>
                        <ul class="wp3d-share-icons">
          
                            <?php foreach ($agent['links'] as $link) { 
                            
                            if (strpos($link,'facebook')) { $social = "facebook"; }
                            elseif (strpos($link,'twitter')) { $social = "twitter"; }
                            elseif (strpos($link,'linkedin')) { $social = "linkedin"; }
                            elseif (strpos($link,'instagram')) { $social = "instagram"; }
                            elseif (strpos($link,'youtube')) { $social = "youtube"; } 
                            elseif (strpos($link,'google')) { $social = "google"; } 
                            elseif (strpos($link,'yelp')) { $social = "yelp"; }                             
                            else { $social = "globe"; }
                            
                            ?>
                            <li class="<?php echo $social; ?>">
                                <a href="<?php echo esc_url($link); ?>" target="_blank" class="url" itemprop="url">
                                    <span class="fa-stack fa-2x">
                                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                                        <i class="fa fa-<?php echo $social; ?> fa-stack-1x <?php echo $social; ?>"></i>
                                    </span>                     
                                </a>
                            </li>
                            <?php } // end link foreach ?>
                        </ul>
                        <?php } // end checking for agent links ?>
                        <?php if ( isset($agent['bio']) ) { ?>
                        <a href="#" class="agent-bio">More About <?php echo esc_html($agent['name']);  ?></a> 
                        <?php } ?>
                    </div>
                </li>
                
                <?php } ?>

            </ul>
        </div>
    </section>
    
 <?php 

    $agents_html = ob_get_clean();
    echo $agents_html;
} // ################# END Agents ################# // ?>
<?php do_action( 'wp3d_skinned_agents_after', get_the_ID() ); ?> 
    
<?php do_action( 'wp3d_skinned_form_before', get_the_ID() ); ?>     
<?php // ################# BEGIN Contact Form ################# //

// first look to see if a form has been enabled at all
if (get_field('add_form')) {

        if (get_field('lead_generation_form_type') == 'shortcode' && get_field('form_shortcode') != '' ) { // 3rd party form, yer on yer own ?>

        <section id="contact">
            <div class="container shortcode-contact">
            <?php 
            echo do_shortcode(get_post_meta( $post->ID, 'form_shortcode', true)); // gotta use raw WP 'get_post_meta' function. 
            ?>
            </div>
        </section>

        <?php } else { // not using a shortcode, just gotta 
            
            if (get_field('lead_generation_form_type') == 'default-custom' && get_field('default_send_to_email_address') != '') {
            
                $email_to = get_field('default_send_to_email_address');
                $email_to = sanitize_email($email_to); // clean er up
            
            } elseif ( get_field('lead_generation_form_type') == 'default-api' || get_field('lead_generation_form_type') == 'default-agents' ) { // trying to use the Matterport API data
            
                if($has_agents && !empty($agents_arr)) { // there are agents, proceed with enabling the form
            
                    // where to send the form?  Build a list of 1-3 addresses
                    if (isset($agents_arr[0]['email'])) { $email_to = sanitize_email($agents_arr[0]['email']); }
                        if (isset($agents_arr[0]['add-form-bcc-email'])) { $email_to .= ','.sanitize_email($agents_arr[0]['add-form-bcc-email']); }
                    if (isset($agents_arr[1]['email'])) { $email_to .= ','.sanitize_email($agents_arr[1]['email']); }
                        if (isset($agents_arr[1]['add-form-bcc-email'])) { $email_to .= ','.sanitize_email($agents_arr[1]['add-form-bcc-email']); }
                    if (isset($agents_arr[2]['email'])) { $email_to .= ','.sanitize_email($agents_arr[2]['email']); }
                        if (isset($agents_arr[2]['add-form-bcc-email'])) { $email_to .= ','.sanitize_email($agents_arr[2]['add-form-bcc-email']); }
                }
            }
                
            if (isset($email_to)) { // rolling our own form...but we gotta have at least one emailTo address set
            
            // DEBUG
            //echo $email_to; exit;
            
                // TEXT FOR FILTERS
                $name_placeholder = __('ENTER YOUR NAME', 'wp3d-models');
                $email_placeholder = __('ENTER YOUR EMAIL', 'wp3d-models'); 
                $phone_placeholder = __('ENTER YOUR PHONE', 'wp3d-models'); 
                $message_placeholder = __('MESSAGE', 'wp3d-models'); 
                $send_message_button = __('Send Message', 'wp3d-models'); 
                $message_success = __('Thanks, your email was sent! We\'ll be in touch shortly.', 'wp3d-models');
                $message_error = __('Sorry, an error occured. Please try again.', 'wp3d-models');
                $capture_content = __('I consent to this submitted data being collected and stored.', 'wp3d-models');
                
                // FILTER LABELS
                $inquiry_from_label = __('Inquiry from:', 'wp3d-models');
                $inquiry_name_label = __('Name:', 'wp3d-models');
                $inquiry_email_label = __('Email:', 'wp3d-models');
                $inquiry_phone_label = __('Phone:', 'wp3d-models');
                $inquiry_property_label = __('Property:', 'wp3d-models');
                $inquiry_url_label = __('URL:', 'wp3d-models');
                $inquiry_comments_label = __('Comments:', 'wp3d-models');
                                
                // START WITH NOTHIN
                $name_error = '';
                $name_class = '';
                $email_error = '';
                $email_class = '';
                $captcha_class = '';
                $captcha_error = '';
                $capture_content_error ='';
                $capture_content_class ='';
    
                if(isset($_POST['submitted'])) {
    
                    $url = trailingslashit(get_permalink()).'skinned/';
                    $property = get_the_title();
                    
                    // check for the existance of the recaptcha keys
                    if (get_option('wp3d_recaptcha_sitekey') && get_option('wp3d_recaptcha_secretkey')) { 
                    
                        if($_POST['g-recaptcha-response'] === ''){
                            $captcha_error = __('Please select the CAPTCHA.', 'wp3d-models');
                            $form_error = true; 
                            $captcha = false;
                        } else {
                            $captcha = $_POST['g-recaptcha-response'];
                            $captcha_request = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.get_option('wp3d_recaptcha_secretkey').'&response='.$captcha.'&remoteip='.$_SERVER['REMOTE_ADDR']);
                            $captcha_response = wp_remote_retrieve_body( $captcha_request );
                            $captcha_response = json_decode($captcha_response);                              
                        }  
                    
                    // if no recaptcha keys, manually set the checked value to let the form submit below
                    } else {
                        $captcha_response = new stdClass(); // empty object
                        $captcha_response->success = true; // setting object value
                    } 
    
                    if(trim($_POST['contactName']) === '') {
                        $name_error = __('Please enter your name.', 'wp3d-models');
                        $form_error = true;
                    } else {
                        $name = trim($_POST['contactName']);
                    }
        
                    if(trim($_POST['email']) === '')  {
                        $email_error = __('Please enter a valid email address.', 'wp3d-models');
                        $form_error = true;
                    } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,6}$/i", trim($_POST['email']))) {
                        $email_error = __('Please enter a valid email address.', 'wp3d-models');
                        $form_error = true;
                    } else {
                        $email = trim($_POST['email']);
                        $from_email = $email;
                        if (strpos($email,'yahoo') !== false || strpos($email,'aol') !== false) { // these two troublemakers
                            $domain_name =  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
                            $from_email = 'noreply@'.$domain_name;
                        }
                    }
                    
                    // Consent Checkbox (if enabled)
                    if (get_option('wp3d_enable_form_capture_content')) { 
                        if(!isset($_POST['captureContent'])) {
                            $capture_content_error = __('Please give your permission to submit this form.', 'wp3d-models');
                            $form_error = true;
                        }
                    }
                    
                    $phone = trim($_POST['phone']);
    
                    if(function_exists('stripslashes')) {
                        $comments = stripslashes(trim($_POST['comments']));
                    } else {
                        $comments = trim($_POST['comments']);
                    }

                    // skip all this, if we have errors
                    if(!isset($form_error)) {

                        if(isset($_POST['antispam']) && $_POST['antispam'] == '' && $captcha_response->success === true) {
                            
                                if (!isset($email_to) || ($email_to == '') ){
                                    $email_to = get_option('admin_email');
                                }

                                $subject = apply_filters( 'wp3d_inquiry_from_label', $inquiry_from_label ).' '.$name;
                                $body = 
                                apply_filters( 'wp3d_inquiry_name_label', $inquiry_name_label )." $name \n\n".
                                apply_filters( 'wp3d_inquiry_email_label', $inquiry_email_label )." $email \n\n".
                                apply_filters( 'wp3d_inquiry_phone_label', $inquiry_phone_label )." $phone \n\n".
                                apply_filters( 'wp3d_inquiry_property_label', $inquiry_property_label )." $property \n\n".
                                apply_filters( 'wp3d_inquiry_url_label', $inquiry_url_label )." $url \n\n".
                                apply_filters( 'wp3d_inquiry_comments_label', $inquiry_comments_label )." $comments";
                                
                                $headers = 'From: '.$name.' <'.$from_email.'>' . "\r\n" . 'Reply-To: ' . $from_email;
            
                                wp_mail($email_to, $subject, $body, $headers);
                                $email_sent = true;
                            
                        } else {
                            $email_sent = true; // make spammer think message went through
                        }

                    }
    
            } // end is post submitted  ?>
            
    <section id="contact">
        
        <?php if(isset($email_sent) && $email_sent == true) {   ?>
            <div id="redirect-to" class="alert-message alert-success">
                <div class="container">
                    <p role="alert"><?php echo apply_filters( 'wp3d_message_success', $message_success ); ?></p>
                </div>
            </div>
        <?php } else { ?>
    
        <?php if(isset($form_error)) { ?>
            <div id="redirect-to" class="alert-message alert-danger">
                <div class="container">
                    <p role="alert"><?php echo apply_filters( 'wp3d_message_error', $message_error ); ?></p>
                </div>
            </div>
        <?php } ?>    
    
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 contact">
            <?php 
            $contact_subtitle = __('Please use the form below to reach out.', 'wp3d-models'); 
            ?>
            <h2><?php echo apply_filters( 'wp3d_contact_heading', $contact_heading ); ?></h2>
            <h3><?php echo apply_filters( 'wp3d_contact_subtitle', $contact_subtitle ); ?></h3>
        </div>
    
        <div class="container contact-form">
            <form id="contact-form" action="<?php echo trailingslashit(get_permalink()); ?>skinned/#redirect-to" method="post">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php if($name_error != '') { $name_class = 'error'; ?>
                            <label class="error"><i class="fa fa-exclamation-triangle"></i> <?php echo apply_filters( 'wp3d_name_error', $name_error ); ?></label>
                        <?php } ?>
                        <input name="contactName" type="text" id="contactName" class="<?php echo $name_class; ?>" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" placeholder="<?php echo apply_filters( 'wp3d_name_placeholder', $name_placeholder ); ?>" >
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php if($email_error != '') { $email_class = 'error'; ?>
                            <label class="error"><i class="fa fa-exclamation-triangle"></i> <?php echo apply_filters( 'wp3d_email_error', $email_error ); ?></label>
                        <?php } ?>                        
                        <input name="email" type="text" id="email" class="<?php echo $email_class; ?>" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" placeholder="<?php echo apply_filters( 'wp3d_email_placeholder', $email_placeholder ); ?>" >
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input name="phone" type="text" id="phone" value="<?php if(isset($_POST['phone']))  echo $_POST['phone'];?>" placeholder="<?php echo apply_filters( 'wp3d_phone_placeholder', $phone_placeholder ); ?>" >
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <textarea name="comments" id="commentsText" rows="8" placeholder="<?php echo apply_filters( 'wp3d_message_placeholder', $message_placeholder ); ?>"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
                        <input style="display: none;" type="text" name="antispam" />
                    </div>
                </div>
                
                <?php if (get_option('wp3d_enable_form_capture_content')) { ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                    <div class="checkbox">
                        <?php if($capture_content_error != '') { $capture_content_class = 'error'; ?>
                            <label class="error"><i class="fa fa-exclamation-triangle"></i> <?php echo apply_filters( 'wp3d_capture_content_error', $capture_content_error ); ?></label>
                        <?php } ?>                           
                        <label><input type="checkbox" name="captureContent" id="captureContent" value="yes"<?php if(isset($_POST['captureContent'])) { echo ' checked="checked"'; } ?>> <?php echo apply_filters( 'wp3d_form_capture_content_message', $capture_content ); ?></label>
                    </div>                    
                </div> 
                <?php } ?>                
                
                <?php if (get_option('wp3d_recaptcha_sitekey') && get_option('wp3d_recaptcha_secretkey')) { ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                        <?php if($captcha_error != '') { $captcha_class = 'error'; ?>
                            <label class="error"><i class="fa fa-exclamation-triangle"></i> <?php echo apply_filters( 'wp3d_captcha_error', $captcha_error ); ?></label>
                        <?php } ?>                  
                    <div class="g-recaptcha <?php echo $captcha_class; ?>" data-sitekey="<?php echo get_option('wp3d_recaptcha_sitekey'); ?>"></div>
                </div>
                <?php } ?>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input class="btn" type="submit" value="<?php echo apply_filters( 'wp3d_send_message_button', $send_message_button ); ?>" id="sendmsg" onclick="__gaTracker('send', 'event', 'button', 'click', 'skinned-form-submit')">
                    </div>
                </div>
                <input type="hidden" name="submitted" id="submitted" value="true" />
            </form>
        </div>
    
    </section>              
                
            <?php } // end success check
    
            } // end making sure we had an $email_to address
            
    } // end rolling our own form (not shortcode)
    
} // ################# END Form ################# // ?>
<?php do_action( 'wp3d_skinned_form_after', get_the_ID() ); ?>

<?php do_action( 'wp3d_skinned_sharing_before', get_the_ID() ); ?>
<?php // ################# BEGIN Sharing ################# //
if ($sharing_enabled) { ?>
    <section id="footer" class="clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 footer">

        <div class="wp3d-share-icons-wrap">
            <h3><?php echo apply_filters( 'wp3d_share_heading', $share_heading ); ?></h3>
            <?php do_action( 'wp3d_share_list_before', get_the_ID() ); ?>   
            <?php echo WP3D_Models()->get_model_share_list($post->ID, true, true); // the 'true' here forces links to point to the "skinned" version ?>
            <?php do_action( 'wp3d_share_list_after', get_the_ID() ); ?>   
        </div>
          
        </div>
    </section>
<?php } // ################# END Sharing ################# // ?> 
<?php do_action( 'wp3d_skinned_sharing_after', get_the_ID() ); ?>
 
<?php do_action( 'wp3d_skinned_disclaimer_before', get_the_ID() ); ?> 
<?php // ################# BEGIN Disclaimer ################# //
if(get_option('wp3d_disclaimer_text') || (get_option('wp3d_hide_branding') != 'on' && get_option('wp3d_hide_branding') !== false)) { ?>    
<section id="disclaimer" class="clearfix">
    <div class="container">    
    
    <?php if (get_option('wp3d_disclaimer_text')) { 
        $has_disclaimer = true; 
    ?> 
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
        <?php 
        echo strip_tags(get_option('wp3d_disclaimer_text'), '<a><img>');
        ?>
    </div>
    <?php } else { 
        $has_disclaimer = false;
    }// end disclaimer check ?>
    
    <?php if (get_option('wp3d_hide_branding') != 'on' && get_option('wp3d_hide_branding') !== false) { // checking
        // for option to hide the WP3D Models
        // branding" 
      
        $wp3d_credit_link = "https://wp3dmodels.com";
        $wp3d_credit_link = apply_filters( 'wp3d_custom_credit_link', $wp3d_credit_link );
      
        if ($has_disclaimer) { 
            $credit_class = 'col-lg-4 col-md-4 col-sm-12 col-xs-12 text-right'; 
        } else { 
            $credit_class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center'; 
        }
    ?>
    <div id="wp3d-credit" class="<?php echo $credit_class; ?>">
        <a href="<?php echo esc_url($wp3d_credit_link); ?>" target="_blank">
            <?php _e('Powered by WP3D Models', 'wp3d-models'); ?>
        </a>
    </div>
    <?php } // end hide branding check ?>
    </div>
</section>
<?php } // ################# END Disclaimer ################# // ?> 
<?php do_action( 'wp3d_skinned_disclaimer_after', get_the_ID() ); ?> 
    
</div><!-- end page wrap -->
    
<?php // ################# BEGIN Sharing Overlay ################# //
if ($sharing_enabled) { 
?>
<div id="share-overlay" class="overlay">
    
    <?php if ($small_logo_set) { ?>
        <img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
    <?php } ?>                  
    
    
        <div class="wp3d-share-icons-wrap">
    
            
            <h2><?php the_title(); ?></h2>
            <h3><?php echo apply_filters( 'wp3d_share_heading', $share_heading ); ?></h3>
            
            <?php do_action( 'wp3d_share_list_before', get_the_ID() ); ?>
            <?php echo WP3D_Models()->get_model_share_list(get_the_ID(), true, true); ?>
            <?php do_action( 'wp3d_share_list_after', get_the_ID() ); ?>

        </div>
        
    <a href="#" class="close-overlay"><i class="fa fa-times"></i></a>
</div>
<?php } // ################# END Sharing Overlay ################# // ?>    
    
    
<?php // ################# BEGIN Agents Overlay ################# //
if($has_agents && !empty($agents_arr)) { 
?>
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
                            <?php if( isset($agent['phone'])) { // this one comes from Matterport ONLY, including the "formatted phone" that is part of this conditional ?><li class="tel"><i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone'])); ?>" class="value"><?php if (isset($agent['formatted_phone']) ) { echo esc_html($agent['formatted_phone']); } else { echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone'])); } ?></a></li>
                            <?php } ?>                            
                            <?php if(isset($agent['phone-mobile'])) { ?><li class="tel"><i class="fa-li fa fa-phone"></i><a href="tel:<?php echo esc_attr(WP3D_Models()->get_trimmed_phone($agent['phone-mobile'])); ?>" class="value"><?php echo esc_html(WP3D_Models()->get_formatted_phone($agent['phone-mobile'])); ?></a></li><?php } ?>
                        </ul>
                    </div>
                </li>
        
            <?php } ?>
            </ul>

        </div>
        
    <a href="#" class="close-overlay"><i class="fa fa-times"></i></a>
</div>
<?php } // ################# END Agents Overlay ################# // ?>

    <?php do_action( 'wp3d_skinned_footer_before', get_the_ID() ); ?>
    
    <?php wp_footer(); ?>
    
    <?php do_action( 'wp3d_skinned_footer', get_the_ID() ); ?>

    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<?php if ($has_videobg) { ?>
    <!-- YTPlayer -->    
    <script src="<?php echo $skin_path; ?>/js/jquery.mb.YTPlayer.min.js"></script>
<?php } ?>
<?php if ($has_gallery) { 
    if ( $wp3d_gallery_type == 'standard_slider' || $wp3d_gallery_type == '' ) { ?>
    <!-- Swiper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.3.1/js/swiper.jquery.min.js"></script>
<?php } elseif ( $wp3d_gallery_type == 'zoom_slider' ) { ?>
    <!-- Slick -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js"></script>    
    <!-- Featherlight -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/featherlight/1.7.0/featherlight.min.js"></script>
<?php } // end elseif

} // end $has_gallery ?>
    <script src="<?php echo $skin_path; ?>/js/jquery.easing.1.3.js"></script>
    <script src="<?php echo $skin_path; ?>/js/custom.js?ver=<?php echo $cache_buster ?>"></script>
    
    <?php do_action( 'wp3d_skinned_footer_after', get_the_ID() ); ?>

<?php } // end password protection check ?>
    </body>

</html>
