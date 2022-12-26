<?php 
// ***************************** //
// ADD THE SHORTCODES
// ***************************** //

// shortcode to list models
add_shortcode( '3d-models', 'threed_models_shortcode' ); // legacy
add_shortcode( 'wp3d-models', 'threed_models_shortcode' ); 

// shortcode to show a single model on a page/post
add_shortcode( 'wp3d-model', 'threed_model_single_shortcode' ); 

// shortcode to show agent(s) on a page/post COMING SOON
// add_shortcode( 'wp3d-agents', 'threed_agents_shortcode' ); COMING SOON

// ***************************** //
// AGENT DISPLAY SHORTCODE ( with assoc. models? )
// ***************************** //

function threed_agents_shortcode( $atts ) {
    
    ob_start();
    
    // define attributes and their defaults
    extract( shortcode_atts( array (
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'posts' => -1,
        'exclude' => ''
        //'include' => '',        
        //'type-include' => '',
        //'type-exclude' => ''
    ), $atts ) );
    
    // NEED TO MASSAGE ATTS HERE
    // "include" needs to become an array
    // "exclude" needs to become an array
    // we need a check for either "include" or "exclude" and (if found) disable the "posts" value of "-1" (show all)
    
    $wp3d_agents_query = get_transient( 'wp3d_agents_query_' . $order . '_' . $orderby . '_' . $posts . '_' . $type . '_' . $client . '_' . $filter . '_' . $map );
    
    if( $wp3d_agents_query === false ) {
    
        $wp3d_agents_query = new WP_Query( 
            
            array(
                'post_type' => 'wp3d_agent',
                'posts_per_page' => $posts,
                'order' => $order,
                'orderby' => $orderby
            )
        
        );
        
        set_transient( 'wp3d_agents_query_' . $order . '_' . $orderby . '_' . $posts . '_' . $type . '_' . $client . '_' . $filter . '_' . $map, $wp3d_models_query, 60*3 );
    }
    
    // run the loop based on the query    
    if ( $wp3d_agents_query->have_posts() ) { 
    
        // show alert if 'get_field' does not exist (ACF)
        if ( !function_exists('get_field') ) {  
        
    ?>
    <div class="wp3d-alert wp3d-alert-error">
        <?php $wp3d_plugin_url = esc_url('/wp-admin/plugins.php'); printf( __( '<strong>BUGGER.</strong>  WP3D Models needs <a href="%s" target="_blank">additional plugins installed</a> in order to work correctly!', 'wp3d-models' ), $wp3d_plugin_url ); ?>
    </div>
    <?php } // end get_field check ?>
    
    <?php do_action( 'wp3d_agents_content_wrap_before', get_the_ID() ); ?>
    
    <?php $wp3d_agents_wrap_classes = ''; // no extra classes by default, just here for the filter ?>
    
    <div class="wp3d-agents wp3d-models-clearfix <?php echo apply_filters( 'wp3d_agents_wrap_classes', $wp3d_agents_wrap_classes );?>" data-viewterm="<?php echo esc_attr(apply_filters( 'wp3d_view_label', $view_label )); ?>">
            
        <?php
        if ( function_exists('get_field') ) { // checking for ACF function 'get_field'
        
            // the loop
            $i=0; while ( $wp3d_agents_query->have_posts() ) : $wp3d_agents_query->the_post(); 
            
            global $post;
            $post_id = get_the_ID();
            
            // getting the AGENT DATA HERE
          
            
            // no extra classes by default, just here for the filter
            $wp3d_agents_item_wrap_classes = '';
            ?> 
                
            <?php do_action( 'wp3d_agents_item_before', get_the_ID() ); ?> 
                <div id="post-<?php the_ID(); ?>" class="agents-item-wrap <?php echo apply_filters( 'wp3d_agents_item_wrap_classes', $wp3d_agents_item_wrap_classes );?>">

                    <?php // PRINT AGENT ITEM CONTENT HERE ?>

                </div>
            <?php do_action( 'wp3d_agents_item_after', get_the_ID() ); ?> 
        
        <?php endwhile;
        wp_reset_postdata(); 
    
        }  // checking for ACF function 'get_field' ?>
        
    </div> <!-- end #wp3d-agents -->
    
    <?php do_action( 'wp3d_agents_content_wrap_after', get_the_ID() ); ?>
        
    <?php 

    $myagents = ob_get_clean();
    return $myagents;
    
    } else { // NO AGENT RESULTS ?>
    
    <h1><?php _e('No Agents To Display', 'wp3d-models'); ?></h1>
    
    <p><?php _e('The current settings didn\'t return any results.  If you are the admin, please make some adjustments.', 'wp3d-models'); ?></p>
    <h3><?php _e('Current Settings:', 'wp3d-models'); ?></h3> 
    <ul>
        <li><?php //SHOW AGENT SETTINGS HERE ?></li>
    </ul>
    
<?php } 
    
}

// ***************************** //
// SINGLE MODEL DISPLAY SHORTCODE
// ***************************** //

function threed_model_single_shortcode( $atts ) {
    
    ob_start();
    
    // define attributes and their defaults
    extract( shortcode_atts( array (
        'id' => ''
    ), $atts ) );
    
    // show alert if 'id' data does not exist (ACF)
    if ( $id == '' ) {  ?>
    <div class="wp3d-alert wp3d-alert-error">
        <?php $wp3d_plugin_url = esc_url('/wp-admin/plugins.php'); printf( __( '<strong>BUGGER.</strong>  You need to make sure to enter a valid WP3D Model ID to use this shortcode.', 'wp3d-models' ), $wp3d_plugin_url ); ?>
    </div>
    <?php } else {
        
        // Default Schema Type
        $content_schema_type = __('MediaObject', 'wp3d-models');         
        
        // MODEL BASE CHECK
        // static_image | threesixtytours | video | generic | matterport
        $wp3d_model_type = get_field('wp3d_model_type', $id); 
        // older (Matterport) Models won't not have this value set
        if ($wp3d_model_type == '') { $wp3d_model_type = 'matterport'; } 
        
        // Reset vars
        $wp3d_iframe_src_url = '';
        $wp3d_id = '';  

        // MATTERPORT MODEL
        if ($wp3d_model_type == 'matterport') {  
                
    		// parameter checks
            $mp_autoplay = get_field('model_autoplay', $id);
            $mp_multifloor = get_field('model_hide_multifloor', $id);
            $mp_force_help = get_field('model_force_help', $id);
            $mp_no_showcase_branding = get_field('showcase_no_branding', $id);
            $mp_no_guided_tour_panning = get_field('disable_model_tour_panning', $id);
            $mp_looped_guided_tour = get_field('enable_model_tour_loop', $id);
            $mp_no_guided_tour_path = get_field('disable_model_tour_path', $id);
            $mp_show_highlight_reel = get_field('force_show_highlight_reel', $id);
            $mp_autostart_guided_tour = get_field('autostart_guided_model_tour', $id);
            $mp_disable_mouse_arrows = get_field('disable_space_scroll', $id);  
            $mp_disable_vr = get_field('disable_model_vr', $id);
            $mp_no_showcase_branding_links = get_field('showcase_no_branding_links', $id);
                // Hack to fix 'null' bug
                if ($mp_no_showcase_branding_links == 'null') { $mp_no_showcase_branding_links = '0'; }	            
            $mp_guided_tour_transition = get_field('model_guided_tour_transition', $id);
            $mp_model_zoom = get_field('model_zoom', $id);
            $mp_model_pin = get_field('model_pin', $id);
            $mp_model_portal = get_field('model_portal', $id);          
            $mp_title_panel = get_field('showcase_title_panel', $id);
            $mp_showcase_tour_cta = get_field('showcase_tour_cta', $id);
            $mp_vr_limited_mode = get_field('vr_limited_mode');
            $mp_dollhouse = get_field('dollhouse_view');
            $mp_mattertags = get_field('mattertag_content');            
    		
            // Quickstart (NO SHARING AUTOSTART ON SHORTCODE)
            $mp_global_quickstart = get_option('wp3d_enable_global_quickstart');
            $mp_enable_quickstart = get_field('enable_model_quickstart', $id);
            if ($mp_global_quickstart && $is_introed) { 
            	$global_quickstart = true; 
            } else { 
            	$global_quickstart = false; 
            }
            
            // BETA Language params
            $mp_showcase_language = '';
            $mp_default_language = get_option('wp3d_mp_default_lang'); // look for global
            if ($mp_default_language !== 'en') { $mp_showcase_language = $mp_default_language; } // check against English default
            if (get_field('mp_model_lang', $id)) { $mp_showcase_language = get_field('mp_model_lang', $id); } // override with Model-specific value if necessary
            
            // Tour Delay
            $guided_tour_seconds = '';
            $guided_tour_seconds_option = (intval(get_option('wp3d_mp_guided_tour_seconds')));
            $guided_tour_seconds_model = get_field('model_guided_tour_seconds', $id); 
        
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
            $showcase_highlight_time_model = get_field('showcase_highlight_time', $id);
        
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
		
    		$mp_incoming = get_field('model_link', $id);
    		$mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
    		$mp_start = WP3D_Models()->mp_start_from_url($mp_incoming); // potential deep-link start
    		
    	    // Build the URL and assemble the PARAMS
    	    //$wp3d_iframe_src_url = 'https://my.matterport.com/show/?m='.$mp_id;
    	    $wp3d_iframe_src_url = WP3D_Models()->mp_get_iframe_url($mp_incoming, $mp_id); 

    	    // Additional Param Check
    	    if ($mp_params){ $wp3d_iframe_src_url .= '&amp;'; }
    	    
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
            if ($mp_disable_mouse_arrows) { $wp3d_iframe_src_url .=  'wh=0&amp;'; } 
    		if ($mp_disable_vr) { $wp3d_iframe_src_url .=  'vr=0&amp;'; }
                    // Hack to fix MP Bug (No need for VR on an iPad)
					if (strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) { $mp_disable_vr = true; }    		
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
    
    	    // Parameter Cleanup
    	    $wp3d_iframe_src_url = preg_replace('/&amp;$/', '', $wp3d_iframe_src_url);
    	    
    	   // Variable re-assigning
            $wp3d_iframe_src_url = $wp3d_iframe_src_url;
            $wp3d_id = $mp_id;
	    
        } // end MATTERPORT
        
        // THREESIXTY TOURS
        if ($wp3d_model_type == 'threesixtytours') {  
            
	        $tst_incoming = trim(get_field('tst_link', $id));
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
	        $tst_header = get_field('tst_header', $id);
	        $tst_footer = get_field('tst_footer', $id);
	        $tst_title = get_field('tst_title', $id);
	        $tst_tournav = get_field('tst_tournav', $id);
			$tst_mousewheel = get_field('tst_mousewheel', $id);
	        $tst_socialshare = get_field('tst_socialshare', $id);
	        $tst_branding = get_field('tst_branding', $id);   
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
        
        // VIDEO MODEL
        if ($wp3d_model_type == 'video') { 
            
            if (get_field('base_video_type', $id) == 'youtube') {
                
                $base_youtube_url = get_field('base_youtube_video_link', $id);
                $wp3d_iframe_src_url = WP3D_Models()->youtube_embed_from_url($base_youtube_url, true);
                
                // Variable re-assigning
                $wp3d_iframe_data_src = $wp3d_iframe_src_url;         
                
            } 
            
            if (get_field('base_video_type', $id) == 'vimeo') {
                
                $base_vimeo_url = get_field('base_vimeo_video_link', $id);
                $wp3d_iframe_src_url = WP3D_Models()->vimeo_embed_from_url($base_vimeo_url, true);
                
                // Variable re-assigning
                $wp3d_iframe_data_src = $wp3d_iframe_src_url;          
                
            }
            
        }        
        
		// GENERIC MODEL
		if ($wp3d_model_type == 'generic') { 
		    $generic_iframe = trim(get_field('generic_iframe', $id));
		    $wp3d_iframe_src_url = WP3D_Models()->get_iframe_src($generic_iframe);
		    
		    // Variable re-assigning
		    $wp3d_iframe_data_src = $wp3d_iframe_src_url;     
		    
		}  


		// MODEL STATUS 
		$model_status = get_field('model_status'. $id);
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
		
		    if (get_field('mark_sold', $id)) { 
		        $status_class = ' wp3d-sold';
		    } elseif (get_field('mark_pending', $id)) {
		        $status_class = ' wp3d-pending';    
		    } else {  
		        $status_class = ''; 
		    }
		
		}		
		
    ?>
		<div class="wp3d-embed-wrap<?php echo $status_class; ?>" itemscope itemtype="http://schema.org/<?php echo esc_attr(apply_filters( 'wp3d_content_schema_type', $content_schema_type )); ?>">	
			<?php echo WP3D_Models()->get_content_schema($id, $wp3d_id, $wp3d_iframe_src_url); ?>
			<iframe src="<?php echo $wp3d_iframe_src_url; ?>" frameborder="0" allow="vr" allowfullscreen></iframe>		
		</div>		
    <?php }
    
        $mysinglemodel = ob_get_clean();
    return $mysinglemodel;
    
    
}

// ***************************** //
// LIST PAGE DISPLAY SHORTCODE
// ***************************** //


function threed_models_shortcode( $atts ) {
    
    static $wp3d_shortcode_ran = FALSE; // setting a variable to see if the shortcode has happened yet.

    if ( $wp3d_shortcode_ran ) // If she's run, display an alert box.
    {
        ob_start(); ?>
        
        <div class="wp3d-alert wp3d-alert-error">
            <?php _e('<strong>Oops!</strong> Only one WP3D Shortcode per page!  The first one is above and this (additional) one was skipped!', 'wp3d-models'); ?>
        </div>
        
        <?php $shortcode_error = ob_get_clean();
        return $shortcode_error;
    }
    
    ob_start();
    
    // looking for models that have been excluded
    $exclude_args = array(
    	'posts_per_page'   => -1,
    	'post_type'		=> 'model',
    	'meta_key'		=> 'model_list_exclude',
    	'meta_value'	=> '1',
    	'post_status'      => 'publish',
    	'fields' => 'ids'
    );
    $excluded_models_arr = get_posts( $exclude_args ); 

    // DEBUGGIN
    //print_r($excluded_models_arr); exit;
    
    // define attributes and their defaults
    extract( shortcode_atts( array (
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'posts' => -1,
        'offset' => '',
        'type' => '',
        'client' => '',
        'filter' => 'false',
        'map' => 'false',  // true, false, or only
        'collection' => 'false', 
        'collvrcheck' => 'false', // explicitly check for VR
        'collname' => '',
        'colldesc' => '',
        'viewlink' => ''
        //'newtab' => 'false'
        
    ), $atts ) );
    
    $wp3d_models_query = get_transient( 'wp3d_models_query_' . $order . '_' . $orderby . '_' . $posts . '_' . $type . '_' . $client . '_' . $filter . '_' . $map );
    
    if( $wp3d_models_query === false ) {
    
        $wp3d_models_query = new WP_Query( 
            
            array(
                'post_type' => 'model',
                'posts_per_page' => $posts,
                'offset' => $offset,
                'order' => $order,
                'orderby' => $orderby,
                'model-type' => $type,
                'model-client' => $client,
                'post__not_in' => $excluded_models_arr
            )
        
        );
        
        set_transient( 'wp3d_models_query_' . $order . '_' . $orderby . '_' . $posts . '_' . $type . '_' . $client . '_' . $filter . '_' . $map, $wp3d_models_query, 60*3 );
    }
    
    // run the loop based on the query    
    if ( $wp3d_models_query->have_posts() ) { 
    
    // set some labels
    $view_label = __('VIEW', 'wp3d-models'); 
    $map_label = __('MAP', 'wp3d-models'); 
    $all_label = __('ALL', 'wp3d-models'); 
    
    // get column numbers
    $wp3d_desk_cols = get_option('wp3d_desktop_columns'); if ($wp3d_desk_cols == '') { $wp3d_desk_cols = "wp3d-d-3col"; } else { $wp3d_desk_cols = 'wp3d-d-'.$wp3d_desk_cols.'col'; }
    $wp3d_tab_cols = get_option('wp3d_tablet_columns'); if ($wp3d_tab_cols == '') { $wp3d_tab_cols = "wp3d-t-2col"; } else { $wp3d_tab_cols = 'wp3d-t-'.$wp3d_tab_cols.'col'; }
    $wp3d_phone_cols = get_option('wp3d_phone_columns'); if ($wp3d_phone_cols == '') { $wp3d_phone_cols = "wp3d-p-1col"; } else { $wp3d_phone_cols = 'wp3d-p-'.$wp3d_phone_cols.'col'; }
    
    // checking for map "only"
    if ($map == 'only') { 
    $map_only_status = "map-only-on ";
    } else {
        $map_only_status = "map-only-off ";
    } 

    // show alert if 'get_field' does not exist (ACF)
    if ( !function_exists('get_field') ) {  ?>
    <div class="wp3d-alert wp3d-alert-error">
        <?php $wp3d_plugin_url = esc_url('/wp-admin/plugins.php'); printf( __( '<strong>BUGGER.</strong>  WP3D Models needs <a href="%s" target="_blank">additional plugins installed</a> in order to work correctly!', 'wp3d-models' ), $wp3d_plugin_url ); ?>
    </div>
    <?php } ?>
    
    <?php do_action( 'wp3d_list_map_before', get_the_ID() ); ?>         

        <?php // checking to see if we have a map or not
        $marker_type = ''; // default empty marker type
        if ($map != 'false')  { 
            
            // get the marker type info
    	    if(get_option('wp3d_list_page_map_type')) { $map_type = get_option('wp3d_list_page_map_type'); } else { $map_type = 'ROADMAP'; } // marker type
            
            // get the marker type info
    	    if(get_option('wp3d_marker_type')) { $marker_type = get_option('wp3d_marker_type'); } else { $marker_type = 'stock'; } // marker type
            
            $wp3d_list_map = '<div id="wp3d-map" class="'.$map_only_status.'" data-map-type="'.$map_type.'"></div>';
            
            // DEBUGGIN'
            // print_r( WP3D_Models() ); 
            
            // filtering the map output here, in case this needs to be customized
            echo apply_filters( 'wp3d_list_map', $wp3d_list_map );
        
            if (!get_option('wp3d_disable_google_maps_js')) { // if Google Maps JS is enabled (not disabled)
                wp_enqueue_script( WP3D_Models()->_token . '-google-maps' );
            }
            
            wp_enqueue_script( WP3D_Models()->_token . '-google-maps-models' ); 
        
        } ?>
     
    <?php do_action( 'wp3d_list_map_after', get_the_ID() ); ?> 
    <?php do_action( 'wp3d_list_filter_wrap_before', get_the_ID() ); ?>
    
    <?php // Show the filter list ONLY if we're displaying ALL models and never for clients
    if ($type =='' && $client =='' && $filter != "false") {
    
        $filtering_status = "filtering-on ";
        $model_type_terms = get_terms( 'model-type' );
        
         if ( ! empty( $model_type_terms ) && ! is_wp_error( $model_type_terms ) ){
             $wp3d_list_filter_nav = '<div id="filter-3d-models" class="wp3d-models-clearfix">';
             $wp3d_list_filter_nav .= '<ul>';
             $wp3d_list_filter_nav .= '<li><a href="#" class="active" data-filter="all-models">'.apply_filters( 'wp3d_all_label', $all_label ).'</a>'; // starts with 'all'
             foreach ( $model_type_terms as $model_type_term ) {
               $wp3d_list_filter_nav .= '<li><a href="#" class="" data-filter="'. $model_type_term->slug .'">' . $model_type_term->name . '</a></li>';
             }
             $wp3d_list_filter_nav .= '</ul>';
             $wp3d_list_filter_nav .= '</div>';

             // filtering the nav output here, in case this needs to be customized
             echo apply_filters( 'wp3d_list_filter_nav', $wp3d_list_filter_nav );
        }  
    } else {
        $filtering_status = 'filtering-off ';
    } ?>
    
    <?php do_action( 'wp3d_list_filter_wrap_after', get_the_ID() ); ?>
    <?php do_action( 'wp3d_list_content_wrap_before', get_the_ID() ); ?>
    
    <?php $wp3d_list_wrap_classes = ''; // no extra classes by default, just here for the filter 
    
    if ( function_exists('get_field') ) { // checking for ACF function 'get_field'
    
            // If the VR Collection is enabled, loop through the results and make the link, separate from actually displaying the Models
            if ($collection == 'true') { 
                
                // get correct collection title (Check Shortcode value first - then revert to page title)
                if ($collname == '') { $wp3d_coll_name = urlencode(esc_attr(get_the_title())); } else { $wp3d_coll_name = urlencode($collname); }
                
                // get correct collection description (Check Shortcode value first - then revert to page excerpt)
                if ($colldesc == '') { $wp3d_coll_desc = urlencode(esc_attr(get_the_excerpt())); } else { $wp3d_coll_desc = urlencode($colldesc); }
                
                // set the return URL
                $wp3d_return_url = '&ret='.urlencode(untrailingslashit(get_the_permalink()));

                $wp3d_sids_arr = array();
                
                while ( $wp3d_models_query->have_posts() ) : $wp3d_models_query->the_post(); 
                   
                  $mp_api_data = '';
                  $mp_incoming = trim(get_field('model_link'));
                  $mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
                  //echo $mp_id.'<br>';
                  
                  if ($mp_id && $mp_id != 'error') {
                      
                      // if collvrcheck is set, only collect IDs of Models with VR explicitly enabled
                      if ($collvrcheck == 'true') {
                            
                          // we're only getting models that explicitely have VR data enabled (requires re-save for older models)  
                          $mp_api_data = get_field('_matterport_api_data');
                          if ($mp_api_data['is_vr']) {
                                //print_r($mp_api_data);
                                $wp3d_sids_arr[] = $mp_id; 
                          }
                          
                      } else {
                          // no need to check for IDs
                          $wp3d_sids_arr[] = $mp_id; 
                      }
                  }
                   
                endwhile;
                
                // clean up & check the array
                $wp3d_sids_arr = array_filter($wp3d_sids_arr);

                if (!empty($wp3d_sids_arr)) {
                
                    // DEBUGGIN
                    //echo $wp3d_sids_arr; // exit;                    

                    // Reference URL
                    // http://my.matterport.com/vr/dlist/?sids=KGNW8SXcYRG,nsBwE4W4WW8,1vpLu8nVR3r&ln=Collection+Title&ld=Collection+description+goes+here%2E&ret=https%3A%2F%2Fwww.matterport.com
                    
                    // build the SIDS string
                    $wp3d_vr_sids = '?sids='. implode (",", $wp3d_sids_arr);
                    
                    $wp3d_vr_collection_url = 'http://my.matterport.com/vr/dlist/'.$wp3d_vr_sids.'&ln='.$wp3d_coll_name.'&ld='.$wp3d_coll_desc.$wp3d_return_url;
                    
                    echo '<div id="wp3d-vr-collection"><a href="'.$wp3d_vr_collection_url.'" target="_blank" class="wp3d-btn"><i class="fa fa-simplybuilt fa-rotate-180"></i>'.urldecode($wp3d_coll_name).'</a></div>';
                    
                } else { // the array is bunk, bounce
                    return;
                }
            }
        }
    ?>
    
    <div id="wp3d-models" class="wp3d-models-clearfix <?php echo $filtering_status; ?><?php echo $map_only_status; ?><?php echo $wp3d_desk_cols; ?> <?php echo $wp3d_tab_cols; ?> <?php echo $wp3d_phone_cols; ?> <?php echo apply_filters( 'wp3d_list_wrap_classes', $wp3d_list_wrap_classes );?>" data-viewterm="<?php echo esc_attr(apply_filters( 'wp3d_view_label', $view_label )); ?>">
        
		<?php 
           
        if ( function_exists('get_field') ) { // checking for ACF function 'get_field'
        
            // the loop
            $i=0; while ( $wp3d_models_query->have_posts() ) : $wp3d_models_query->the_post(); 
            
            global $post;
            $post_id = get_the_ID();
            
            // clearing some vars
            $wp3d_assigned_terms = '';
            $wp3d_terms_classes = '';
            $wp3d_terms_assembled = '';
            
            // MODEL STATUS 
    		$model_status = get_field('model_status');
    		if ($model_status) {
    		    
    		    if ($model_status == 'sold') { 
    		        $status_class = ' wp3d-sold';
    		        $flap_status = true;
    		    } elseif ($model_status == 'pending') { 
    		        $status_class = ' wp3d-pending'; 
    		        $flap_status = true;
    		    } elseif ($model_status == 'custom') {
    		        $status_class = ' wp3d-custom-status';
    		        $flap_status = true;
    		    } else {
    		        $status_class = '';
    		        $flap_status = false;
    		    }
    		    
    		// SOLD (LEGACY)
    		} else {
    		
    		    if (get_field('mark_sold')) { 
    		        $status_class = ' wp3d-sold';
    		        $flap_status = true;
    		    } elseif (get_field('mark_pending')) {
    		        $status_class = ' wp3d-pending';
    		        $flap_status = true;
    		    } else {  
    		        $status_class = '';
    		        $flap_status = false;
    		    }
    		
    		}            
                        
            // MODEL BASE CHECK
            // static_image | threesixtytours | video | generic | matterport
		    $wp3d_model_type = get_field('wp3d_model_type');  
		    // older (Matterport) Models won't not have this value set
            if ($wp3d_model_type == '') { $wp3d_model_type = 'matterport'; } 
		    
		     // STATIC IMAGE
            if ($wp3d_model_type == 'static_image') {
                $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
                if (!$model_src) {
                    $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Static%20Image';
                }
            }
		    
		    // MATTERPORT 
		    if ($wp3d_model_type == 'matterport') {
                $mp_incoming = trim(get_field('model_link'));
                $mp_id = WP3D_Models()->mp_id_from_url($mp_incoming);
                
                if ($mp_id == 'error') {
                    $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Image';
                } else { // getting images from MP 
                    $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
                } 
                
                $wp3d_id = $mp_id;
		    }
		    
		    // THREESIXTY 
		    if ($wp3d_model_type == 'threesixtytours') {
                $tst_incoming = trim(get_field('tst_link'));
                $tst_id = WP3D_Models()->tst_id_from_url($tst_incoming); // array
                
                if ($tst_id == 'error') {
                    $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Image';
                } else { // getting images from MP 
                    $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
                } 
                
                $wp3d_id = $tst_id;
		    }
		    
		    // VIDEO
		    if ($wp3d_model_type == 'video') {
                $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
                if (!$model_src) {
                    $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Video%20Image';
                }
		    }		    
		    
		    // GENERIC
		    if ($wp3d_model_type == 'generic') {
                $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
                if (!$model_src) {
                    $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Generic%20Image';
                }
		    }		    
            
            // checking to see if we've got a map
            $address = WP3D_Models()->get_model_address_info($post_id);
            
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
            
            // View Link
            
            if ($viewlink == 'modal') { // force the fullscreen
                $wp3d_view_link = WP3D_Models()->get_model_link($post_id, true, true);
            } else { 
                $wp3d_view_link = WP3D_Models()->get_model_link($post_id);
            }
            
            // // getting the image
            // // STATIC IMAGE
            // if ($wp3d_model_type == 'static_image') {
            //     $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
            //     if (!$model_src) {
            //         $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Static%20Image';
            //     }
                
            // // MATTERPORT IMAGE
            // } else { // must be Matterport
            //     if ($mp_id == 'error') {
            //         $model_src = 'https://placehold.it/400x230/&text=Error%20Retrieving%20Image';
            //     } else { // getting images from MP 
            //         $model_src = WP3D_Models()->get_model_image_src($post_id, 'thumb');
            //     }            
            // }
                        
            // getting terms
            $wp3d_assigned_terms = get_the_terms( $post->ID, 'model-type' );
            						
            if ( $wp3d_assigned_terms && ! is_wp_error( $wp3d_assigned_terms ) ) { 
            
            	$wp3d_terms_classes = array();
            
            	foreach ( $wp3d_assigned_terms as $wp3d_assigned_term ) {
            		$wp3d_terms_classes[] = sanitize_html_class($wp3d_assigned_term->slug);
            	}
            						
            	$wp3d_terms_assembled = join( " ", $wp3d_terms_classes );
            	
            } 
            
            // no extra classes by default, just here for the filter
            $wp3d_list_item_wrap_classes = '';
            ?> 
                
            <?php do_action( 'wp3d_list_item_before', get_the_ID() ); ?> 
            <?php if ($nomarker) { // if there are no markers ?>
                <div id="post-<?php the_ID(); ?>" class="active model-list-wrap<?php if ($flap_status) { echo ' has-flap'; } if (isset($wp3d_terms_assembled)) { echo ' all-models '.$wp3d_terms_assembled; } ?> <?php echo apply_filters( 'wp3d_list_item_wrap_classes', $wp3d_list_item_wrap_classes );?>">
            <?php } else { ?>
                <div id="post-<?php the_ID(); ?>" class="active model-list-wrap marker<?php if ($flap_status) { echo ' has-flap'; } if (isset($wp3d_terms_assembled)) { echo ' all-models '.$wp3d_terms_assembled; } ?> <?php echo apply_filters( 'wp3d_list_item_wrap_classes', $wp3d_list_item_wrap_classes );?>" data-id="<?php echo esc_attr($i); ?>" data-latlng="<?php echo esc_attr($address['lat']);?>,<?php echo esc_attr($address['lng']);?>" data-slug="<?php echo esc_attr($wp3d_view_link); ?>" data-thumb="<?php echo esc_attr(urlencode($model_src)); ?>" data-title="<?php echo urlencode(the_title_attribute( 'echo=0' )); ?>" data-marker-type="<?php echo $marker_type; ?>">
            <?php } ?>
            
            
                <?php if ($map != 'only') { // if we're not on a "map only" page....proceed 
                
                $viewlink_attributes = '';
                
                if ($viewlink == 'modal') { 
                    $viewlink_attributes = ' data-featherlight="iframe" data-featherlight-iframe-allowfullscreen="true" data-featherlight-iframe-height="100%" data-featherlight-iframe-width="100%"';
                } elseif ($viewlink == 'newtab') { 
                    $viewlink_attributes = ' target="_blank"';
                }                      
                ?>
                    
                    <a href="<?php echo $wp3d_view_link; ?>" class="image-wrap<?php echo $status_class; ?>"<?php echo $viewlink_attributes; ?>>
                        <?php do_action( 'wp3d_list_anchor_inside_before', get_the_ID() ); ?>
                        <img src="<?php echo esc_url($model_src); ?>" alt="<?php the_title_attribute(); ?> <?php _e('3D Model', 'wp3d-models'); ?>">
                        <?php do_action( 'wp3d_list_anchor_inside_after', get_the_ID() ); ?>
                    </a>
                    
                    <?php do_action( 'wp3d_list_buttons_before', get_the_ID() ); ?>
                    
                    <?php if ( ($nomarker) || ($map == "false") ) { // no markers....or map is not enabled ?>
                        
                    <div class="button-wrap viewonly-button-wrap">
                        <a href="<?php echo $wp3d_view_link; ?>" class="btn"<?php echo $viewlink_attributes; ?>><?php echo esc_html(apply_filters( 'wp3d_view_label', $view_label )); ?></a>
                    </div>
                        
                    <?php } else { ?>
                        
                    <div class="button-wrap hasmap-button-wrap wp3d-models-clearfix">
                        <a href="<?php echo $wp3d_view_link; ?>" class="view-btn btn"<?php echo $viewlink_attributes; ?>><?php echo esc_html(apply_filters( 'wp3d_view_label', $view_label )); ?></a><a class="map-btn btn" data-id="<?php echo esc_attr($i); $i++; //increment here ?>"><?php echo esc_html(apply_filters( 'wp3d_map_label', $map_label )); ?></a>
                    </div>
                        
                    <?php } ?>
                    
                    <?php do_action( 'wp3d_list_buttons_after', get_the_ID() ); ?>
                    
                    <?php do_action( 'wp3d_list_titles_before', get_the_ID() ); ?>
                    
                    <h2 class="wp3d-model-list-title"><?php the_title(); ?></h2> 
                    <?php if (get_field('model_subtitle')) { ?><h3 class="wp3d-model-list-subtitle"><?php the_field('model_subtitle'); ?></h3><?php } ?>
                    
                    <?php do_action( 'wp3d_list_titles_after', get_the_ID() ); ?>
                    
                <?php } // end $map 'only' check ?>

                </div>
                <?php do_action( 'wp3d_list_item_after', get_the_ID() ); ?> 
        
        <?php endwhile;
        wp_reset_postdata(); 
    
        }  // checking for ACF function 'get_field' ?>
        
        <?php if (get_option('wp3d_hide_branding') != 'on' && get_option('wp3d_hide_branding') !== false) { // checking for option to hide the WP3D Models branding" 
        $wp3d_credit_link = "https://wp3dmodels.com";
        $wp3d_credit_link = apply_filters( 'wp3d_custom_credit_link', $wp3d_credit_link );
        ?>
        <div id="wp3d-credit">
            <a href="<?php echo esc_url($wp3d_credit_link); ?>" target="_blank">
                <?php _e('Powered by WP3D Models', 'wp3d-models'); ?>
            </a>
        </div>
        <?php } // end hide branding check ?>
        
    </div> <!-- end #wp3d-models -->
    
    <?php do_action( 'wp3d_list_content_wrap_after', get_the_ID() ); ?>
        
    <?php 
    $wp3d_shortcode_ran = TRUE; // shortcode happened
    // New Filter to allow the ability to FORCE allow shortcode to run more than once. 
    // This addition allows for "pre-rendering" Page Builder tools to display when the otherwise wouldn't.
    $wp3d_shortcode_ran = apply_filters( 'wp3d_override_shortcode_limit', $wp3d_shortcode_ran );
    
    $mymodels = ob_get_clean();
    return $mymodels;
    } else { // NO RESULTS ?>
    
    <h1><?php _e('No Models To Display', 'wp3d-models'); ?></h1>
    
    <p><?php _e('The current settings for this page didn\'t return any results.  If you are the admin, please make some adjustments.', 'wp3d-models'); ?></p>
    <h3><?php _e('Current Settings:', 'wp3d-models'); ?></h3> 
    <ul>
        <li><?php _e('Model Type:', 'wp3d-models'); ?> <?php echo $type; ?></li>
        <li><?php _e('Client:', 'wp3d-models'); ?> <?php echo $client; ?></li>
        <li><?php _e('Filter:', 'wp3d-models'); ?> <?php if ($filter) { _e('TRUE', 'wp3d-models'); } else { _e('FALSE', 'wp3d-models'); } ?></li>
        <li><?php _e('Map:', 'wp3d-models'); ?> <?php if ("false" != $map) { _e('TRUE', 'wp3d-models'); } else { _e('FALSE', 'wp3d-models'); } ?></li>
    </ul>
    
<?php } 

}
    
add_shortcode('wp3d-calendly', 'wp3d_calendly_shortcode');

function wp3d_calendly_shortcode()
{
    $agents = WP3D_Models()->get_associated_agents();
    foreach ($agents as $agent) {
        if (!empty($agent['calendly_enabled']) && $agent['calendly_type'] == 'text') {
            $url = $agent['calendly_event_link'];
            $text = !empty($agent['custom_link_title']) ? $agent['custom_link_title'] : 'Schedule Tour';
            $link = '<a href="" onclick="Calendly.initPopupWidget({url: \'' . $url . '\'});return false;">' . $text . '</a>';
            return $link;
        }
    }
    
    return '';
}

?>
