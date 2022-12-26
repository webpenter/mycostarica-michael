<?php 

add_action( 'tgmpa_register', 'wp3d_register_plugins' );

/**
 * Register the supplementary plugins for this theme.
 *
 */

function wp3d_register_plugins() {

    // These are the no-brainers
    $plugins = array(
                    
            // WPSEO -- WordPress Plugin Repository.
            array(
                'name'      => 'WordPress SEO by Yoast',
                'slug'      => 'wordpress-seo',
                'required'  => false,
                'is_callable' => 'wpseo_init', // this will pick up if another version ("Premium") of Yoast SEO is installed (like the PREMIUM version)
            ),
    
            // Simple Ordering -- WordPress Plugin Repository.
            array(
                'name'      => 'Simple Page Ordering',
                'slug'      => 'simple-page-ordering',
                'required'  => false,
            ) 
    
    );

    // Checking to see if any version of ACF is installed....if not, require the FREE version
    if (!class_exists('acf')) {  
        
        array_push( $plugins, 
    
            // ACF -- WordPress Plugin Repository.
            array(
                'name'      => 'Advanced Custom Fields',
                'slug'      => 'advanced-custom-fields',
                'required'  => true,
            )
            
        );
        
    }

    // Now checking to see if the the ACF Gallery Class ('acf_field_gallery') is available....if not (because only the FREE/v.4 version of ACF is installed), then require it from the bundled (non-updateable) version
    if (!class_exists('acf_field_gallery')) {  
        
        array_push( $plugins, 
    
            // ACF -- Gallery Add On
    		array(
    			'name'               => 'Advanced Custom Fields: Gallery Field', // The plugin name.
    			'slug'               => 'acf-gallery', // The plugin slug (typically the folder name).
    			'source'             => plugin_dir_path( __FILE__ ) . 'plugins/acf-gallery.zip', // The plugin source.
    			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
    			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
    			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
    			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
    			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
    			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
    		)
            
        );
        
    }
    
    // Now checking to see if the the ACF Gallery Class ('acf_field_repeater') is available....if not (because only the FREE/v.4 version of ACF is installed), then require it from the bundled (non-updateable) version
    if (!class_exists('acf_field_repeater')) {  
        
        array_push( $plugins, 
    
            // ACF -- Repeater Add On
    		array(
    			'name'               => 'Advanced Custom Fields: Repeater Field', // The plugin name.
    			'slug'               => 'acf-repeater', // The plugin slug (typically the folder name).
    			'source'             => plugin_dir_path( __FILE__ ) . 'plugins/acf-repeater.zip', // The plugin source.
    			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
    			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
    			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
    			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
    			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
    			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
    		)
            
        );
        
    }  

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '', // Default absolute path to pre-packaged plugins within WP3D Models.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => __('<div class="notice notice-warning"><p>These plugins are recommended (and some required) by WP3D Models. Without them, your installation of WP3D Models will not work correctly.</p></div>', 'wp3d-models'),  // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Plugins', 'wp3d-models'),
            'menu_title'                      => __( 'Install Plugins', 'wp3d-models'),
            'installing'                      => __( 'Installing Plugin: %s', 'wp3d-models'), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'wp3d-models'),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'wp3d-models'), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'wp3d-models'), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'wp3d-models'),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'wp3d-models'),
            'return'                          => __( 'Return to Required Plugins Installer', 'wp3d-models'),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'wp3d-models'),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'wp3d-models' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}


?>