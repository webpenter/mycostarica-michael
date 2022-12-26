<?php
/**
 * The template for displaying all model "single" posts
 * USE THIS AS THE FOUNDATION FOR ANY CUSTOMIZATIONS & PLACE A COPY IN YOUR THEME'S ROOT
 * REMEMBER THAT THIS FILE MAY GET UPDATED WITH FUTURE PLUGIN UPDATES.  USE AT YOUR OWN RISK!
 *
 */
 
get_header(); 

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

<?php do_action( 'wp3d_single_model_wrap_before', get_the_ID() ); ?>
	<div id="wp3d-single-model" class="wp3d-content-area">
		<main id="main" class="site-main" role="main">
			
<?php if ( function_exists('get_field') && !post_password_required() ) { // true if ACF function 'get_field' exists && model is NOT password protected ?>	
			
		<header class="entry-header wp3d-entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<?php if(get_field('model_subtitle')) { ?><h2><?php the_field('model_subtitle'); ?></h2><?php } ?>
		</header><!-- .entry-header -->

		<?php 
		// PLUGINS DIR
		$plugins_url = plugins_url();
		
		// DEFAULT INTRO STATEMENT
		$default_intro_statement = __('START TOUR', 'wp3d-models');		
		$primary_logo_set = false;
		$small_logo_set = false;
		
		// Start the loop.
		while ( have_posts() ) : the_post();
		
		// LETS GET SOME MODEL DATA
		$post_id = get_the_ID(); 
		
		// ########### INTRO IMAGE CHECK ########### 
		// Calling the 'mp-intro-size' (1920 x 1080) in case the image that was uploaded was bigger
		$intro_src_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'mp-intro-size' );
        $intro_src = $intro_src_arr[0];	
		
		// ########### MODEL TYPE CHECK ########### 
		$is_static_image = false;

		$wp3d_model_type = get_field('wp3d_model_type'); // static_image | threesixtytours | video | generic | matterport
		if ($wp3d_model_type == "static_image") { 
			$is_static_image = true;
		}
		if ($wp3d_model_type == '') { // older Models did not have this value set
		    $wp3d_model_type = 'matterport';
		}

		// ########### SCHEMA ########### 
		$content_schema_type = __('MediaObject', 'wp3d-models'); 		

		// ########### INTRO CHECK ########### 
		$showcase_type = get_field('showcase_branding');
		$intro_arr = array('intro-unbranded', 'intro-presented', 'intro-branded', 'intro-cobranded');
		if (in_array($showcase_type, $intro_arr)) {  //if the selected showcase type is in our "intro" array, she's introed
			$is_introed = true;

			// cobrand check
			if ($showcase_type == 'intro-cobranded') {
				$is_cobranded = true;
			} else {
				$is_cobranded = false;
			}
	        
		} else {
			$is_introed = false;
			$is_cobranded = false;
		}
		
		
		// ########## PlAY BUTTON ###########
		$play_button_html = array(
		  'i' => array( 'class' => array() ),
		  'img' => array( 'class' => array(), 'src' => array() ),
		);
		$play_button_tag = '<i class="fa fa-play-circle"></i>';
		
		// ########### BRANDING CHECK ########### 
		$branding_arr = array('intro-branded', 'intro-cobranded');
		if (in_array($showcase_type, $branding_arr)) {
			$is_branded = true;
			
			// LARGE/PRIMARY LOGO 
			$large_logo_src = WP3d_Models()->get_model_large_logo($post_id); 
				if ($large_logo_src) {
					$primary_logo_set = true;
				} 
			
			// SMALL LOGO 
			$small_logo_src = WP3d_Models()->get_model_small_logo($post_id); 
				if ($small_logo_src) {
					$small_logo_set = true;
				} 
				
	        // OVERLAY LOGO CHECK
			if (get_field('logo_overlay') && $small_logo_set) { // are we overlaying a logo
				$overlay_logo_set = true;
			} else {
				$overlay_logo_set = false;
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
		
		// ########### CONTENT/FEATURE CHECKS ########### 
		if (get_field('add_property_info_details')) { $has_property_info = true; } else { $has_property_info = false; }		
		if (WP3D_Models()->get_wp3d_gallery_images()) { $has_gallery = true; } else { $has_gallery = false; }
		if (WP3D_Models()->get_related_models()) { $has_related_models = true; } else { $has_related_models = false; }
		
		// PRELOAD
		$wp3d_preload = get_field('preload_model');
			
		// MATTERPORT MODEL
		if ($wp3d_model_type == 'matterport') { 
			
				// get the ID!
				$mp_incoming = trim(get_field('model_link'));
				$mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
				$mp_start = WP3D_Models()->mp_start_from_url($mp_incoming); // potential deep-link start
				
				// Update Intro Text
				$default_intro_statement = __('START TOUR', 'wp3d-models');	
				
				// When a Model is saved, we look to Matterport for any saved data and copy it back
				$api_data = get_field('_matterport_api_data'); // hidden custom field stores any retrieved Matterport data in an array
				
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
					
				// BETA Language params
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
				     
				// tour highlight overlay logo adjust
				if ($mp_show_highlight_reel) {
					$overlay_highlight_class = " has-highlight-reel";
				} else {
					$overlay_highlight_class = '';
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
				if ($mp_autoplay && !$is_introed) { $wp3d_iframe_src_url .= 'play=1&amp;'; } 
				if ($mp_multifloor) { $wp3d_iframe_src_url .=  'f=0&amp;'; }
			    if ($mp_no_guided_tour_panning) { $wp3d_iframe_src_url .=  'kb=0&amp;'; }
		        if ($mp_looped_guided_tour) { $wp3d_iframe_src_url .=  'lp=1&amp;'; }
		        if ($mp_no_guided_tour_path) { $wp3d_iframe_src_url .=  'guides=0&amp;'; }
		        if ($mp_show_highlight_reel != '') { $wp3d_iframe_src_url .=  'hl='.$mp_show_highlight_reel.'&amp;'; }
		        if ($mp_autostart_guided_tour) { $wp3d_iframe_src_url .=  'ts='.$guided_tour_seconds.'&amp;'; }
		        if ($mp_force_help != '') { $wp3d_iframe_src_url .= 'help='.$mp_force_help.'&amp;'; }
		        if ($mp_disable_mouse_arrows) { $wp3d_iframe_src_url .=  'wh=0&amp;'; }
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
	        if ($tst_branding) { $wp3d_iframe_src_url .=  'brand='.$tst_branding.'&amp;'; } else { $wp3d_iframe_src_url .=  'brand=false&amp;'; }
	        
	        // Startscreen logic
	        if (!$is_introed) { // no reason to ever add a startscreen for "introed" content
	        	if ($tst_startscreen == "true") { 
	        		$wp3d_iframe_src_url .=  'startscreen='.$tst_startscreen.'&amp;'; 
	        	}
	        }	        
	        
	        // Parameter Cleanup
	        $wp3d_iframe_src_url = preg_replace('/&amp;$/', '', $wp3d_iframe_src_url);
	       
	        // Variable re-assigning
	        $wp3d_iframe_data_src = $wp3d_iframe_src_url; 
	        $wp3d_id = $tst_id;
				
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
		
		// VIDEO MODEL
		if ($wp3d_model_type == 'video') { 
			
			// Update Intro Text
			$default_intro_statement = __('START VIDEO TOUR', 'wp3d-models');	
		    
		    if (get_field('base_video_type') == 'youtube') {
		        
		        $base_youtube_url = get_field('base_youtube_video_link');	
		        $wp3d_iframe_src_url = WP3D_Models()->youtube_embed_from_url($base_youtube_url, true).'&amp;autoplay=1';;
		        
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
			
		
		// ADDRESS
		// This function does all of the heavy lifting re: getting the correct address information for the model map.
		// Depending on what has been selected, this address data may come from Matterport, or may be set locally, or may not exist at all.
		$address = WP3D_Models()->get_model_address_info($post_id);

		// AGENTS INFO
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
		
		// MODEL TYPE & INTROS 
		
		if ($is_static_image) { // JUST AN IMAGE ?>
		
			<div class="wp3d-embed-wrap<?php echo $status_class; ?>">
				<div id="wp3d-intro" style="background-image: url('<?php echo $intro_src; ?>');"></div>
			</div>
			
		<?php } else { // NOT A STATIC IMAGE 
		
			if ($is_introed) { // add intro image & enable autoplay, but don't pass the src to the iframe just yet ?>
			
			<div class="wp3d-embed-wrap<?php echo $status_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">
				
				<div id="wp3d-intro" class="is-loading" style="background-image: url('<?php echo $intro_src; ?>');">
					<a href="#">
						<div class="wp3d-start <?php if ($primary_logo_set) { echo "has-primary-logo "; } if ($small_logo_set) { echo "has-small-logo "; } ?>">
							
							<?php if ($is_branded && $primary_logo_set) { ?>
							<img src="<?php echo $large_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-large">
							<?php } ?>
							
							<?php if ($is_branded && $small_logo_set) { ?>
								<img src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>" class="overlay-logo-small">
							<?php } ?>						
							
							<div class="play-button">
	    						<?php echo wp_kses(apply_filters( 'wp3d_play_button', $play_button_tag ), $play_button_html); ?>
							</div>
							<div class="message">
							<?php  
							if (get_field('custom_showcase_statement') && $is_branded ) { // branded and has a custom statement
								the_field('custom_showcase_statement'); 
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
						
						<?php if ($is_cobranded) { ?>
						<div class="cobrand">
							<img src="<?php echo $plugins_url; 
							?>/wp3d-models-free/assets/images/powered-by-matterport-1color.png" alt="Powered by 
							Matterport">
						</div>
						<?php } ?>					
	
					</a>
				</div>
				
				<?php if ($overlay_logo_set) { ?>
					<img class="iframe-logo-overlay<?php echo $overlay_highlight_class ?>" src="<?php echo $small_logo_src; ?>" alt="<?php echo esc_attr($model_title); ?>">
				<?php } ?>			
					<?php echo WP3D_Models()->get_content_schema($post_id, $wp3d_id, $wp3d_iframe_src_url); ?>
					<iframe id="mp-iframe" src="" <?php if ($wp3d_preload) { echo 'data-preload="true" '; } ?>data-src="<?php echo $wp3d_iframe_data_src; ?>" data-allow="<?php echo $allow; ?>" frameborder="0" allow="vr<?php echo $allow; ?>" allowfullscreen></iframe>		
	
			</div><!-- end embed wrap -->
			
			<?php } else { // just print out the stock iframe, with options, of course ?>		
			
			<div class="wp3d-embed-wrap<?php echo $status_class; ?><?php echo $fov_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">
				<?php echo WP3D_Models()->get_content_schema($post_id, $wp3d_id, $wp3d_iframe_src_url); ?>
				<iframe src="<?php echo $wp3d_iframe_src_url; ?>" frameborder="0" allow="vr<?php echo $allow; ?>" allowfullscreen></iframe>		
			</div><!-- end embed wrap -->
	
			<?php } // end introed check 
		
		} // END MODEL TYPE CHECK 
        
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
		<?php } ?>

    <!--Content Section-->
    
    <section id="content">
             	
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
	                        if (0 === strpos($title, $search_text)) { $row_has_icon = true; } else { $row_has_icon = false; } ?>
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
                     
    </section><!-- end #content (wrap) -->

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
    $youtube_url = get_field('youtube_video_link');
    $vimeo_url = get_field('vimeo_video_link');    
    
    if( $youtube_url || $vimeo_url ) {
?>    
	<?php do_action( 'wp3d_single_video_before', get_the_ID() ); ?>
    <div id="wp3d-video">
	<?php if ($has_video == "youtube") {
		echo WP3D_Models()->youtube_embed_from_url( $youtube_url );
	} elseif ($has_video == "vimeo") {
		echo WP3D_Models()->vimeo_embed_from_url( $vimeo_url );
	} ?>
    </div>
    <?php do_action( 'wp3d_single_video_after', get_the_ID() ); ?>
<?php  } // end if youtube or vimeo isn't empty
}  // ################# END Video ################# // ?>             
 
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
  
<?php // ################# BEGIN Agents ################# //
if($has_agents && !empty($agents_arr)) {  // if there is agent data, lets sift through it and return it
 
	if (count($agents_arr) == 1) { $agent_class = " mp-contact-info"; } else { $agent_class = ''; } 
	//print_r($agents_arr);
	ob_start();
?>
    <?php do_action( 'wp3d_single_agents_before', get_the_ID() ); ?>
    <section id="agents-section">
        <div class="container">
            <ul class="agents-list clearfix">
                
                <?php foreach ($agents_arr as $agent) { ?>
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
                        <?php if(isset($agent['name'])) { ?><span class="fn" title="<?php echo esc_attr($agent['name']); ?>" itemprop="name"><?php echo esc_html($agent['name']); ?></span><?php } ?>
                        <?php if(isset($agent['subheading'])) { ?><span class="sub-heading"><?php echo esc_html($agent['subheading']); ?></span><?php } 
                        
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
                        
                        if( // checking for agent meta
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
                        ?>
                        <?php if ($agent_meta) { ?>
                        <ul class="agent-meta fa-ul">
                        <?php } ?>
                            <?php if(isset($agent['email'])) { ?>
                            <li class="email">
                                <i class="fa-li fa fa-envelope"></i><a href="mailto:<?php echo sanitize_email($agent['email']); ?>" itemprop="email"><?php echo esc_html($agent['email']); ?></a>
                            </li>
                            <?php } ?>
							<?php if( isset($agent['phone'])) { // this one comes from Matterport ONLY, including the "formatted phone" that is part of this conditional ?>
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
                        <a href="#" class="wp3d-agent-bio">More About <?php echo esc_html($agent['name']);  ?></a> 
                        <?php } ?>
                    </div>
                </li>
                <?php } ?>

            </ul>
        </div>
    </section>
    <?php do_action( 'wp3d_single_agents_after', get_the_ID() ); ?>
    
 <?php 
	$agents_html = ob_get_clean();
	echo $agents_html;
} // ################# END Agents ################# // ?>     
    
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
<?php } // ################# END Sharing ################# //     
    
if(get_option('wp3d_disclaimer_text') || (get_option('wp3d_hide_branding') != 'on' && get_option('wp3d_hide_branding') !== false)) { ?>    
<section id="disclaimer" class="clearfix">
	    
<?php // ################# BEGIN Disclaimer ################# //
	if (get_option('wp3d_disclaimer_text')) { ?>
	<?php do_action( 'wp3d_single_disclaimer_before', get_the_ID() ); ?>
	<div id="wp3d-disclaimer-text">
        <?php 
        echo strip_tags(get_option('wp3d_disclaimer_text'), '<a><img>');
        ?>
	</div>
	<?php do_action( 'wp3d_single_disclaimer_after', get_the_ID() ); ?>
<?php } // ################# END Disclaimer ################# // ?> 
	  
<?php // ################# BEGIN WP3D Branding ################# //
	if (get_option('wp3d_hide_branding') != 'on' && get_option('wp3d_hide_branding') !== false) { // checking for option to hide the WP3D Models branding" 
	
	   $wp3d_credit_link = "https://wp3dmodels.com";
       $wp3d_credit_link = apply_filters( 'wp3d_custom_credit_link', $wp3d_credit_link );
	
	?>
	<?php do_action( 'wp3d_single_wp3dbrand_before', get_the_ID() ); ?>
	<div id="wp3d-credit">
	    <a href="<?php echo esc_url($wp3d_credit_link); ?>" target="_blank">
	        <?php _e('Powered by WP3D Models', 'wp3d-models'); ?>
	    </a>
	</div>
	<?php do_action( 'wp3d_single_wp3dbrand_after', get_the_ID() ); ?>
<?php } // ################# END WP3D Branding ################# // ?> 		

</section>
<?php } // end section check ?> 

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

		</main><!-- .site-main -->
		
<?php // ################# BEGIN DEBUGGING ################# //
if(isset($_GET['debug'])) { ?>
	<!--
	MPID = <?php echo $wp3d_id; ?>
	
	LARGE LOGO = <?php echo WP3D_Models()->get_model_large_logo($post_id); ?>
	
	SMALL LOGO = <?php echo WP3D_Models()->get_model_small_logo($post_id); ?>
	
	VERSION = <?php echo WP3D_MODELS_VERSION; ?>	
	
	MP STORED LOCALLY = <?php print_r(get_field('_matterport_api_data')); ?>
	
	MATTERTAGS = <?php print_r(get_field('_matterport_mattertag_data')); ?>
	
	TST STORED LOCALLY = <?php print_r(get_field('_tst_api_data')); ?>	
	
	<?php $all_options = wp_load_alloptions();
	  $my_options = array();
	  foreach( $all_options as $name => $value ) {
	    if(stristr($name, '_transient_mp_api')) { $my_options[$name] = $value; }
	    if(stristr($name, 'wp3d_')) { 
	    	
	    	// no license info
	    	if (strpos($name, 'license') === false) {
			    $my_options[$name] = $value;
			}
	    	
	    }
	  }
	  print_r($my_options);	
	?>
	-->
<?php } // ################# END DEBUGGING ################# // ?> 
		
	</div><!-- end #wp3d-single-model -->
<?php do_action( 'wp3d_single_model_wrap_after', get_the_ID() ); ?>
<?php get_footer(); ?>
