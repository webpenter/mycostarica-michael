<?php
function enqueue_single_scripts () {
     global $post;
     if ( !function_exists('get_field') ) { return; } // no ACF, bounce.
     if ( empty($post) ) { return; } // a page with no $post? bounce.
     $wp3d_post_content = $post->post_content;
     if ( !is_singular('model') && !has_shortcode($wp3d_post_content, 'wp3d-models') && !has_shortcode($wp3d_post_content, '3d-models')  ) { return; } // only continue on single models, or pages where a WP3D shortcode is in use
     $mp_incoming = trim(get_field('model_link'));
	$mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);

     //if address data is present on a model (either from Matterport or local data), show the single map - TRUE/FALSE check - ALL VIEWS
     if ( WP3D_Models()->get_model_address_info($post->ID) ) {
          
          $is_fullscreen = get_query_var('fullscreen', null);
          $is_fullscreen_nobrand = get_query_var('fullscreen-nobrand', null);
          if (
               !isset($_GET['fullscreen']) && !isset($is_fullscreen) && 
               !isset($_GET['fullscreen-nobrand']) && !isset($is_fullscreen_nobrand) ) { // no maps on fullscreen versions

               // enqueue the single maps scripts
               if (!get_option('wp3d_disable_google_maps_js')) { // if Google Maps JS is enabled (not disabled)
                    wp_enqueue_script( WP3D_Models()->_token . '-google-maps' ); 
               }
               wp_enqueue_script( WP3D_Models()->_token . '-google-maps-models-single' ); 
               
               /* ############################### ENQUEING NOTE ############################### */
               /* ########       All JS Enqueing happens on each 'view' template       ######## */
               /* ########       Doing it this way to ease "Stand Alone" funct.        ######## */
               /* ############################### ENQUEING NOTE ############################### */
          }

     }
     
     // Since the SKINNED view is meant to be largely 'stand alone' these scripts/styles will not be enqueued automatically on this VIEW
     $is_skinned = get_query_var('skinned', null);
     if (!isset($_GET['skinned']) && !isset($is_skinned)) { // if 'skinned' is NOT set via the URL
     
		// only frontend gets enqueued by default
		wp_enqueue_script( WP3D_Models()->_token . '-frontend' );     
          
          // BRING IN FONT AWESOME GLOBALLY ON 'NON' SKINNED PAGES
          if (!get_option('wp3d_disable_fontawesome_css')) { // if FontAwesome is enabled (not disabled)
               wp_enqueue_style( WP3D_Models()->_token . '-font-awesome', esc_url('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'), array(), WP3D_Models()->_version );
          }
     
     }
     
} // End enqueue_scripts () 

add_action( 'wp_enqueue_scripts', 'enqueue_single_scripts', 10 );


function get_3d_model_template($single_template) {
     global $post;
     
     // looking for query vars
     $is_skinned = get_query_var('skinned', null);
     $is_nobrand = get_query_var('nobrand', null);
     $is_custom = get_query_var('custom', null);
     $is_fullscreen = get_query_var('fullscreen', null);
     $is_fullscreen_nobrand = get_query_var('fullscreen-nobrand', null);
     
     // It is understood that the use of Globals is not always best, but in this case we need this value to pass to a Google Analytics Event.  
     // Since we don't want to run through all of this conditional again, and because we're namespaced, and because everything happens in this
     // function, it seems like the best route to go.
     global $wp3d_ga_label; $wp3d_ga_label = '';

     if ( is_singular( 'model' ) ) {
          
          if (isset($_GET['skinned']) || isset($is_skinned)) { // if 'skinned' is set in the URL
          
               // look for a custom skin
               if (locate_template( 'wp3d-skin/index.php' ) ) {
                                   
                    $single_template = locate_template( 'wp3d-skin/index.php' );
                    
               // otherwise use the one that ships with the plugin
               } else { 
               
                    $single_template = dirname(dirname( __FILE__ )) . '/skins/crosby/index.php';

               }    
               
               remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // FU Emoji Detection
               remove_action( 'wp_print_styles', 'print_emoji_styles' ); // FU Emoji
               add_filter( 'style_loader_src', function() { return ''; } ); // oh yeah...no theme/plugin CSS! We're trim baby.
               
               $wp3d_ga_label = "Skinned";               
          
          } elseif (isset($_GET['nobrand']) || isset($is_nobrand) ) { // if 'nobrand' is set in the URL
          
               // if the local parent/child theme has an overriding template, use it
               if (locate_template('single-model-nobrand.php') != '') { 
               
                    $single_template = locate_template('single-model-nobrand.php');
                    
               // otherwise use the one that ships with the plugin
               } else { 
               
                    $single_template = dirname(dirname( __FILE__ )) . '/templates/single-model-nobrand.php';
               }          
               
               $wp3d_ga_label = "Nobrand";               
               
          } elseif (isset($_GET['custom']) || isset($is_custom) ) { // if 'fullscreen' is set in the URL
          
               // if the local parent/child theme has an overriding template, use it
               if (locate_template('single-model-custom.php') != '') { 
               
                    $single_template = locate_template('single-model-custom.php');
                    
               // otherwise use the stock single model template
               } else { 
               
                    $single_template = dirname(dirname( __FILE__ )) . '/templates/single-model.php'; 
               } 
               
               $wp3d_ga_label = "Custom";                  
                         
          } elseif (isset($_GET['fullscreen']) || isset($is_fullscreen) ) { // if 'fullscreen' is set in the URL
          
               // if the local parent/child theme has an overriding template, use it
               if (locate_template('single-model-fullscreen.php') != '') { 
               
                    $single_template = locate_template('single-model-fullscreen.php');
                    
               // otherwise use the one that ships with the plugin
               } else { 
               
                    $single_template = dirname(dirname( __FILE__ )) . '/templates/single-model-fullscreen.php'; 
               }
               
               // check to see if we're embedded or not
               if (isset($_GET['embedded'])) {
                    $wp3d_ga_label = "Fullscreen+Embedded"; 
               } else {
                    $wp3d_ga_label = "Fullscreen"; 
               }
                   
          } elseif (isset($_GET['fullscreen-nobrand']) || isset($is_fullscreen_nobrand) ) { // if 'fullscreen-nobrand' is set in the URL
          
               // if the local parent/child theme has an overriding template, use it
               if (locate_template('single-model-fullscreen-nobrand.php') != '') { 
               
                    $single_template = locate_template('single-model-fullscreen-nobrand.php');
                    
               // otherwise use the one that ships with the plugin
               } else { 
               
                    $single_template = dirname(dirname( __FILE__ )) . '/templates/single-model-fullscreen-nobrand.php'; 
               } 

               // check to see if we're embedded or not
               if (isset($_GET['embedded'])) {
                    
                    $wp3d_ga_label = "Fullscreen+Nobrand+Embedded"; 
                    
               } else {
                    
                    $wp3d_ga_label = "Fullscreen+Nobrand";
                    
               }               

          } else {

               // if the local parent/child theme has an overriding template, use it
               if (locate_template('single-model.php') != '') { 
               
                    $single_template = locate_template('single-model.php');

               // otherwise use the one that ships with the plugin
               } else { 
               
                    $single_template = dirname(dirname( __FILE__ )) . '/templates/single-model.php';
                                         
               }
               
               $wp3d_ga_label = "Standard";               
          
          }
          
          // Push the Google Analytics Filters for "Google Analytics for WordPress" plugin - https://wordpress.org/support/plugin/google-analytics-for-wordpress
          add_filter( 'yst_ga_filter_push_vars', function() { global $wp3d_ga_label; return "'send', 'event', 'WP3D', 'View', '".$wp3d_ga_label."', { nonInteraction: true }"; }, 10, 1 );     
          
          // Or...roll your own analytics events
          do_action( 'wp3d_analytics_events');    
     }
     return $single_template;
}

add_filter( 'single_template', 'get_3d_model_template' );

?>