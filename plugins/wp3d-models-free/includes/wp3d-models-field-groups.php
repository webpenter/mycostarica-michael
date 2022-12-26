<?php 
// Model Type Check (returns the actual model type, or 'matterport', or 'new')
$wp3d_type_info = WP3D_Models()->get_wp3d_type_info();

// ACF Type Check
$wp3d_acf_version = WP3D_Models()->get_acf_version();

// DEBUGGIN
//print_r($wp3d_type_info); exit;

// Address Retrival
$has_address_check = 'no_address';
$has_address = WP3D_Models()->has_wp3d_address();

if ($has_address) {
	$has_address_check = 'custom_address';
} else {
	$has_address_check = 'matterport_address';
}

// Set the default "View" link
$default_view_link = '';
$default_view_link = get_option('wp3d_default_view_link');
$default_view_link = esc_attr($default_view_link);

// Set the default "Gallery" type based on the global setting
$default_gallery_type = '';
$global_gallery_type = get_option('wp3d_global_default_gallery');
if ($global_gallery_type == '') { // not yet set
$default_gallery_type = 'standard_slider';
} else {
$default_gallery_type = $global_gallery_type;
}

// Get the Default Guided Tour Delay
$default_guided_tour_seconds = get_option('wp3d_mp_guided_tour_seconds');
if ($default_guided_tour_seconds) { 
	$default_guided_tour_seconds = intval($default_guided_tour_seconds); 
} else {
	$default_guided_tour_seconds = '1';
}

// Get Default Showcase Hightlight Time
$default_showcase_highlight_time = get_option('wp3d_mp_highlight_time');
if ($default_showcase_highlight_time) { 
	$default_showcase_highlight_time = intval($default_showcase_highlight_time); 
} else {
	$default_showcase_highlight_time = '3500';
}

// Max Property Tabs 
$max_property_tabs = get_option('wp3d_max_property_tabs');

if ($max_property_tabs) { 
	$max_property_tabs = intval($max_property_tabs); 
} else {
	$max_property_tabs = '3';
}

// Media buttons on Info Tabs
$enable_property_tab_media = get_option('wp3d_enable_property_tab_media');

if ($enable_property_tab_media) { 
	$enable_property_tab_media = 'yes'; 
} else {
	$enable_property_tab_media = 'no';
}

/* CHECKING FOR ACF, specific model type & Creating Model Fields */

if (function_exists("register_field_group")) {
	
	// first check to see if the array includes "model_type"
	if (!isset($wp3d_type_info['model_type'])) { $wp3d_type_info['model_type'] = 'none'; }
	
	// check for Model Type as it relates to Form conditionals
	if ($wp3d_type_info['model_type'] == 'none' || $wp3d_type_info['model_type'] == 'matterport') {
		$lead_generation_form_conditional_array = array (
			'default-api' => __('Built in Form - Send to the "Contact Email" info retrieved from Matterport', 'wp3d-models'),
			'default-agents' => __('Built In Form - Send to all attached "Agents"', 'wp3d-models'),					
			'default-custom' => __('Built In Form - Send to a custom email address', 'wp3d-models'),
			'shortcode' => __('Use a Shortcode (3rd Party Plugin Form)', 'wp3d-models'),
		);
	} else {
		$lead_generation_form_conditional_array = array (
			'default-agents' => __('Built In Form - Send to all attached "Agents"', 'wp3d-models'),					
			'default-custom' => __('Built In Form - Send to a custom email address', 'wp3d-models'),
			'shortcode' => __('Use a Shortcode (3rd Party Plugin Form)', 'wp3d-models'),
		);		
	}
	
	// ################## BEGIN MODEL TYPE ##################
		register_field_group(array (
			'id' => 'acf_model-information',
			'title' => __('Model Information', 'wp3d-models'),
			'key' => 'group_58b059e513b87',
			'fields' => array (
				array (
					'key' => 'field_5504b8036d565',
					'label' => __('Model', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),
				array (
					'key' => 'field_587557a304734',
					'label' => 'Model Base',
					'name' => 'wp3d_model_type',
					'type' => 'select',
					'choices' => array (
						'matterport' => 'Matterport',
						'threesixtytours' => 'ThreeSixty Tours',
						'video' => 'Video',
						'generic' => 'Generic iFrame',
						'static_image' => 'Static Image',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),						
				array (
					'key' => 'field_5504b8726d566',
					'label' => __('Matterport URL', 'wp3d-models'),
					'name' => 'model_link',
					'type' => 'text',
					'instructions' => __('Copy & Paste your BRANDED Matterport Showcase Link from the Sharing column of your \'my.matterport.com\' dashboard.  <a href="https://wp3dmodels.com/doc/use-matterport-deep-links/" target="_blank">Supports Deep Links</a>.<br><b>Make sure your Showcase is shared as PUBLIC!</b>', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),
						),
						'allorany' => 'all',
					),						
					'default_value' => '',
					'placeholder' => 'https://my.matterport.com/show/?m=XXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				
				// MPEMBED START
				
				array (
					'key' => 'field_5b226cbd9e754',
					'label' => __('Customize Showcase with MPEmbed', 'wp3d-models'),
					'name' => 'customize_showcase',
					'type' => 'select',
					'instructions' => __('Enhance & further customize your Matterport Showcase content using <a href="https://mpembed.com" target="_blank">MPEmbed</a>.<br><em>Please refer to <a href="https://mpembed.com/docs/" target="_blank">MPEmbed Documentation</a> for option explanations and <a href="https://mpembed.com/contact/" target="_blank">contact MPEmbed</a> directly for assistance/support.<br></em>NOTE: Some of these options may force override "stock" Matterport options that may already be set in the "Opt" tab.</em>', 'wp3d-models'),				
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'' => 'NONE (Default Matterport Showcase)',
						'mpembed' => 'Enable MPEmbed Customization',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,					
				),
				
				// PREMIUM
				array(
					'key' => 'field_5e93622d984d3',
					'label' => 'MPEmbed Premium',
					'name' => 'mpembed_premium',
					'type' => 'select',
					'instructions' => 'Enter your existing MPEmbed Premium account information.',
					'required' => 0,
					
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'disabled' => 'Disabled',
						'enabled' => 'Enable MPEmbed Premium',
					),
					'default_value' => array(
					),
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				),
				array(
					'key' => 'field_5e9362f5984d4',
					'label' => 'Premium User ID',
					'name' => 'mpembed_premium_userid',
					'type' => 'text',
					'instructions' => 'Required (sets "mpu" parameter)',
					'required' => 1,
					'conditional_logic' => array(
						array(
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),							
							array(
								'field' => 'field_5e93622d984d3',
								'operator' => '==',
								'value' => 'enabled',
							),
						),
					),
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5e93642f984d5',
					'label' => 'Premium Version ID',
					'name' => 'mpembed_premium_version',
					'type' => 'text',
					'instructions' => 'Optional (sets "mpv" parameter)',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),							
							array(
								'field' => 'field_5e93622d984d3',
								'operator' => '==',
								'value' => 'enabled',
							),
						),
					),
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),				
				
				
				array (
					'key' => 'field_5b3a4eebc884e',
					'label' => __('Details Tab', 'wp3d-models'),
					'name' => 'mpembed_infotab_details',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'1' => 'Position: 1',
						'2' => 'Position: 2',
						'3' => 'Position: 3',
					),
					'default_value' => '1',
					'allow_null' => 0,
					'multiple' => 0,
				),	
				
				array (
					'key' => 'field_5b3a4fd45780b',
					'label' => __('Highlights Reel Tab', 'wp3d-models'),
					'name' => 'mpembed_infotab_hdir',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'1' => 'Position: 1',
						'2' => 'Position: 2',
						'3' => 'Position: 3',
					),
					'default_value' => '2',
					'allow_null' => 0,
					'multiple' => 0,
				),
				
				array (
					'key' => 'field_5b3a505c6696a',
					'label' => __('Mattertags Directory Tab', 'wp3d-models'),
					'name' => 'mpembed_infotab_mdir',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'1' => 'Position: 1',
						'2' => 'Position: 2',
						'3' => 'Position: 3',
					),
					'default_value' => '3',
					'allow_null' => 0,
					'multiple' => 0,
				),
				
				array (
					'key' => 'field_5b3a50838d5c8',
					'label' => __('Mattertags Directory Search Tab', 'wp3d-models'),
					'name' => 'mpembed_infotab_mdirsearch',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'1' => 'Enabled',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),	
				
				array (
					'key' => 'field_5b38e5366bad6',
					'label' => __('Merge Reels', 'wp3d-models'),
					'instructions' => __('<i>Use MPEmbed\'s combined highlight reel feature to merge highlight reels between tours.</i>', 'wp3d-models'),
					'name' => 'mpembed_merge_reels',
					'type' => 'true_false',
					'message' => 'Enable Multi-Tour Highlight Reel',
					'default_value' => 0,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
				),				
				
				array (
					'key' => 'field_5b38e96408862',
					'label' => 'Enter Matterport IDs to Merge',
					'name' => 'mpembed_merge_reels_ids',
					'instructions' => __('Enter <em>Comma Separated</em> Matterport IDs (i.e.: "PuyiJAwAvg2,kKpiGmQKHF6,pGAM6U7AGB8,AHLcxHouYtp")', 'wp3d-models'),
					'type' => 'text',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b38e5366bad6',
								'operator' => '==',
								'value' => '1',
							),
														
						),
						'allorany' => 'all',
					),	
					'placeholder' => 'SpaceID,SpaceID,SpaceID',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',					
				),				
				
				
				
				
				
				
				//BG MUSIC
				array(
					'key' => 'field_5e9328c487c84',
					'label' => 'Background Music',
					'name' => 'mpembed_background_music',
					'type' => 'select',
					'instructions' => '',
					'required' => 0,
		
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
		
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'disabled' => 'Disabled',
						'enabled' => 'Enable Background Music',
					),
					'default_value' => array(
					),
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				),
				array(
					'key' => 'field_5e931fc93dc34',
					'label' => 'Background Music File',
					'name' => 'mpembed_bg_music_file',
					'type' => 'file',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => array(
						array(
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),					
							array(
								'field' => 'field_5e9328c487c84',
								'operator' => '==',
								'value' => 'enabled',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'url',
					'library' => 'all',
					'min_size' => '',
					'max_size' => '',
					'mime_types' => 'mp3,ogg,wav',
				),
				array(
					'key' => 'field_5e93274387c82',
					'label' => 'Background Music Loop',
					'name' => 'mpembed_bg_music_loop',
					'type' => 'select',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),					
							array(
								'field' => 'field_5e9328c487c84',
								'operator' => '==',
								'value' => 'enabled',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array(
						'1' => 'Loop Audio',
						'0' => 'Do Not Loop Audio',
					),
					'default_value' => array(
						0 => '1',
					),
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'return_format' => 'value',
					'ajax' => 0,
					'placeholder' => '',
				),
				array(
					'key' => 'field_5e93286687c83',
					'label' => 'Background Music Volume',
					'name' => 'mpembed_bg_music_volume',
					'type' => 'range',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),					
							array(
								'field' => 'field_5e9328c487c84',
								'operator' => '==',
								'value' => 'enabled',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '0.5',
					'min' => '0.1',
					'max' => 1,
					'step' => '.1',
					'prepend' => '',
					'append' => '',
				),				
				
				
				
				
				
				
				
				array (
					'key' => 'field_5b22976645ac7',
					'label' => __('Custom Logo/Avatar/Icon', 'wp3d-models'),
					'name' => 'mpembed_custom_logo',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'custom_logo' => 'Upload custom 32x32 logo image',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),	
				
				array (
					'key' => 'field_5b25565c696b9',
					'label' => __('Custom Logo Upload', 'wp3d-models'),
					'name' => 'mpembed_custom_logo_url',
					'type' => 'image',
					'instructions' => __('<i>The image should be optimized and sized at (exactly) 32 x 32 pixels (PNG/JPG/SVG).</i>', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b22976645ac7',
								'operator' => '==',
								'value' => 'custom_logo',
							),
						),
						'allorany' => 'all',
					),
					'save_format' => 'url',
					'preview_size' => 'thumbnail',
					'library' => 'uploadedTo',
				),				
				
				array (
					'key' => 'field_5b2297fc046e9',
					'label' => __('Custom Image', 'wp3d-models'),
					'name' => 'mpembed_custom_image',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'wp3d_image' => 'Use current WP3D Small Logo',
						'custom_image' => 'Upload custom image',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				
				array (
					'key' => 'field_5b2558d94d0e6',
					'label' => __('Custom Image Upload', 'wp3d-models'),
					'name' => 'mpembed_custom_image_url',
					'type' => 'image',
					'instructions' => __('<i>The image should be optimized and have a max width of 330 pixels (GIF/PNG/JPG/SVG).</i>', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b2297fc046e9',
								'operator' => '==',
								'value' => 'custom_image',
							),
						),
						'allorany' => 'all',
					),
					'save_format' => 'url',
					'preview_size' => 'medium',
					'library' => 'uploadedTo',
				),					
			
				array (
					'key' => 'field_5b228bd2059aa',
					'label' => __('Mini Map', 'wp3d-models'),
					'name' => 'mpembed_minimap',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'1' => 'Start Minimized',
						'2' => 'Start Closed',
						'3' => 'Start Maximized',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),					
				
				array (
					'key' => 'field_5b228f7ed8711',
					'label' => __('Mini Map Rotate', 'wp3d-models'),
					'name' => 'mpembed_minimap_rotate',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b228bd2059aa',
								'operator' => '!=',
								'value' => 'disabled',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'90' => 'Rotate 90 degrees',
						'180' => 'Rotate 180 degrees',
						'270' => 'Rotate 270 degrees',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				
				array (
					'key' => 'field_5b253b18bc4fe',
					'label' => __('Mini Map Photo Filter', 'wp3d-models'),
					'name' => 'mpembed_minimap_filter',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b228bd2059aa',
								'operator' => '!=',
								'value' => 'disabled',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'favorite' => 'Favorite',
						'lighten' => 'Lighten',
						'lightenless' => 'Lighten (Less)',
						'lightenmore' => 'Lighten (More)',
						'darken' => 'darken',
						'darkenmore' => 'Darken (More)',
						'oversaturate' => 'Oversaturate',
						'invert' => 'Invert',
						'blackandwhite' => 'Black & White',
						'sepia' => 'Sepia',
						'hueshift90' => 'Hue Shift 90',
						'hueshift180' => 'Hue Shift 180',
						'hueshift270' => 'Hue Shift 270',						
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),				
				
				array (
					'key' => 'field_5b2292d88b1a0',
					'label' => __('Hide Hotspots', 'wp3d-models'),
					'name' => 'mpembed_fade_hotspots',
					'type' => 'true_false',
					'message' => 'Hide inactive hotspots when map unfocused',
					'default_value' => 0,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b228bd2059aa',
								'operator' => '!=',
								'value' => 'disabled',
							),
						),
						'allorany' => 'all',
					),
				),
				
				array (
					'key' => 'field_5b40ed1e6849d',
					'label' => __('Small Hotspots', 'wp3d-models'),
					'name' => 'mpembed_small_hotspots',
					'type' => 'true_false',
					'message' => 'Display hotspots at 50% size',
					'default_value' => 0,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b228bd2059aa',
								'operator' => '!=',
								'value' => 'disabled',
							),
						),
						'allorany' => 'all',
					),
				),				
				
				array (
					'key' => 'field_5b28039bcf9fc',
					'label' => __('Custom Mini Map (Experimental)', 'wp3d-models'),
					'name' => 'mpembed_custom_minimap_url',
					'type' => 'image',
					'instructions' => __('<i>This will work very well if your custom mini map was created and aligned to match the automatically generated mini map. If the map is different, you can tweak some offset values below.</i>', 'wp3d-models'),
					//'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b228bd2059aa',
								'operator' => '!=',
								'value' => 'disabled',
							),
						),
						'allorany' => 'all',
					),
					'save_format' => 'url',
					'preview_size' => 'thumbnail',
					'library' => 'uploadedTo',
				),
				
				array (
					'key' => 'field_5b280448ce724',
					'label' => __('Mini Map Offset (Experimental)', 'wp3d-models'),
					'name' => 'mpembed_custom_minimap_offset',
					'type' => 'text',
					'instructions' => __('Align hotspots with custom map', 'wp3d-models'),
					'default_value' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b228bd2059aa',
								'operator' => '!=',
								'value' => 'disabled',
							),
						),
						'allorany' => 'all',
					),
					'placeholder' => 'Width %,Height %,X offset %,Y offset %',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),

				array (
					'key' => 'field_5b2551ae36d1b',
					'label' => __('Google Analytics', 'wp3d-models'),
					'name' => 'mpembed_google_analytics',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'wp3d_ga' => 'Use Google Analytics from WP3D (if enabled via MonsterInsights)',
						'custom_ga' => 'Provide custom Google Analytics ID',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),	
				
				array (
					'key' => 'field_5b2552df24b2f',
					'label' => __('Custom Google Analytics ID', 'wp3d-models'),
					'name' => 'mpembed_custom_ga',
					'type' => 'text',
					'instructions' => __('Paste your custom Google Analytics Tracking ID.', 'wp3d-models'),
					'default_value' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
							array (
								'field' => 'field_5b2551ae36d1b',
								'operator' => '==',
								'value' => 'custom_ga',
							),							
						),
						'allorany' => 'all',
					),
					'placeholder' => 'UA-XXXXXXXX',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				
				array (
					'key' => 'field_5b40f3c834e78',
					'label' => __('Showcase Photo Filter', 'wp3d-models'),
					'name' => 'mpembed_showcase_filter',
					'type' => 'select',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'disabled' => 'Disabled',
						'favorite' => 'Favorite',
						'lighten' => 'Lighten',
						'lightenless' => 'Lighten (Less)',
						'lightenmore' => 'Lighten (More)',
						'darken' => 'darken',
						'darkenmore' => 'Darken (More)',
						'oversaturate' => 'Oversaturate',
						'invert' => 'Invert',
						'blackandwhite' => 'Black & White',
						'sepia' => 'Sepia',
						'hueshift90' => 'Hue Shift 90',
						'hueshift180' => 'Hue Shift 180',
						'hueshift270' => 'Hue Shift 270',						
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),				
				
				array (
					'key' => 'field_5b253f7a817e5',
					'label' => __('Additional Features', 'wp3d-models'),
					'name' => 'mpembed_additional_features',
					'type' => 'checkbox',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'choices' => array (
						'optmt' => 'Mattertags appear in infobox',
						'nofade' => 'Disable Fading GUI',
						'opthr' => 'Hide Highlights Reel',
					),
					'default_value' => 'opthr
										',
					'layout' => 'horizontal',
				),
				
				array (
					'key' => 'field_5b254a556b6b2',
					'label' => __('Copyright Text', 'wp3d-models'),
					'name' => 'mpembed_copyright_text',
					'type' => 'text',
					'instructions' => __('Include customized showcase footer copyright text.', 'wp3d-models'),
					'default_value' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				
				array (
					'key' => 'field_5b2555fcae21c',
					'label' => __('UI Tint', 'wp3d-models'),
					'name' => 'mpembed_ui_tint',
					'type' => 'color_picker',
					'instructions' => __('Tint the UI with a color of your choice.', 'wp3d-models'),
					'default_value' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),						
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
				),
				
				array (
					'key' => 'field_5b28057a830a9',
					'label' => __('Wildcard Parameters', 'wp3d-models'),
					'name' => 'mpembed_wildcard_parameters',
					'type' => 'text',
					'instructions' => __('An advanced user "catch-all" for bleeding edge MPEmbed parameters. Use at your own risk.<br>Comma separated multiple values are supported.', 'wp3d-models'),
					'default_value' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),							
							array (
								'field' => 'field_5b226cbd9e754',
								'operator' => '==',
								'value' => 'mpembed',
							),
						),
						'allorany' => 'all',
					),
					'placeholder' => 'newparameter=value,newparameter=value',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				
				
				// MPEMBED END

				array (
					'key' => 'field_59173daec1970',
					'label' => __('ThreeSixty Tours URL', 'wp3d-models'),
					'name' => 'tst_link',
					'type' => 'text',
					'instructions' => __('Copy & Paste your ThreeSixty Tours Link from your \'my.threesixty.tours\' Dashboard<br><b><a href="https://threesixty.tours" target="_blank">Learn more about ThreeSixty Tours &raquo;</a></b>', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'threesixtytours',
							),
						),
						'allorany' => 'all',
					),						
					'default_value' => '',
					'placeholder' => 'http://my.threesixty.tours/app/v/xxxxxx/xxxxxx',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_591889003e73e',
					'label' => __('Base Video Type', 'wp3d-models'),
					'name' => 'base_video_type',
					'type' => 'select',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="basevideotypehelp" alt="Use a YouTube or Vimeo video as the base content for your Model." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is "Base Video Type"?</span>', 'wp3d-models'),				
					'choices' => array (
						'youtube' => 'YouTube',
						'vimeo' => 'Vimeo',
					),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'video',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5918975b1e80d',
					'label' => __('YouTube BRANDED Base Video Link', 'wp3d-models'),
					'name' => 'base_youtube_video_link',
					'type' => 'text',
					'instructions' => __('Enter the full BRANDED YouTube video URL here. Copy this directly from the browser window, do not use the short version, or any YouTube-provided embed code.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'video',
							),
							array (
								'field' => 'field_591889003e73e',
								'operator' => '==',
								'value' => 'youtube',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://www.youtube.com/watch?v=XXXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_59189767cc430',
					'label' => __('YouTube UNBRANDED Base Video Link', 'wp3d-models'),
					'name' => 'base_youtube_unbranded_video_link',
					'type' => 'text',
					'instructions' => __('OPTIONAL: Enter your separate UNBRANDED (MLS-Compliant) YouTube video URL here.', 'wp3d-models'),
					//'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'video',
							),
							array (
								'field' => 'field_591889003e73e',
								'operator' => '==',
								'value' => 'youtube',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://www.youtube.com/watch?v=XXXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),			
				array (
					'key' => 'field_591897742aa88',
					'label' => __('Vimeo BRANDED Base Video Link', 'wp3d-models'),
					'name' => 'base_vimeo_video_link',
					'type' => 'text',
					'instructions' => __('Enter the full BRANDED Vimeo video URL here. Copy this directly from the browser window, do not use the short version, or any Vimeo-provided embed code.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'video',
							),
							array (
								'field' => 'field_591889003e73e',
								'operator' => '==',
								'value' => 'vimeo',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://vimeo.com/XXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_5918977fa9ffe',
					'label' => __('Vimeo UNBRANDED Base Video Link', 'wp3d-models'),
					'name' => 'base_vimeo_unbranded_video_link',
					'type' => 'text',
					'instructions' => __('OPTIONAL: Enter your separate UNBRANDED (MLS-Compliant) Vimeo video URL here.', 'wp3d-models'),
					//'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'video',
							),
							array (
								'field' => 'field_591889003e73e',
								'operator' => '==',
								'value' => 'vimeo',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://vimeo.com/XXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_5918c75ba6dc7',
					'label' => __('Generic iFrame Embed', 'wp3d-models'),
					'name' => 'generic_iframe',
					'type' => 'textarea',
					'instructions' => __('Enter the RAW <i>&lt;iframe&gt;</i> code for this particular embed.', 'wp3d-models'),
					'required' => 1,					
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'generic',
							),
						),
						'allorany' => 'all',
					),					
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '',
					'formatting' => 'none',
				),
				array (
					'key' => 'field_58755a5a0c541',
					'label' => __('Static Image', 'wp3d-models'),
					'name' => 'image_override',
					'type' => 'image',
					'instructions' => __('<i>The image should be optimized and (at least) 1080px tall and 1920px wide.</i><br><b>NOTE:</b> This "Static Image" is required for select Model Base options where it is not possible (or reliable) to dynamically retrieve a large enough hero image.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'static_image',
							),
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'video',
							),
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'generic',
							),							
						),
						'allorany' => 'any',
					),
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'uploadedTo',
				),				
				array (
					'key' => 'field_5505e4a312821',
					'label' => __('Model Subtitle', 'wp3d-models'),
					'name' => 'model_subtitle',
					'type' => 'text',
					'instructions' => __('Include optional subtitle text for your Model. This is seen both on the listing page(s) as well as all Model "views"', 'wp3d-models'),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_55fafb4189f07',
					'label' => __('Model Primary Content', 'wp3d-models'),
					'name' => 'model_content',
					'type' => 'wysiwyg',
					'instructions' => __('General/introductory information about your Model.  This description is included with social sharing as well as all views, including those that are "unbranded"', 'wp3d-models'),
					'default_value' => '',
					'toolbar' => 'full',
					'media_upload' => 'yes',
				),			
				array (
					'key' => 'field_55fad97e6fc5a',
					'label' => __('Default View for Shortcode & Related Model', 'wp3d-models'),
					'name' => 'default_view_link',
					'type' => 'select',
					'instructions' => __('This applies to all internally generated links (shortcode & related Model lists)', 'wp3d-models'),
					'choices' => array (
						'stock' => __('STANDARD (Current theme page formatting)', 'wp3d-models'),
						'skinned' => __('SKINNED ("Single Property Website" formatting)', 'wp3d-models'),
						'nobrand' => __('NO BRAND (Simple unbranded page formatting)', 'wp3d-models'),
						'fullscreen' => __('FULL SCREEN (Fullscreen Model formatting)', 'wp3d-models'),
						'fullscreen-nobrand' => __('FULL SCREEN + NO BRAND (Fullscreen &amp; Unbranded Model formatting)', 'wp3d-models'),
						'custom' => __('CUSTOM URL (Custom location URL)', 'wp3d-models'),
					),
					'default_value' => $default_view_link,
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_55577d4e324d9',
					'label' => __('View Link Override', 'wp3d-models'),
					'name' => 'view_link_override',
					'type' => 'text',
					'instructions' => __('Enter the <em>overriding</em> URL you\'d like the "VIEW" button to go to.	Can be an absolute (starts with "http://") or relative (starts with "/") URL.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fad97e6fc5a',
								'operator' => '==',
								'value' => 'custom',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => __('CAUTION : Anything entered here will change where this Model\'s "VIEW" button goes!', 'wp3d-models'),
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
				array (
					'key' => 'field_560c5d8cfc91c',
					'label' => __('Related Models', 'wp3d-models'),
					'name' => 'related_models',
					'type' => 'select',
					'instructions' => __('Optionally show related Models by "Type" or "Client" ("standard" & "nobrand" views)', 'wp3d-models'),
					'choices' => array (
						'none' => 'Don\'t Show Related Models',
						'type' => 'Show Related Models by "Type"',
						'client' => 'Show Related Models by "Client"',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_560c5ce3fc91a',
					'label' => __('Related "Type" Models', 'wp3d-models'),
					'name' => 'related_type_models',
					'type' => 'taxonomy',
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_560c5d8cfc91c',
								'operator' => '==',
								'value' => 'type',
							),
						),
						'allorany' => 'all',
					),
					'taxonomy' => 'model-type',
					'field_type' => 'select',
					'allow_null' => 0,
					'load_save_terms' => 0,
					'return_format' => 'object',
					'multiple' => 0,
				),
				array (
					'key' => 'field_560c5d68fc91b',
					'label' => __('Related "Client" Models', 'wp3d-models'),
					'name' => 'related_client_models',
					'type' => 'taxonomy',
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_560c5d8cfc91c',
								'operator' => '==',
								'value' => 'client',
							),
						),
						'allorany' => 'all',
					),
					'taxonomy' => 'model-client',
					'field_type' => 'select',
					'allow_null' => 0,
					'load_save_terms' => 0,
					'return_format' => 'object',
					'multiple' => 0,
				),
                array (
                    'key' => 'field_bb773a38c8228',
                    'label' => __('Troubleshooting', 'wp3d-models'),
                    'name' => 'troubleshooting',
                    'instructions' => __(
                    	'<div class="matterport_zip"><a href="'.esc_url(plugins_url().'/wp3d-models-free/includes/wp3d-models-get-zip.php?mid=' . (!empty($_GET['post']) ? WP3D_Models::getMpID($_GET['post']) : '' )) . '" onclick="return confirm(\'Generate ZIP download from Matterport?\')">Get ZIP file from Matterport</a> - Use this link to download the full ZIP file for your model from Matterport.  These are large files and you may need to extend the default PHP timeout set with the host (heavy files)</div>
<a class="wp3d_post_link" data-post_id="'.(!empty($_GET['post']) ? $_GET['post'] : '0').'" href="'.esc_url('https://developers.facebook.com/tools/debug/sharing/?q=wp3d_post_url').'" target="_blank">Facebook Debugger</a> - This is where you come to refresh the cache (i.e.  re-scrape) the info from WP3D to showcase in Facebook.  Simply click the link in Facebook to “Fetch New Information”)', 'wp3d-models'),
                ),
				array (
					'key' => 'field_55fae1a7aaf70',
					'label' => __('Info', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),
				array (
					'key' => 'field_597421e963e10',
					'label' => __('Model Status', 'wp3d-models'),
					'name' => 'model_status',
					'type' => 'select',
					'instructions' => __('Set Model Status ("Sold", "Sale Pending", "Custom") - Customize Here: \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Branding -> Custom Status Image</a>\'', 'wp3d-models'),
					'choices' => array (
						'sold' => __('Sold', 'wp3d-models'),
						'pending' => __('Sale Pending', 'wp3d-models'),
						'custom' => __('Custom', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),					
				array (
					'key' => 'field_560c4e03c7e4f',
					'label' => 'SOLD',
					'name' => 'mark_sold',
					'type' => 'true_false',
					'instructions' => __('Note: Customize Here: \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Labels</a>\'', 'wp3d-models'),
					'message' => 'Mark as SOLD',
					'default_value' => 0,
				),	
				array (
					'key' => 'field_572a6049e419c',
					'label' => 'SALE PENDING',
					'name' => 'mark_pending',
					'type' => 'true_false',
					'instructions' => __('Note: Customize Here: \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Labels</a>\'', 'wp3d-models'),
					'message' => 'Mark as SALE PENDING',
					'default_value' => 0,
				),
				array (
					'key' => 'field_5600b127d8fcd',
					'label' => __('Property Info Details', 'wp3d-models'),
					'name' => 'add_property_info_details',
					'type' => 'true_false',
					'instructions' => __('Use this option to add short snippets of content, like \'Price\' or number of \'Bedrooms\', \'Bathrooms\', etc.', 'wp3d-models'),
					'message' => __('Add property info details?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_5600b183d8fce',
					'label' => __('Property Info Details', 'wp3d-models'),
					'name' => 'property_info_details',
					'type' => 'repeater',
					'instructions' => __('Enter as many, or as few, details (Title + Value) as you like and drag/drop to re-order.', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600b127d8fcd',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'sub_fields' => array (
						array (
							'key' => 'field_5600b51dd8fcf',
							'label' => __('Title', 'wp3d-models'),
							'name' => 'title',
							'type' => 'text',
							'column_width' => '',
							'default_value' => '',
							'placeholder' => __('Enter Title: (i.e. "Price", "Beds", "Baths")', 'wp3d-models'),
							'prepend' => '',
							'append' => '',
							'formatting' => 'html',
							'maxlength' => '',
						),
						array (
							'key' => 'field_5600b588d8fd0',
							'label' => __('Value', 'wp3d-models'),
							'name' => 'value',
							'type' => 'text',
							'column_width' => '',
							'default_value' => '',
							'placeholder' => __('Enter Corresponding Value: (i.e. "$675,000", "3", "4")', 'wp3d-models'),
							'prepend' => '',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => '',
						),
					),
					'row_min' => 1,
					'row_limit' => '',
					'layout' => 'table',
					'button_label' => __('Add Another Detail', 'wp3d-models'),
				),
				array (
					'key' => 'field_560106905e9c6',
					'label' => __('Property Info Tabs', 'wp3d-models'),
					'name' => 'add_property_text_tabs',
					'type' => 'true_false',
					'instructions' => __('By default, any content found in the primary WYSIWYG field (in the "Model" tab) will always be displayed.	Additional custom tabbed content can be added here to be published alongside this primary content.', 'wp3d-models'),
					'message' => __('Add property info tabs?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_560106b15e9c7',
					'label' => __('Property Info Tabs', 'wp3d-models'),
					'name' => 'property_text_tabs',
					'type' => 'repeater',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_560106905e9c6',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'sub_fields' => array (
						array (
							'key' => 'field_5601072f5e9c8',
							'label' => __('Tab Title', 'wp3d-models'),
							'name' => 'tab_title',
							'type' => 'text',
							'column_width' => '',
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => '',
						),
						array (
							'key' => 'field_560107485e9c9',
							'label' => __('Tab WYSIWYG', 'wp3d-models'),
							'name' => 'tab_wysiwyg',
							'type' => 'wysiwyg',
							'column_width' => '',
							'default_value' => '',
							'toolbar' => 'full',
							'media_upload' => $enable_property_tab_media,
						),
					),
					'row_min' => 1,
					'row_limit' => $max_property_tabs,
					'layout' => 'row',
					'button_label' => __('Add Additional Tab', 'wp3d-models'),
				),
				array (
					'key' => 'field_5600f99123391',
					'label' => __('Disable Sharing', 'wp3d-models'),
					'name' => 'disable_model_sharing',
					'type' => 'true_false',
					'message' => __('Disable ALL Social Sharing', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_564b6bb18a959',
					'label' => __('Disable Connect', 'wp3d-models'),
					'name' => 'disable_model_connect',
					'type' => 'true_false',
					'message' => __('Disable ALL Connect Links', 'wp3d-models'),
					'default_value' => 0,
				),				
				array (
					'key' => 'field_588041bc04d41',
					'label' => __('Intro', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'static_image',
							),
						),
						'allorany' => 'all',
					),	
				),
				array (
					'key' => 'field_55fae055e188f',
					'label' => __('Branded Intro', 'wp3d-models'),
					'name' => 'showcase_branding',
					'type' => 'select',
					'instructions' => __('Choose how you want the interactive content to appear, by default, on single (unskinned) pages, in "fullscreen" mode, and inside shared embed codes.', 'wp3d-models'),
					'choices' => array (
						'stock' => __('No WP3D Intro', 'wp3d-models'),
						'intro-branded' => __('WP3D Intro - Play Button with your logo & statement', 'wp3d-models'),
						'intro-cobranded' => __('WP3D Intro - Play Button with your logo, statement, & Matterport co-branding', 'wp3d-models'),
						'intro-presented' => __('WP3D Intro - Play Button with Matterport "Presented By" text', 'wp3d-models'),
						'intro-unbranded' => __('WP3D Intro - Play Button - no branding', 'wp3d-models'),
					),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_591886969aaf1',
					'label' => __('Branded Intro', 'wp3d-models'),
					'name' => 'showcase_branding',
					'type' => 'select',
					'instructions' => __('Choose how you want your interactive content to appear, by default, on single (unskinned) pages, in "fullscreen" mode, and inside shared embed codes.', 'wp3d-models'),
					'choices' => array (
						'stock' => __('No WP3D Intro', 'wp3d-models'),
						'intro-branded' => __('WP3D Intro - Play Button with your logo & statement', 'wp3d-models'),
						'intro-unbranded' => __('WP3D Intro - Play Button - no branding', 'wp3d-models'),
					),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'matterport',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),				
				array (
					'key' => 'field_570c1c936509c',
					'label' => __('Preload Model', 'wp3d-models'),
					'name' => 'preload_model',
					'type' => 'true_false',
					'instructions' => __('
					<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="preloadhelp" alt="DESKTOP ONLY?! WHY?  We set it up this way largely because of bandwidth & performance considerations for mobile users." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">DESKTOP ONLY! Preload this Model for immediate playback once the "Intro" has been clicked. <b>Disabled on Models with "Video" Bases</b>.</span>
					', 'wp3d-models'),				
					'message' => __('Preload this Model', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-unbranded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-presented',
							),						
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-cobranded',
							),
							// Non-Matterport
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-unbranded',
							),
						),
						'allorany' => 'any',
					),
					'default_value' => 0,
				),			
				array (
					'key' => 'field_5622730a302ff',
					'label' => __('Custom Statement', 'wp3d-models'),
					'name' => 'custom_showcase_statement',
					'type' => 'text',
					'instructions' => __('Add an optional statement text to "standard", fullscreen" and "skinned" versions of your Model.', 'wp3d-models'),				
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-cobranded',
							),
							// Non-Matterport
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-branded',
							),
						),
						'allorany' => 'any',
					),
					'default_value' => '',
					'placeholder' => __('Replace the default "START TOUR" statement text', 'wp3d-models'),
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '60',
				),	
				array (
					'key' => 'field_562fc6e4ec357',
					'label' => __('Logo Overlay', 'wp3d-models'),
					'name' => 'logo_overlay',
					'type' => 'true_false',
					'instructions' => __('Add a logo overlay to "standard", fullscreen" and "skinned" views.<br>Looks for a small \'Override Logo\', then to \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Branding -> Small Logo</a>\', unless force overridden below.', 'wp3d-models'),
					'message' => __('Add Showcase Logo Overlay ', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-cobranded',
							),
							// Non-Matterport
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-branded',
							),
						),
						'allorany' => 'any',
					),
					'default_value' => 0,
				),
				
				
				array (
					'key' => 'field_589297cadca7c',
					'label' => __('Force "Skinned" Default Logo Overlay', 'wp3d-models'),
					'name' => 'force_default_logo_overlay',
					'type' => 'true_false',
					'instructions' => __('On the "Skinned" view, force use the Small Logo overlay from <a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Branding -> Small Logo</a>', 'wp3d-models'),
					'message' => __('On the "Skinned" view, force use the Settings Small Logo overlay ', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_562fc6e4ec357',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => 0,
				),				
				array (
					'key' => 'field_572a38ca826d2',
					'label' => __('Play Button ONLY', 'wp3d-models'),
					'name' => 'play_button_only',
					'type' => 'true_false',
					'instructions' => __('
	<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="playbuttononlyhelp" alt="This option can be used if you have a unique embedding situation with limited (vertical) space. This is a bit of a fringe situation, but this should make some of you CHEER!" data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">Show only the PLAY BUTTON icon in Fullscreen & Embedded views.</span>				
					', 'wp3d-models'),				
					'message' => __('Fullscreen PLAY BUTTON Icon ONLY', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-unbranded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-presented',
							),						
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-cobranded',
							),
							// Non-Matterport
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-unbranded',
							),							
						),
						'allorany' => 'any',
					),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_57e832b07c9e3',
					'label' => __('Custom Copyright Text', 'wp3d-models'),
					'name' => 'custom_copyright',
					'type' => 'text',
					'instructions' => __('Customize the Copyright Text that appears on FULLSCREEN views.', 'wp3d-models'),				
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-cobranded',
							),
							// Non-Matterport
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-branded',
							),
						),
						'allorany' => 'any',
					),
					'default_value' => '',
					'placeholder' => __('Custom Copyright Text', 'wp3d-models'),
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '60',
				),			
				
				array (
					'key' => 'field_57e8326903072',
					'label' => __('Fullscreen Header Disable', 'wp3d-models'),
					'name' => 'disable_header_bar',
					'type' => 'true_false',
					'instructions' => __('Choose this option if you want to entirely disable/hide the (black) FULLSCREEN Header Bar.', 'wp3d-models'),				
					'message' => __('Fullscreen Header Disable', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-unbranded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-presented',
							),						
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_55fae055e188f',
								'operator' => '==',
								'value' => 'intro-cobranded',
							),	
							// Non-Matterport
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-branded',
							),
							array (
								'field' => 'field_591886969aaf1',
								'operator' => '==',
								'value' => 'intro-unbranded',
							),	
						),
						'allorany' => 'any',
					),
					'default_value' => 0,
				),				
				
				array (
					'key' => 'field_55fae02ee188e',
					'label' => __('Branding', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),				
				
				array (
					'key' => 'field_5600db6173dac',
					'label' => __('Override Logos', 'wp3d-models'),
					'name' => 'add_override_logos',
					'type' => 'true_false',
					'message' => __('Add override logos for this Model?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_5600d9c173daa',
					'label' => __('Large Logo Override', 'wp3d-models'),
					'name' => 'large_logo_override',
					'type' => 'image',
					'instructions' => __('If added, this logo will override the "Large" logo uploaded to \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Branding -> Large Logo</a>\', for this Model only.<br><b>Logo should be a transparent (32-bit) PNG and visible on a dark background. It should be sized at exactly 400px wide by 400px tall (square).</b>', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600db6173dac',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'all',
				),
				array (
					'key' => 'field_562dd55a0aab1',
					'label' => __('Small Logo Override', 'wp3d-models'),
					'name' => 'small_logo_override',
					'type' => 'image',
					'instructions' => __('If added, this logo will override the "Small" logo uploaded to \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">Settings -> Branding -> Large Logo</a>\', for this Model only.<br><b>Logo should be a transparent (32-bit) PNG and visible on a dark background. It should be sized at exactly 300px wide by 120px tall (landscape).</b>', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600db6173dac',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'all',
				),
				
				// override skinned URL
				array (
					'key' => 'field_597971f5a1d67',
					'label' => __('Skinned Logo Link Override', 'wp3d-models'),
					'name' => 'logo_link_override',
					'type' => 'text',
					'instructions' => __('If entered, the "Skinned" logo will become a link to this address.', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600db6173dac',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'http(s)://',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),				
				
				
				// Matterport
				array (
					'key' => 'field_5600deee5e803',
					'label' => __('Contact/Agent Information', 'wp3d-models'),
					'name' => 'model_contact_information',
					'instructions' => 'To create and associate Agents to Models (and offer scheduling as well), upgrade to the pro license for WP3D Models.<br><br><a target="_blank" href="https://wp3dmodels.com/buy-now" class="wp3d_upgrade_button">Upgrade to pro</a>',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_55fad8676fc57',
					'label' => __('Media', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),
				array (
					'key' => 'field_5600ef46aece1',
					'label' => __('Model Image Override', 'wp3d-models'),
					'name' => 'add_model_image_override',
					'type' => 'true_false',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="modelimageoverridehelp" alt="Replace the auto-generated Matterport image with your own!  This is especially valuable for providing exterior or separately captured (DSLR) imagery." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is a "Model Image Override"?</span><br><i>Uploaded image will be auto-cropped to 1080px tall by 1920px wide.</i>', 'wp3d-models'),				
					'message' => __('Override the default Model Image?', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'static_image',
							),
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'video',
							),
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'generic',
							),		
						),
						'allorany' => 'all',
					),					
					'default_value' => 0,
				),
				array (
					'key' => 'field_5504bb473d6f2',
					'label' => __('Model Image Override', 'wp3d-models'),
					'name' => 'image_override',
					'type' => 'image',
					'instructions' => __('<i>The image should be optimized and (at least) 1080px tall and 1920px wide.</i><br><b>NOTE:</b> This field is OPTIONAL and is here to make it possible to override the automatically generated (default) Matterport Model image.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600ef46aece1',
								'operator' => '==',
								'value' => '1',
							),
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'static_image',
							),
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'video',
							),	
							array (
								'field' => 'field_587557a304734',
								'operator' => '!=',
								'value' => 'generic',
							),	
						),
						'allorany' => 'all',
					),
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'uploadedTo',
				),
				array (
					'key' => 'field_570fc4dcb7d8c',
					'label' => __('Video Background', 'wp3d-models'),
					'name' => 'add_model_video_background',
					'type' => 'true_false',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="bgvideohelp" alt="Present a looping video (no audio or controls) as the header background for your Single Propery (\'Skinned\') Views!<br><br>NOTE: This video will not play on mobile devices." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is a "Video Background"?</span>', 'wp3d-models'),				
					'message' => __('Add a Video Background?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_570fc56989f23',
					'label' => __('YouTube Video Background Link', 'wp3d-models'),
					'name' => 'youtube_video_background_link',
					'type' => 'text',
					'instructions' => __('SKINNED VIEW ONLY - Enter the FULL YouTube video URL here. Copy this directly from the browser window, do not use the short version, or any YouTube-provided embed code.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_570fc4dcb7d8c',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://www.youtube.com/watch?v=XXXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),			
				array (
					'key' => 'field_5600ee3b9d43a',
					'label' => __('Photo Gallery', 'wp3d-models'),
					'name' => 'add_gallery',
					'type' => 'true_false',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="photogalleryhelp" alt="Include a gallery of your own still images, or use those captured from the Matterport Workshop." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is a "Photo Gallery"?</span>', 'wp3d-models'),				
					'message' => __('Add a photo gallery?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_5866e2d905958',
					'label' => __('Gallery Type', 'wp3d-models'),
					'name' => 'gallery_type',
					'type' => 'select',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="photogalleryselect" alt="Choose from two different styles of photo gallery. Note that the \'Zoom Slider\' option offers better support for images that have a PORTRAIT orientation." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is a "Gallery Type"?</span>', 'wp3d-models'),				
					'choices' => array (
						'zoom_slider' => 'Zoom Slider',					
						'standard_slider' => 'Standard Slider'
					),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600ee3b9d43a',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => $default_gallery_type,
					'allow_null' => 0,
					'multiple' => 0,
				),			
				array (
					'key' => 'field_5600baeabc761',
					'label' => __('Photo Gallery', 'wp3d-models'),
					'name' => 'photo_gallery',
					'type' => 'gallery',
					'instructions' => __('Use the "Add Image" button below to begin uploading images.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600ee3b9d43a',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'preview_size' => 'thumbnail',
					'library' => 'uploadedTo',
				),
				array (
					'key' => 'field_5600f38ea014c',
					'label' => __('Content Video', 'wp3d-models'),
					'name' => 'add_video',
					'type' => 'select',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="contentvideohelp" alt="Display a YouTube or Vimeo video alongside the rest of your supplemental property content." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is "Content Video"?</span>', 'wp3d-models'),				
					'choices' => array (
						'youtube' => 'YouTube',
						'vimeo' => 'Vimeo',
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5600bb3abc762',
					'label' => __('YouTube BRANDED Video Link', 'wp3d-models'),
					'name' => 'youtube_video_link',
					'type' => 'text',
					'instructions' => __('Enter the full BRANDED YouTube video URL here. Copy this directly from the browser window, do not use the short version, or any YouTube-provided embed code.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f38ea014c',
								'operator' => '==',
								'value' => 'youtube',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://www.youtube.com/watch?v=XXXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_57196650963c6',
					'label' => __('YouTube UNBRANDED Video Link', 'wp3d-models'),
					'name' => 'youtube_unbranded_video_link',
					'type' => 'text',
					'instructions' => __('OPTIONAL: Enter your separate UNBRANDED (MLS-Compliant) YouTube video URL here.', 'wp3d-models'),
					//'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f38ea014c',
								'operator' => '==',
								'value' => 'youtube',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://www.youtube.com/watch?v=XXXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),			
				array (
					'key' => 'field_5600f368a014b',
					'label' => __('Vimeo BRANDED Video Link', 'wp3d-models'),
					'name' => 'vimeo_video_link',
					'type' => 'text',
					'instructions' => __('Enter the full BRANDED Vimeo video URL here. Copy this directly from the browser window, do not use the short version, or any Vimeo-provided embed code.', 'wp3d-models'),
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f38ea014c',
								'operator' => '==',
								'value' => 'vimeo',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://vimeo.com/XXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_5719668115428',
					'label' => __('Vimeo UNBRANDED Video Link', 'wp3d-models'),
					'name' => 'vimeo_unbranded_video_link',
					'type' => 'text',
					'instructions' => __('OPTIONAL: Enter your separate UNBRANDED (MLS-Compliant) Vimeo video URL here.', 'wp3d-models'),
					//'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f38ea014c',
								'operator' => '==',
								'value' => 'vimeo',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://vimeo.com/XXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),			
				array (
					'key' => 'field_5600ab014fa03',
					'label' => __('Floorplan Images', 'wp3d-models'),
					'name' => 'add_floorplan',
					'type' => 'true_false',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="floorplanhelp" alt="Upload 2D Floorplan images (with dimensions) that can be used to supplement the other information you provide about this property." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What are "Floorplan Images"?</span>', 'wp3d-models'),				
					'message' => __('Add floorplan images?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_5600edfb9d439',
					'label' => __('Floorplan Gallery', 'wp3d-models'),
					'name' => 'floorplan_gallery',
					'type' => 'gallery',
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600ab014fa03',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'preview_size' => 'thumbnail',
					'library' => 'uploadedTo',
				),				
				array (
					'key' => 'field_560c53d4d82c6',
					'label' => '3rd Party Content',
					'name' => 'add_smart_gallery',
					'type' => 'true_false',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="thirdpartyhelp" alt="Here at WP3D Models, we\'re impressed with various complementary solutions that are available via 3rd party providers.<br><br><a href=\'http://wp3dmodels.com/3rd-party-content/\' target=\'_blank\'>MORE INFO ON 3RD PARTY CONTENT &raquo;</a>" data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">What is "3rd Party Content"?</span>', 'wp3d-models'),	
					'message' => 'Add 3rd Party Content?',
					'default_value' => 0,
				),
				array (
					'key' => 'field_560c5410d82c7',
					'label' => '3rd Party Content URL',
					'name' => 'smart_gallery_url',
					'type' => 'text',
					'instructions' => __('Enter the provided link/URL to (embeddable) 3rd Party content.', 'wp3d-models'),				//'instructions' => 'Enter the provided link/URL to your UNREALER "Smart Gallery".',
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_560c53d4d82c6',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => 'https://www.3rdpartycontenturl.com/XXXXXXXXXXX',
					'prepend' => '<span class="dashicons dashicons-admin-site"></span>',				
					'maxlength' => '',
					'rows' => 4,
					'formatting' => 'none',
				),	
				array (
					'key' => 'field_55fb9cbe295d2',
					'label' => __('Map', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),
				
				array (
					'key' => 'field_5600ea80d3360',
					'label' => __('Form', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),
				array (
					'key' => 'field_5600f6ddcd8bd',
					'label' => __('Add Form', 'wp3d-models'),
					'name' => 'add_form',
					'type' => 'true_false',
					'message' => __('Add lead generation form to the "skinned" view?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_591892b4b89bf',
					'label' => __('Lead Generation Form Type', 'wp3d-models'),
					'name' => 'lead_generation_form_type',
					'type' => 'select',
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f6ddcd8bd',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'choices' => $lead_generation_form_conditional_array,
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5600f79634a31',
					'label' => __('Send To Email Address', 'wp3d-models'),
					'name' => 'default_send_to_email_address',
					'type' => 'email',
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f6ddcd8bd',
								'operator' => '==',
								'value' => '1',
							),
							array (
								'field' => 'field_591892b4b89bf',
								'operator' => '==',
								'value' => 'default-custom',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array (
					'key' => 'field_5600f7d099f4c',
					'label' => __('Form Shortcode', 'wp3d-models'),
					'name' => 'form_shortcode',
					'type' => 'text',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="shortcodeformhelp" alt="Out of the box support for GRAVITY FORMS.<br><br>Contact Form 7 Support <a href=\'https://wordpress.org/plugins/bootstrap-for-contact-form-7/\' target=\'_blank\'>via plugin</a>.<br><br>Ninja Forms Support <a href=\'https://github.com/bostondv/bootstrap-ninja-forms\' target=\'_blank\'>via plugin</a>." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">Click (?) for specific 3rd party form (shortcode) support information.</span>', 'wp3d-models'),				
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_5600f6ddcd8bd',
								'operator' => '==',
								'value' => '1',
							),
							array (
								'field' => 'field_591892b4b89bf',
								'operator' => '==',
								'value' => 'shortcode',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				// TST OPTIONS
				array (
					'key' => 'field_5917b39e28847',
					'label' => __('Opt', 'wp3d-models'),
					'name' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'threesixtytours',
							),
						),
						'allorany' => 'all',
					),					
					'type' => 'tab',
				),
				array (
					'key' => 'field_5917b756cf767',
					'label' => __('Header Content', 'wp3d-models'),
					'name' => 'tst_header',
					'type' => 'select',
					'instructions' => __('How should the Header content be treated?', 'wp3d-models'),
					'choices' => array (
						'transparent' => __('Transparent (Default)', 'wp3d-models'),
						'true' => __('On', 'wp3d-models'),
						'false' => __('Off', 'wp3d-models'),
						'closed' => __('Closed', 'wp3d-models'),
						'delayclose' => __('Closed Delay', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5917b42181882',
					'label' => __('Footer Content', 'wp3d-models'),
					'name' => 'tst_footer',
					'type' => 'select',
					'instructions' => __('How should the Footer be treated?', 'wp3d-models'),
					'choices' => array (
						'true' => __('On (Default)', 'wp3d-models'),
						'false' => __('Remove Footer Content', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),					
				array (
					'key' => 'field_596ffde51b965',
					'label' => __('Title', 'wp3d-models'),
					'name' => 'tst_title',
					'type' => 'select',
					'instructions' => __('How should the Title Content be treated?', 'wp3d-models'),
					'choices' => array (
						'false' => __('Remove Title Content (Default)', 'wp3d-models'),
						'true' => __('On', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5917b77386ff1',
					'label' => __('Tour Navigation', 'wp3d-models'),
					'name' => 'tst_tournav',
					'type' => 'select',
					'instructions' => __('For "Tours", how should the lower Navigation be treated?', 'wp3d-models'),
					'choices' => array (
						'delayclose' => __('Delay Closed (Default)', 'wp3d-models'),
						'true' => __('On ', 'wp3d-models'),
						'closed' => __('Closed', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_596ce44095bc8',
					'label' => __('Mousewheel / Trackpad Zoom', 'wp3d-models'),
					'name' => 'tst_mousewheel',
					'type' => 'select',
					'instructions' => __('Set Mousewheel & Trackpad Zoom Functionality', 'wp3d-models'),
					'choices' => array (
						'false' => __('Disabled', 'wp3d-models'),
						'true' => __('On (Default)', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_596ce44eb51ed',
					'label' => __('Social Sharing', 'wp3d-models'),
					'name' => 'tst_socialshare',
					'type' => 'select',
					'instructions' => __('Set TST Social Sharing Functionality', 'wp3d-models'),
					'choices' => array (
						'false' => __('Removed (Default)', 'wp3d-models'),
						'true' => __('On', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_596ce45c4a2bb',
					'label' => __('Branding', 'wp3d-models'),
					'name' => 'tst_branding',
					'type' => 'select',
					'instructions' => __('Set TST Branding Functionality. (Always removed on "nobrand" views)', 'wp3d-models'),
					'choices' => array (
						'false' => __('Removed (Default)', 'wp3d-models'),
						'true' => __('On', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),				
				array (
					'key' => 'field_59736d0282245',
					'label' => __('Start Screen', 'wp3d-models'),
					'name' => 'tst_startscreen',
					'type' => 'select',
					'instructions' => __('TST Start Screen Functionality. (Only applies to non-"Introed" views)', 'wp3d-models'),
					'choices' => array (
						'false' => __('Off (Default)', 'wp3d-models'),
						'true' => __('On', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),	
			

				// MATTERPORT OPTIONS
				array (
					'key' => 'field_57128e49f1bc6',
					'label' => __('Opt', 'wp3d-models'),
					'name' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),
						),
						'allorany' => 'all',
					),					
					'type' => 'tab',
				),
				array (
					'key' => 'field_5636c58961bae',
					'label' => __('Showcase Branding & MP Sharing', 'wp3d-models'),
					'name' => 'showcase_no_branding',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),				
					'message' => __('Remove Showcase Branding & MP Sharing', 'wp3d-models'),
					'default_value' => 0,
				),	
				array (
					'key' => 'field_58717be640a7d',
					'label' => __('Showcase Branding, Links, MP Sharing &amp; VR', 'wp3d-models'),
					'name' => 'showcase_no_branding_links',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Display Showcase Branding, Mattertag Links, MP Sharing, VR & About Panel (Default)', 'wp3d-models'),
						'1' => __('Remove Showcase Branding, Mattertag Links, MP Sharing & VR', 'wp3d-models'),
						'2' => __('Remove Showcase Title, Branding, Mattertag Links, MP Sharing, VR & About Panel', 'wp3d-models'),
					),
					'default_value' => '0',
					'allow_null' => 0,
					'multiple' => 0,					
				),	
				array (
					'key' => 'field_55521e1ba8ea0',
					'label' => __('Showcase Autoplay', 'wp3d-models'),
					'name' => 'model_autoplay',
					'type' => 'true_false',
					'instructions' => __('"STANDARD" & "NO BRAND" views w/o "Intro"', 'wp3d-models'),				
					'message' => __('Autoplay', 'wp3d-models'),
					'default_value' => 0,
				),
				
				
				array (
					'key' => 'field_59619c9a94213',
					'label' => __('Title and About Panel', 'wp3d-models'),
					'name' => 'showcase_title_panel',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Remove Showcase Title & About Panel', 'wp3d-models'),
						'1' => __('Show Title & About Panel (Default)', 'wp3d-models'),
						'2' => __('Remove Showcase Title (About Panel remains)', 'wp3d-models'),
					),
					'default_value' => '1',
					'allow_null' => 0,
					'multiple' => 0,					
				),					
				
				
				array (
					'key' => 'field_560c736400fbc',
					'label' => __('Field Of View (FOV)', 'wp3d-models'),
					'name' => 'widened_field_of_view',
					'type' => 'true_false',
					'instructions' => __('"STANDARD" & "NO BRAND" views w/o "Intro"', 'wp3d-models'),
					'message' => __('Wider FOV', 'wp3d-models'),
					'default_value' => 0,
				),	
				
				array (
					'key' => 'field_59619da96b541',
					'label' => __('Tour End Call to Action', 'wp3d-models'),
					'name' => 'showcase_tour_cta',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Remove Call to Action', 'wp3d-models'),
						'1' => __('Large Call to Action (Default)', 'wp3d-models'),
						'2' => __('Small Call to Action', 'wp3d-models'),
					),
					'default_value' => '1',
					'allow_null' => 0,
					'multiple' => 0,					
				),					
				
				
				array (
					'key' => 'field_55ac45592f5da',
					'label' => __('Multifloor', 'wp3d-models'),
					'name' => 'model_hide_multifloor',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),				
					'message' => __('Hide Multifloor', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_55fad8dc6fc59',
					'label' => __('Help Tools', 'wp3d-models'),
					'name' => 'model_force_help',
					'instructions' => __('ALL views', 'wp3d-models'),				
					'message' => __('Force "Help"', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Do not show "Help" content', 'wp3d-models'),
						'1' => __('Force show "Help" content (verbose)', 'wp3d-models'),
						'2' => __('Force show "Help" content (concise)', 'wp3d-models'),
						'' => __('Show "Help" content for new users (Default)', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_571287c33c674',
					'label' => __('Tour Panning', 'wp3d-models'),
					'name' => 'disable_model_tour_panning',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),				
					'message' => __('Disable Tour Panning', 'wp3d-models'),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_59c97a4799d50',
					'label' => __('Dollhouse View', 'wp3d-models'),
					'name' => 'dollhouse_view',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Hide Dollhouse View & fly-in', 'wp3d-models'),
						'1' => __('Show Dollhouse View & fly-in (Default)', 'wp3d-models'),
					),
					'default_value' => '1',
					'allow_null' => 0,
					'multiple' => 0,					
				),					
				
				array (
					'key' => 'field_571287d631746',
					'label' => __('Tour Loop', 'wp3d-models'),
					'name' => 'enable_model_tour_loop',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),				
					'message' => __('Enable Tour Loop', 'wp3d-models'),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_59c97ae8342f2',
					'label' => __('Mattertag Content', 'wp3d-models'),
					'name' => 'mattertag_content',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Hide Mattertag Content', 'wp3d-models'),
						'1' => __('Show Mattertag Content (Default)', 'wp3d-models'),
					),
					'default_value' => '1',
					'allow_null' => 0,
					'multiple' => 0,					
				),	
				
				
				array (
					'key' => 'field_571287e5edf2c',
					'label' => __('Tour Path', 'wp3d-models'),
					'name' => 'disable_model_tour_path',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Disable Tour Blue Path', 'wp3d-models'),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_571287f87c1af',
					'label' => __('Highlight Reel', 'wp3d-models'),
					'name' => 'force_show_highlight_reel',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Briefly show, then hide the Reel', 'wp3d-models'),
						'1' => __('Keep the Reel visible', 'wp3d-models'),
						'2' => __('Hide the Reel (if there are no 360 Views)', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,					
					
				),
			
				
				array (
					'key' => 'field_58717b6216a40',
					'label' => __('Disable Model VR', 'wp3d-models'),
					'name' => 'disable_model_vr',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Disable Showcase VR link', 'wp3d-models'),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_59c97b95e5359',
					'label' => __('VR Limited Mode', 'wp3d-models'),
					'name' => 'vr_limited_mode',
					'instructions' => __('ALL views', 'wp3d-models'),
					'type' => 'select',
					'choices' => array (
						'0' => __('Open VR space in the "Shared with Me" folder within the app. (Default)', 'wp3d-models'),
						'1' => __('Open VR space in a limited mode by itself.', 'wp3d-models'),
					),
					'default_value' => 0,
					'allow_null' => 0,
					'multiple' => 0,					
				),
				
				
				array (
					'key' => 'field_572a87f1cd1d3',
					'label' => __('Disable Mousewheel', 'wp3d-models'),
					'name' => 'disable_space_scroll',
					'type' => 'true_false',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="thirdpartyhelp" alt="\'Disable Actions\' has several major affects on the Showcase experience. With this checked, there is no navigation via a mousewheel in inside view and no dollhouse/floorplan zooming.  It should be used only in very special cases." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">ALL views</span>', 'wp3d-models'),
					'message' => __('Disable Showcase mousewheel/swipe', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_58717d53ec3d1',
					'label' => __('Quickstart Showcase', 'wp3d-models'),
					'name' => 'enable_model_quickstart',
					'type' => 'true_false',
					'instructions' => __('"SKINNED" & views with WP3D "Intro"', 'wp3d-models'),
					'message' => __('Quickstart ("Help" is disabled)', 'wp3d-models'),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_57128802617c9',
					'label' => __('Guided Tour Autostart', 'wp3d-models'),
					'name' => 'autostart_guided_model_tour',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Autostart Tour ("Help" is disabled)', 'wp3d-models'),
					'default_value' => 0,
				),
				







				
	
				
				array (
					'key' => 'field_58744f2ceddc4',
					'label' => __('3D Mesh Transition', 'wp3d-models'),
					'name' => 'model_guided_tour_transition',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Use 3D Mesh <a href="https://support.matterport.com/hc/en-us/articles/212625138-3-Choose-Tour-Type-Publish" target="_blank">tour transition</a>', 'wp3d-models'),
					'default_value' => 0,
				),	
					
				array (
					'key' => 'field_587448fec09f7',
					'label' => __('Autostart Delay', 'wp3d-models'),
					'name' => 'model_guided_tour_seconds',
					'type' => 'number',
					'instructions' => __('Autostart Tour Delay (seconds)', 'wp3d-models'),
					'default_value' => '',
					'placeholder' => $default_guided_tour_seconds,
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),	
				array (
					'key' => 'field_587181e7e28f7',
					'label' => __('Showcase Highlight Time', 'wp3d-models'),
					'name' => 'showcase_highlight_time',
					'type' => 'number',
					'instructions' => __('Tour highlight time (milliseconds)', 'wp3d-models'),
					'default_value' => '',
					'placeholder' => $default_showcase_highlight_time,
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),	
				
				array (
					'key' => 'field_58744f3a7fbde',
					'label' => __('Model Zoom', 'wp3d-models'),
					'name' => 'model_zoom',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Disable Model Zoom', 'wp3d-models'),
					'default_value' => 0,
				),
				
				array (
					'key' => 'field_5eae017f3dd97',
					'label' => __('Model Pin', 'wp3d-models'),
					'name' => 'model_pin',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Hide Dollhouse/Floorplan 360 Views', 'wp3d-models'),
					'default_value' => 0,
				),	
				
				array (
					'key' => 'field_5eae01873dd98',
					'label' => __('Model Portal', 'wp3d-models'),
					'name' => 'model_portal',
					'type' => 'true_false',
					'instructions' => __('ALL views', 'wp3d-models'),
					'message' => __('Hide Inside View 360 Views', 'wp3d-models'),
					'default_value' => 0,
				),						
				
				array (
					'key' => 'field_58717ea2d4afc',
					'label' => __('Showcase Language', 'wp3d-models'),
					'name' => 'mp_model_lang',
					'type' => 'select',
					'instructions' => __('Select (Model-specific) Language for Showcase text labels.', 'wp3d-models'),
					'choices' => array (
						'en' => __('English (Default)', 'wp3d-models'),
						'es' => __('Spanish', 'wp3d-models'),
						'fr' => __('French', 'wp3d-models'),
						'de' => __('German', 'wp3d-models'),
						'ru' => __('Russian', 'wp3d-models'),
						'cn' => __('Chinese', 'wp3d-models'),
						'jp' => __('Japanese', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				
				array (
					'key' => 'field_5b26a06dbc0eb',
					'label' => __('Alternate Showcase CDN', 'wp3d-models'),
					'name' => 'mp_model_alternate_cdn',
					'type' => 'select',
					'instructions' => __('Select Alternate Matterport CDN', 'wp3d-models'),
					'choices' => array (
						//'standard' => __('Standard', 'wp3d-models'),
						'cn' => __('Chinese', 'wp3d-models'),
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),				
				
				
				
				array (
					'key' => 'field_557845a8646f8',
					'label' => __('Admin', 'wp3d-models'),
					'name' => '',
					'type' => 'tab',
				),
				array (
					'key' => 'field_58e910c31807a',
					'label' => __('Listing ID', 'wp3d-models'),
					'name' => 'listing_id',
					'type' => 'text',
					'instructions' => __('Enter the Listing ID for this Model. <b>By default, this data is unpublished. It is included here for custom add-on (syndication) opportunities.</b>', 'wp3d-models'),
					'default_value' => '',
					'maxlength' => '',
				),
				array (
					'key' => 'field_557845b7646f9',
					'label' => __('Admin Notes', 'wp3d-models'),
					'name' => 'admin_notes',
					'type' => 'textarea',
					'instructions' => __('Add any administrative and NON-PUBLIC notes you might want to associate with this Model', 'wp3d-models'),
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '',
					'formatting' => 'br',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'model',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'acf_after_title',
				'layout' => 'no_box',
				'hide_on_screen' => array (
					0 => 'excerpt',
					1 => 'discussion',
					2 => 'comments',				
				),
			),
			'menu_order' => 0,
			
		));
		
	// *************  MODEL SIDEBAR TEMPLATE *************//
	
	// This is a less-than-ideal solution...but offers a quick fix for ACF 5 where conditional logic does not appear to be able to be shared across field groups.
	$wp3d_sidebar_matterport_conditional = array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'matterport',
							),
						),
						'allorany' => 'all',
					);
					
		$wp3d_sidebar_tst_conditional = array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_587557a304734',
								'operator' => '==',
								'value' => 'threesixtytours',
							),
						),
						'allorany' => 'all',
					);
		
	// basically, we check for version 5 (greater than 4) and then void the conditional logic for the one we want to see.
	// this might need to be simplified in later versions
	if ($wp3d_acf_version > 4) { 
		if ($wp3d_type_info['model_type'] == 'matterport') {
			$wp3d_sidebar_matterport_conditional = 0;
		} else if ($wp3d_type_info['model_type'] == 'threesixtytours') {
			$wp3d_sidebar_tst_conditional = 0;
		}
	} 
	
	// DEBUG
	//print_r($wp3d_sidebar_tst_conditional); exit;
	
	
	register_field_group(array (
			'id' => 'acf_matterport-api-data',
			'title' => __('Model Visibility & API Data', 'wp3d-models'),
			'key' => 'group_58b05a27accd3',
			'fields' => array (
				array (
					'key' => 'field_572a8c97e18ff',
					'label' => __('Exclude from List Views', 'wp3d-models'),
					'name' => 'model_list_exclude',
					'type' => 'true_false',
					'instructions' => __('Checking this box will prevent this Model from appearing on any "list" (shortcode-generated) pages on this site.', 'wp3d-models'),
					'message' => 'Hide Model on all "list" pages.',
					'default_value' => 0,
				),
				array (
					'key' => 'field_5632071a407fe',
					'label' => __('Matterport API Data', 'wp3d-models'),
					'name' => 'retrieve_mp_data',
					'type' => 'true_false',
					'instructions' => __('Your Showcase\'s "Public Details" will be gathered up and saved locally upon initial save of this Model. If that information is later updated, select this box to force re-retrieve this information upon \'Update\'.', 'wp3d-models'),
					'conditional_logic' => $wp3d_sidebar_matterport_conditional,
					'message' => 'Re-retrieve Matterport Data',
					'default_value' => 1,
				),
				// HIDING FOR NOW (4/12/20)
				// array (
				// 	'key' => 'field_591d396b47730',
				// 	'label' => __('ThreeSixty Tours API Data', 'wp3d-models'),
				// 	'name' => 'retrieve_tst_data',
				// 	'type' => 'true_false',
				// 	'instructions' => __('Your Tour\'s Public Info will be gathered up and saved locally upon initial save of this Model. If that information is later updated, select this box to force re-retrieve this information upon \'Update\'.', 'wp3d-models'),
				// 	'conditional_logic' => $wp3d_sidebar_tst_conditional,
				// 	'message' => 'Re-retrieve ThreeSixty Tours Data',
				// 	'default_value' => 1,
				// ),				
				array (
					'key' => 'field_5797031770cab',
					'label' => __('Social Share Image', 'wp3d-models'),
					'name' => 'generate_social_image',
					'type' => 'true_false',
					'instructions' => __('If enabled in your <a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=social" target="_blank">"Social" settings</a>, a custom share image is auto-generated when you first save this Model.  In the future, you can use this setting to force regenerate this image (upon Model update).', 'wp3d-models'),
					'message' => 'Regenerate Social Image',
					'default_value' => 0,
				),			
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'model',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		));	
	
	// ################## END MODEL ##################
	
	
	// ################## BEGIN AGENT  ##################	
	
		register_field_group(array (
			'id' => 'acf_agent-template',
			'title' => __('Agent Template', 'wp3d-models'),
			'key' => 'group_58b05a49902b9',
			'fields' => array (
				array (
					'key' => 'field_560c0ad48e2e3',
					'label' => __('Agent Image', 'wp3d-models'),
					'name' => 'agent_image',
					'type' => 'image',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="agentphotohelp" alt="If you don\'t have a 300px SQUARE image, consider using an online cropping tool to help you.<br><br><a href=\'https://croppola.com/\' target=\'_blank\'>CROPPOLA.COM ONLINE CROP TOOL &raquo;</a>" data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">Upload a SQUARE 300px (optimized!) agent image. <i>WP3D Models auto-applies the "circle" styling.</i></span>', 'wp3d-models'),
					//'instructions' => __('Upload a SQUARE 300px (optimized!) agent image.', 'wp3d-models'),				
					'required' => 1,
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'all',
				),
				array (
					'key' => 'field_560c0aff8e2e5',
					'label' => __('Agent Subheading', 'wp3d-models'),
					'name' => 'agent_subheading',
					'type' => 'text',
					'instructions' => __('This optional field can be used to provide brief Agent info, certifications, marketing slogans, accolades, etc.', 'wp3d-models'),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),
				array (
					'key' => 'field_560c0b268e2e6',
					'label' => __('Agent Info', 'wp3d-models'),
					'name' => 'agent_info',
					'type' => 'repeater',
					'instructions' => __('<img src="'.plugins_url().'/wp3d-models-free/assets/images/question-mark.png" class="alignleft wp3d-help" id="agentdetailshelp" alt="If you don\'t like the labels of \'Mobile\', \'Direct\' or \'Office\' for the phone numbers (or any of the other labels, for that matter), these can be customized via a simple WordPress hook.  Let us know if you need help." data-hasqtip="0" aria-describedby="qtip-0"> <span class="wp3d-help-text">At a minimum, an agent must include an email and one phone number.  If a 2nd or 3rd phone number is included, the primary number is assumed to be the agent\'s \'Mobile\' number and that label will be applied. Please enter non-US phone numbers in international format, starting with "+".</span>', 'wp3d-models'),
					'required' => 1,
					'sub_fields' => array (
						array (
							'key' => 'field_560c0b308e2e7',
							'label' => __('Email', 'wp3d-models'),
							'name' => 'email',
							'type' => 'email',
							'required' => 1,
							'column_width' => 25,
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '<span class="dashicons dashicons-email-alt"></span>',
							'append' => '',
						),
						array (
							'key' => 'field_560c0b4f8e2e8',
							'label' => __('Phone', 'wp3d-models'),
							'name' => 'phone_mobile',
							'type' => 'text',
							'required' => 1,
							'column_width' => 25,
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '<span class="dashicons dashicons-phone"></span>',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => '',
						),
						array (
							'key' => 'field_56229ba5b75ec',
							'label' => __('Phone (direct)', 'wp3d-models'),
							'name' => 'phone_direct',
							'type' => 'text',
							'column_width' => 25,
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '<span class="dashicons dashicons-phone"></span>',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => '',
						),
						array (
							'key' => 'field_560c0b678e2e9',
							'label' => __('Phone (office)', 'wp3d-models'),
							'name' => 'phone_office',
							'type' => 'text',
							'column_width' => 25,
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '<span class="dashicons dashicons-phone"></span>',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => '',
						),
					),
					'row_min' => 1,
					'row_limit' => 1,
					'layout' => 'table',
					'button_label' => 'Add Info',
				),
				array (
					'key' => 'field_58921c8df1001',
					'label' => __('Schedule Sessions with Calendly', 'wp3d-models'),
					'name' => 'calendly_enabled',
					'type' => 'true_false',
					'message' => __('Offer Calendar Times to Meet with Website Visitors?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_58921c8df1002',
					'name' => 'calendly_type',
					'type' => 'radio',
					'choices' => [
						'widget' => 'PopUp Widget',
						'text' => 'Text Widget',
					],
					'layout' => 'horizontal',
					'wrapper' => [
						'class' => 'wp3d_calendly'
					],
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1001',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1010',
					'name' => 'calendly_popup_location',
					'instructions' => 'You can also put the shortcode "[wp3d-calendly]"  elsewhere on the website to display your Calendly link.',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_mt'
					],
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1002',
								'operator' => '==',
								'value' => 'text',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1003',
					'name' => 'calendly_event_link',
					'type' => 'text',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_text'
					],
					'prepend' => 'Calendly Event Link',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1001',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1004',
					'name' => 'custom_link_title',
					'type' => 'text',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_text'
					],
					'default_value' => 'Schedule Tour',
					'prepend' => 'Custom Link Title',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1001',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1005',
					'name' => 'calendly_popup_location',
					'type' => 'radio',
					'label' => 'PopUp Widget Location',
					'choices' => [
						'left' => 'Left',
						'center' => 'Center',
						'right' => 'Right',
					],
					'layout' => 'horizontal',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_mt'
					],
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1002',
								'operator' => '==',
								'value' => 'widget',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1006',
					'name' => 'calendly_popup_location',
					'label' => 'PopUp Widget Colors',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_mt'
					],
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1002',
								'operator' => '==',
								'value' => 'widget',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1007',
					'name' => 'calendly_color_back',
					'type' => 'text',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_text'
					],
					'prepend' => 'Background',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1002',
								'operator' => '==',
								'value' => 'widget',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1008',
					'name' => 'calendly_color_border',
					'type' => 'text',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_text'
					],
					'prepend' => 'Border Outline',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1002',
								'operator' => '==',
								'value' => 'widget',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df1009',
					'name' => 'calendly_color_text',
					'type' => 'text',
					'wrapper' => [
						'class' => 'wp3d_calendly wp3d_calendly_text'
					],
					'prepend' => 'Text',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df1002',
								'operator' => '==',
								'value' => 'widget',
							),
						),
					),
				),
				array (
					'key' => 'field_58921c8df12a7',
					'label' => __('Additional Agent Info & Form BCC', 'wp3d-models'),
					'name' => 'add_agent_info',
					'type' => 'true_false',
					'message' => __('Add Additional Agent Info & Logo?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_596ce8e6a0751',
					'label' => __('Contact Form BCC Email', 'wp3d-models'),
					'name' => 'agent_form_bcc_email',
					'type' => 'email',
					'instructions' => __('Use this field to add an additional BCC Email for this Agent.  This makes it possible to add a hidden "Send To" assistant/associate email address.', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df12a7',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),							
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'none',
					'maxlength' => '',
				),				
				array (
					'key' => 'field_589211b139fa9',
					'label' => __('Agent Additional Info', 'wp3d-models'),
					'name' => 'agent_add_info',
					'type' => 'textarea',
					'instructions' => __('Use this field to add supplemental Agent information, like company/brokerage name, address, etc. Simple HTML permitted.', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df12a7',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),			
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '',
					'formatting' => 'br',
				),
				array (
					'key' => 'field_58921d4b0dfed',
					'label' => __('Agent Logo', 'wp3d-models'),
					'name' => 'agent_logo',
					'type' => 'image',
					'instructions' => __('Add supplemental (Agent/Broker/etc.) branding logo. Ideal size is 300px(w) x 120px(h).', 'wp3d-models'),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_58921c8df12a7',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),			
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'all',
				),						
				array (
					'key' => 'field_560c3083a0838',
					'label' => __('Add Agent Links', 'wp3d-models'),
					'name' => 'add_agent_links',
					'type' => 'true_false',
					'message' => __('Include Agent Links?', 'wp3d-models'),
					'default_value' => 0,
				),
				array (
					'key' => 'field_560c0b898e2ea',
					'label' => __('Agent Links', 'wp3d-models'),
					'name' => 'agent_links',
					'type' => 'repeater',
					'instructions' => __('Enter as many, or as few, Agent links as you like. Additionally, you can drag/drop to re-order the specific details that you choose to include.<br><br><b>NOTE: WP3D Models will automatically apply the appropriate social media icon for commonly used networks (Facebook, Twitter, LinkedIn, Google Business Page & Instagram)</b>', 'wp3d-models'),				
					'required' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_560c3083a0838',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
					'sub_fields' => array (
						array (
							'key' => 'field_560c0c168e2ec',
							'label' => __('Link URL', 'wp3d-models'),
							'name' => 'link_url',
							'type' => 'text',
							'required' => 1,
							'column_width' => '',
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
							'append' => '',
							'formatting' => 'none',
							'maxlength' => '',
						),
					),
					'row_min' => 1,
					'row_limit' => '',
					'layout' => 'table',
					'button_label' => 'Add Icon Link',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'wp3d_agent',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'acf_after_title',
				'layout' => 'no_box',
				'hide_on_screen' => array (
					0 => 'the_content',
					1 => 'excerpt',
					2 => 'featured_image',
				),
			),
			'menu_order' => 0,
		));
		
	// ################## END AGENT ##################
	
	// ################## BEGIN CLIENT (TAXONOMY) SPECIFIC  ##################		
	
	register_field_group(array (
		'key' => 'group_5b65ca6cb2e1c',
		'title' => 'Model Client Content',
		'fields' => array(
			// override skinned URL
			array (
				'key' => 'field_5b660f073ef5b',
				'label' => __('Skinned Logo Client Link Override', 'wp3d-models'),
				'name' => 'logo_link_client_override',
				'type' => 'text',
				'instructions' => __('If entered, the "Skinned" logo will become a link to this address for all Models that are associated with this Client.', 'wp3d-models'),
				'conditional_logic' => 0,
				'default_value' => '',
				'placeholder' => 'http(s)://',
				'prepend' => '<span class="dashicons dashicons-admin-site"></span>',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),	
			array (
				'key' => 'field_5b65ca7d96f52',
				'label' => __('Large Logo Client Override', 'wp3d-models'),
				'name' => 'large_logo_client_override',
				'type' => 'image',
				'instructions' => __('If added, this logo will override the "Large" logo uploaded to \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">WP3D Models -> Settings</a>\', for Models assigned to this Client Type.<br><b>Logo should be a transparent (32-bit) PNG and visible on a dark background. It should be sized at exactly 400px wide by 400px tall (square).</b>', 'wp3d-models'),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => 'wp3d-pad-10',
					'id' => '',
				),
				'save_format' => 'object',
				'preview_size' => 'medium',
				'library' => 'all',
			),
			array (
				'key' => 'field_5b65cfbfa86b9',
				'label' => __('Small Logo Client Override', 'wp3d-models'),
				'name' => 'small_logo_client_override',
				'type' => 'image',
				'instructions' => __('If added, this logo will override the "Small" logo uploaded to \'<a href="/wp-admin/edit.php?post_type=model&page=WP3D_Models_settings&tab=branding" target="_blank">WP3D Models -> Settings</a>\', for Models assigned to this Client Type.<br><b>Logo should be a transparent (32-bit) PNG and visible on a dark background. It should be sized at exactly 300px wide by 120px tall (landscape).</b>', 'wp3d-models'),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => 'wp3d-pad-10',
					'id' => '',
				),
				'save_format' => 'object',
				'preview_size' => 'medium',
				'library' => 'all',
			),		
			
		),
		'location' => array(
			array(
				array(
					'param' => 'taxonomy',
					'operator' => '==',
					'value' => 'model-client',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));
	
	// ################## END CLIENT (TAXONOMY) SPECIFIC  ##################		

}

?>
