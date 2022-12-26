<?php

global $wpdb;

$result = $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts} WHERE `post_type` = 'model' AND `post_status` = 'publish'");
if (empty($result)) {
    $url = get_site_url();
    $files = [
        'tyburrell_cc11.jpg',
        'tyburrell_cc10.jpg',
        'tyburrell_cc8.jpg',
        'tyburrell_cc7.jpg',
        'tyburrell_cc6.jpg',
        'tyburrell_cc5.jpg',
        'tyburrell_cc4.jpg',
        'tyburrell_cc3.jpg',
        'tyburrell_cc2.jpg',
    ];
    
    $wpdb->query("INSERT INTO `wp_posts` (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`)
VALUES
	(1, '2021-05-21 14:43:05', '2021-05-21 14:43:05', 'Ty Burrell, who isn’t a Realtor but plays one on TV’s \"Modern Family,\" put his penthouse condo in Culver City, CA, on the market for $1.4 million and eventually sold it for $1.432M.\r\n\r\nBurrell’s unit is one of 18 within Culver Centrale, a four-story, mixed-use complex in downtown Culver City that opened in 2010. Burrell’s unit went on the market in January 2010 for $945,000, and the actor snapped it up a month later for $50,000 below the list price.\r\n\r\nThe 1,690-square-foot corner condo has two bedrooms, two baths, city and hill views, custom wallpaper, and an open kitchen with Bosch appliances. The balcony measures over 200 square feet, making it ideal for outdoor entertaining &amp; showcasing with <a href=\"https://wp3dmodels.com/\">The WP3D Models Plugin</a> .\r\n\r\n&nbsp;', 'Ty Burrell\'s Penthouse (SAMPLE MODEL)', '', 'publish', 'closed', 'closed', '', 'ty-burrells-penthouse-condo', '', '', '2021-08-26 21:59:34', '2021-08-26 21:59:34', '', 0, '', 0, 'model', '', 0)");

    $sample_post_id = $wpdb->insert_id;
    
    $wpdb->query("UPDATE `wp_posts` SET `guid` = '$url/?post_type=model&#038;p=$sample_post_id'");

    //gallery
    for ($i = 1; $i <= 9; $i++) {
        $filename = substr($files[$i-1], 0, -4);
        $wpdb->query("INSERT INTO `wp_posts` (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`)
VALUES
	(1, '2021-08-20 17:40:03', '2021-08-20 17:40:03', '', '$filename', '', 'inherit', 'open', 'closed', '', '$filename', '', '', '2021-08-20 17:40:03', '2021-08-20 17:40:03', '', $sample_post_id, '$url/wp-content/uploads/2021/05/$filename.jpg', 0, 'attachment', 'image/jpeg', 0)");
        
        $pid = ${'sg' . $i} = $wpdb->insert_id;
        ${'sl' . $i} = strlen((string)$pid);
        $wpdb->query("INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`)
VALUES
	($pid, '_wp_attached_file', '2021/05/$filename.jpg'),
	($pid, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:900;s:6:\"height\";i:600;s:4:\"file\";s:26:\"2021/05/$filename.jpg\";s:5:\"sizes\";a:4:{s:6:\"medium\";a:4:{s:4:\"file\";s:26:\"$filename-300x200.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:26:\"$filename-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:26:\"$filename-768x512.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:13:\"mp-thumb-size\";a:4:{s:4:\"file\";s:26:\"$filename-400x230.jpg\";s:5:\"width\";i:400;s:6:\"height\";i:230;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"1\";s:8:\"keywords\";a:0:{}}}');");
    }

            $wpdb->query("INSERT INTO `wp_posts` (`post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`)
VALUES
	(1, '2021-08-20 17:01:23', '2021-08-20 17:01:23', '', 'mp_image_from_url', '', 'inherit', 'open', 'closed', '', 'mp_image_from_url-6', '', '', '2021-08-20 17:01:23', '2021-08-20 17:01:23', '', $sample_post_id, '$url/wp-content/uploads/2021/05/04.30.2020_11.54.52.jpg', 0, 'attachment', 'image/jpeg', 0),
	(1, '2021-08-20 17:36:38', '2021-08-20 17:36:38', '', 'wp3d-logo (1)', '', 'inherit', 'open', 'closed', '', 'wp3d-logo-1', '', '', '2021-08-20 17:36:38', '2021-08-20 17:36:38', '', $sample_post_id, '$url/wp-content/uploads/2021/05/wp3d-logo-1.png', 0, 'attachment', 'image/png', 0)");

    $thumb_id = $wpdb->insert_id;
    $logo_id = $thumb_id + 1;

    $wpdb->query("INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`)
VALUES
    ($thumb_id, '_wp_attached_file', '2021/05/04.30.2020_11.54.52.jpg'),
	($thumb_id, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:1920;s:6:\"height\";i:1080;s:4:\"file\";s:31:\"2021/05/04.30.2020_11.54.52.jpg\";s:5:\"sizes\";a:7:{s:6:\"medium\";a:4:{s:4:\"file\";s:31:\"04.30.2020_11.54.52-300x169.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:169;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:5:\"large\";a:4:{s:4:\"file\";s:32:\"04.30.2020_11.54.52-1024x576.jpg\";s:5:\"width\";i:1024;s:6:\"height\";i:576;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:31:\"04.30.2020_11.54.52-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:31:\"04.30.2020_11.54.52-768x432.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:432;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:13:\"mp-thumb-size\";a:4:{s:4:\"file\";s:31:\"04.30.2020_11.54.52-400x230.jpg\";s:5:\"width\";i:400;s:6:\"height\";i:230;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"1536x1536\";a:4:{s:4:\"file\";s:32:\"04.30.2020_11.54.52-1536x864.jpg\";s:5:\"width\";i:1536;s:6:\"height\";i:864;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"post-thumbnail\";a:4:{s:4:\"file\";s:32:\"04.30.2020_11.54.52-1568x882.jpg\";s:5:\"width\";i:1568;s:6:\"height\";i:882;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	($thumb_id, '_source_url', 'https://cdn-1.matterport.com/apifs/models/1HPbtLDTsdC/images/aghGueKuHv7/04.30.2020_11.54.52.jpg?t=2-f1f03bc03e5476b3d7e5de86b99d40abc5838cd5-1630083682-1&width=1920&height=1080&fit=crop&disable=upscale&width=1920'),
	($logo_id, '_wp_attached_file', '2021/05/wp3d-logo-1.png'),
	($logo_id, '_wp_attachment_metadata', 'a:5:{s:5:\"width\";i:900;s:6:\"height\";i:600;s:4:\"file\";s:26:\"2021/05/wp3d-logo-1.png\";s:5:\"sizes\";a:4:{s:6:\"medium\";a:4:{s:4:\"file\";s:26:\"wp3d-logo-1-300x200.png\";s:5:\"width\";i:300;s:6:\"height\";i:200;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:26:\"wp3d-logo-1-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:26:\"wp3d-logo-1-768x512.png\";s:5:\"width\";i:768;s:6:\"height\";i:512;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:13:\"mp-thumb-size\";a:4:{s:4:\"file\";s:26:\"wp3d-logo-1-400x230.png\";s:5:\"width\";i:400;s:6:\"height\";i:230;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"1\";s:8:\"keywords\";a:0:{}}}')");

    $wpdb->query("INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`)
VALUES
	($sample_post_id, '_wp3d_model_update', 'update'),
	($sample_post_id, '_edit_last', '1'),
	($sample_post_id, 'wp3d_model_type', 'matterport'),
	($sample_post_id, '_wp3d_model_type', 'field_587557a304734'),
	($sample_post_id, 'model_link', 'https://my.matterport.com/show/?m=1HPbtLDTsdC'),
	($sample_post_id, '_model_link', 'field_5504b8726d566'),
	($sample_post_id, 'customize_showcase', ''),
	($sample_post_id, '_customize_showcase', 'field_5b226cbd9e754'),
	($sample_post_id, 'model_subtitle', '9900 Culver Blvd Ph D, Culver City, CA 90232'),
	($sample_post_id, '_model_subtitle', 'field_5505e4a312821'),
	($sample_post_id, 'model_content', ''),
	($sample_post_id, '_model_content', 'field_55fafb4189f07'),
	($sample_post_id, 'default_view_link', 'skinned'),
	($sample_post_id, '_default_view_link', 'field_55fad97e6fc5a'),
	($sample_post_id, 'related_models', 'none'),
	($sample_post_id, '_related_models', 'field_560c5d8cfc91c'),
	($sample_post_id, 'model_status', ''),
	($sample_post_id, '_model_status', 'field_597421e963e10'),
	($sample_post_id, 'mark_sold', '0'),
	($sample_post_id, '_mark_sold', 'field_560c4e03c7e4f'),
	($sample_post_id, 'mark_pending', '0'),
	($sample_post_id, '_mark_pending', 'field_572a6049e419c'),
	($sample_post_id, 'add_property_info_details', '1'),
	($sample_post_id, '_add_property_info_details', 'field_5600b127d8fcd'),
	($sample_post_id, 'add_property_text_tabs', '1'),
	($sample_post_id, '_add_property_text_tabs', 'field_560106905e9c6'),
	($sample_post_id, 'disable_model_sharing', '0'),
	($sample_post_id, '_disable_model_sharing', 'field_5600f99123391'),
	($sample_post_id, 'disable_model_connect', '0'),
	($sample_post_id, '_disable_model_connect', 'field_564b6bb18a959'),
	($sample_post_id, 'showcase_branding', 'intro-branded'),
	($sample_post_id, '_showcase_branding', 'field_55fae055e188f'),
	($sample_post_id, 'add_override_logos', '1'),
	($sample_post_id, '_add_override_logos', 'field_5600db6173dac'),
	($sample_post_id, 'add_model_image_override', '0'),
	($sample_post_id, '_add_model_image_override', 'field_5600ef46aece1'),
	($sample_post_id, 'add_model_video_background', '0'),
	($sample_post_id, '_add_model_video_background', 'field_570fc4dcb7d8c'),
	($sample_post_id, 'add_gallery', '1'),
	($sample_post_id, '_add_gallery', 'field_5600ee3b9d43a'),
	($sample_post_id, 'add_video', 'youtube'),
	($sample_post_id, '_add_video', 'field_5600f38ea014c'),
	($sample_post_id, 'add_floorplan', '0'),
	($sample_post_id, '_add_floorplan', 'field_5600ab014fa03'),
	($sample_post_id, 'add_smart_gallery', '0'),
	($sample_post_id, '_add_smart_gallery', 'field_560c53d4d82c6'),
	($sample_post_id, 'model_address_source', 'no_address'),
	($sample_post_id, '_model_address_source', 'field_562d185f01029'),
	($sample_post_id, 'add_form', '1'),
	($sample_post_id, '_add_form', 'field_5600f6ddcd8bd'),
	($sample_post_id, 'showcase_no_branding', '0'),
	($sample_post_id, '_showcase_no_branding', 'field_5636c58961bae'),
	($sample_post_id, 'showcase_no_branding_links', '0'),
	($sample_post_id, '_showcase_no_branding_links', 'field_58717be640a7d'),
	($sample_post_id, 'model_autoplay', '0'),
	($sample_post_id, '_model_autoplay', 'field_55521e1ba8ea0'),
	($sample_post_id, 'showcase_title_panel', '1'),
	($sample_post_id, '_showcase_title_panel', 'field_59619c9a94213'),
	($sample_post_id, 'widened_field_of_view', '0'),
	($sample_post_id, '_widened_field_of_view', 'field_560c736400fbc'),
	($sample_post_id, 'showcase_tour_cta', '1'),
	($sample_post_id, '_showcase_tour_cta', 'field_59619da96b541'),
	($sample_post_id, 'model_hide_multifloor', '0'),
	($sample_post_id, '_model_hide_multifloor', 'field_55ac45592f5da'),
	($sample_post_id, 'model_force_help', ''),
	($sample_post_id, '_model_force_help', 'field_55fad8dc6fc59'),
	($sample_post_id, 'disable_model_tour_panning', '0'),
	($sample_post_id, '_disable_model_tour_panning', 'field_571287c33c674'),
	($sample_post_id, 'dollhouse_view', '1'),
	($sample_post_id, '_dollhouse_view', 'field_59c97a4799d50'),
	($sample_post_id, 'enable_model_tour_loop', '0'),
	($sample_post_id, '_enable_model_tour_loop', 'field_571287d631746'),
	($sample_post_id, 'mattertag_content', '1'),
	($sample_post_id, '_mattertag_content', 'field_59c97ae8342f2'),
	($sample_post_id, 'disable_model_tour_path', '0'),
	($sample_post_id, '_disable_model_tour_path', 'field_571287e5edf2c'),
	($sample_post_id, 'force_show_highlight_reel', ''),
	($sample_post_id, '_force_show_highlight_reel', 'field_571287f87c1af'),
	($sample_post_id, 'disable_model_vr', '0'),
	($sample_post_id, '_disable_model_vr', 'field_58717b6216a40'),
	($sample_post_id, 'vr_limited_mode', '0'),
	($sample_post_id, '_vr_limited_mode', 'field_59c97b95e5359'),
	($sample_post_id, 'disable_space_scroll', '0'),
	($sample_post_id, '_disable_space_scroll', 'field_572a87f1cd1d3'),
	($sample_post_id, 'enable_model_quickstart', '0'),
	($sample_post_id, '_enable_model_quickstart', 'field_58717d53ec3d1'),
	($sample_post_id, 'autostart_guided_model_tour', '0'),
	($sample_post_id, '_autostart_guided_model_tour', 'field_57128802617c9'),
	($sample_post_id, 'model_guided_tour_transition', '0'),
	($sample_post_id, '_model_guided_tour_transition', 'field_58744f2ceddc4'),
	($sample_post_id, 'model_guided_tour_seconds', ''),
	($sample_post_id, '_model_guided_tour_seconds', 'field_587448fec09f7'),
	($sample_post_id, 'showcase_highlight_time', ''),
	($sample_post_id, '_showcase_highlight_time', 'field_587181e7e28f7'),
	($sample_post_id, 'model_zoom', '0'),
	($sample_post_id, '_model_zoom', 'field_58744f3a7fbde'),
	($sample_post_id, 'model_pin', '0'),
	($sample_post_id, '_model_pin', 'field_5eae017f3dd97'),
	($sample_post_id, 'model_portal', '0'),
	($sample_post_id, '_model_portal', 'field_5eae01873dd98'),
	($sample_post_id, 'mp_model_lang', ''),
	($sample_post_id, '_mp_model_lang', 'field_58717ea2d4afc'),
	($sample_post_id, 'mp_model_alternate_cdn', ''),
	($sample_post_id, '_mp_model_alternate_cdn', 'field_5b26a06dbc0eb'),
	($sample_post_id, 'listing_id', ''),
	($sample_post_id, '_listing_id', 'field_58e910c31807a'),
	($sample_post_id, 'admin_notes', ''),
	($sample_post_id, '_admin_notes', 'field_557845b7646f9'),
	($sample_post_id, 'model_list_exclude', '0'),
	($sample_post_id, '_model_list_exclude', 'field_572a8c97e18ff'),
	($sample_post_id, 'retrieve_mp_data', '0'),
	($sample_post_id, '_retrieve_mp_data', 'field_5632071a407fe'),
	($sample_post_id, 'generate_social_image', '0'),
	($sample_post_id, '_generate_social_image', 'field_5797031770cab'),
	($sample_post_id, '_matterport_api_data', 'a:6:{s:3:\"sid\";s:11:\"1HPbtLDTsdC\";s:4:\"name\";s:28:\"Ty Burrell\'s Penthouse Condo\";s:7:\"created\";s:27:\"2020-04-03T20:25:38.089734Z\";s:14:\"player_options\";a:17:{s:14:\"highlight_reel\";b:1;s:12:\"tour_buttons\";b:1;s:6:\"labels\";b:1;s:13:\"contact_email\";b:1;s:7:\"address\";b:1;s:12:\"contact_name\";b:1;s:12:\"presented_by\";b:1;s:10:\"floor_plan\";b:1;s:12:\"measurements\";b:1;s:9:\"dollhouse\";b:1;s:18:\"measurements_saved\";b:1;s:16:\"fast_transitions\";b:1;s:12:\"external_url\";b:1;s:13:\"model_summary\";b:1;s:13:\"contact_phone\";b:1;s:10:\"model_name\";b:1;s:8:\"autoplay\";b:0;}s:5:\"is_vr\";b:1;s:6:\"vr_url\";s:48:\"https://my.matterport.com/vr/show/?m=1HPbtLDTsdC\";}'),
	($sample_post_id, '_matterport_mattertag_data', 'a:0:{}'),
	($sample_post_id, '_social_image_id', ''),
	($sample_post_id, '_yoast_wpseo_estimated-reading-time-minutes', ''),
	($sample_post_id, '_edit_lock', '1630416062:1'),
	($sample_post_id, 'property_info_details_0_title', 'Beds'),
	($sample_post_id, '_property_info_details_0_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_0_value', '2'),
	($sample_post_id, '_property_info_details_0_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_1_title', 'Baths'),
	($sample_post_id, '_property_info_details_1_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_1_value', '2'),
	($sample_post_id, '_property_info_details_1_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_2_title', 'Living Area'),
	($sample_post_id, '_property_info_details_2_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_2_value', '1,680 sqft'),
	($sample_post_id, '_property_info_details_2_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_3_title', 'Sold'),
	($sample_post_id, '_property_info_details_3_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_3_value', '$1,432,300'),
	($sample_post_id, '_property_info_details_3_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details', '14'),
	($sample_post_id, '_property_info_details', 'field_5600b183d8fce'),
	($sample_post_id, 'property_text_tabs_0_tab_title', 'Property Notes'),
	($sample_post_id, '_property_text_tabs_0_tab_title', 'field_5601072f5e9c8'),
	($sample_post_id, 'property_text_tabs_0_tab_wysiwyg', 'The fourth-floor aerie measures in at a decidedly modest-by-celeb-standards 1,680-square-feet with maple wood floors and custom cloth wall coverings.\r\n\r\nThe open kitchen, fitted with sleek designer appliances and solid surface counter tops, has a huge center island with four-stool integrated snack counter while the adjoining combination living/dining space opens through floor-to-ceiling glass sliders a private terrace with open views towards the Baldwin Hills.\r\n\r\nThe guest/family bedroom, which opens off the entrance gallery, has a spacious and custom-fitted walk-in closet while the master bedroom, which opens less than elegantly directly off the main living space, has glass sliders to the terrace, a spacious fitted walk-in closet/dressing area and an efficiently arranged bathroom with floating double sink vanity, soaking but and separate glass-enclosed shower space with integrated bench seat.\r\n\r\nThere’s an unexpectedly large laundry room just inside the front door and online marketing materials show the unit transfers with two side-by-side parking spaces in a secured garage, both with outlets to power up a electric car, and the building offers both cardio and yoga studios for its exercise inclined residents.'),
	($sample_post_id, '_property_text_tabs_0_tab_wysiwyg', 'field_560107485e9c9'),
	($sample_post_id, 'property_text_tabs_1_tab_title', 'Features'),
	($sample_post_id, '_property_text_tabs_1_tab_title', 'field_5601072f5e9c8'),
	($sample_post_id, 'property_text_tabs_1_tab_wysiwyg', 'Choosing to live in this spacious 2 bed, 2 bath Penthouse located in Downtown Culver City, is choosing a lifestyle with endless opportunities for entertainment, relaxation, convenience and modern luxury.\r\n\r\nExhilarating energy emanating from sidewalk cafes, boutiques and theaters lining the intersection of Culver and Washington Blvd.\r\n\r\nNestled in one of the largest Penthouses in the building, this corner location is the most private and includes the most expansive views in the building and of Downtown Culver. Enjoy idyllic views of rolling hills and an abundance of natural light.\r\n\r\nCustom fabric wallpaper throughout the entry, kitchen and living room adds warmth and elegance.\r\n\r\nThe open kitchen features modern Bosch appliances, caesarstone countertops and an expansive island perfect for entertaining.\r\n\r\nAmenities include outdoor entertaining area, yoga/cardio studios and 2 side-by-side parking spaces with outlets for 2 electric cars.\r\n\r\nEasy access to Sony, Silicon Beach and highly-ranked schools.'),
	($sample_post_id, '_property_text_tabs_1_tab_wysiwyg', 'field_560107485e9c9'),
	($sample_post_id, 'property_text_tabs', '2'),
	($sample_post_id, '_property_text_tabs', 'field_560106b15e9c7'),
	($sample_post_id, 'preload_model', '0'),
	($sample_post_id, '_preload_model', 'field_570c1c936509c'),
	($sample_post_id, 'custom_showcase_statement', ''),
	($sample_post_id, '_custom_showcase_statement', 'field_5622730a302ff'),
	($sample_post_id, 'logo_overlay', '1'),
	($sample_post_id, '_logo_overlay', 'field_562fc6e4ec357'),
	($sample_post_id, 'force_default_logo_overlay', '0'),
	($sample_post_id, '_force_default_logo_overlay', 'field_589297cadca7c'),
	($sample_post_id, 'play_button_only', '0'),
	($sample_post_id, '_play_button_only', 'field_572a38ca826d2'),
	($sample_post_id, 'custom_copyright', ''),
	($sample_post_id, '_custom_copyright', 'field_57e832b07c9e3'),
	($sample_post_id, 'disable_header_bar', '0'),
	($sample_post_id, '_disable_header_bar', 'field_57e8326903072'),
	($sample_post_id, '_yoast_wpseo_content_score', '30'),
	($sample_post_id, '_thumbnail_id', '$thumb_id'),
	($sample_post_id, 'property_info_details_4_title', 'Built'),
	($sample_post_id, '_property_info_details_4_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_4_value', '2009'),
	($sample_post_id, '_property_info_details_4_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'large_logo_override', '$logo_id'),
	($sample_post_id, '_large_logo_override', 'field_5600d9c173daa'),
	($sample_post_id, 'small_logo_override', '$logo_id'),
	($sample_post_id, '_small_logo_override', 'field_562dd55a0aab1'),
	($sample_post_id, 'logo_link_override', 'https://wp3dmodels.com/'),
	($sample_post_id, '_logo_link_override', 'field_597971f5a1d67'),
	($sample_post_id, 'gallery_type', 'zoom_slider'),
	($sample_post_id, '_gallery_type', 'field_5866e2d905958'),
	($sample_post_id, 'photo_gallery', 'a:9:{i:0;s:$sl1:\"$sg1\";i:1;s:$sl2:\"$sg2\";i:2;s:$sl3:\"$sg3\";i:3;s:$sl4:\"$sg4\";i:4;s:$sl5:\"$sg5\";i:5;s:$sl6:\"$sg6\";i:6;s:$sl7:\"$sg7\";i:7;s:$sl8:\"$sg8\";i:8;s:$sl9:\"$sg9\";}'),
	($sample_post_id, '_photo_gallery', 'field_5600baeabc761'),
	($sample_post_id, 'youtube_video_link', 'https://www.youtube.com/watch?v=x2nPTeVOzk4'),
	($sample_post_id, '_youtube_video_link', 'field_5600bb3abc762'),
	($sample_post_id, 'youtube_unbranded_video_link', 'https://www.youtube.com/watch?v=x2nPTeVOzk4'),
	($sample_post_id, '_youtube_unbranded_video_link', 'field_57196650963c6'),
	($sample_post_id, 'lead_generation_form_type', 'default-custom'),
	($sample_post_id, '_lead_generation_form_type', 'field_591892b4b89bf'),
	($sample_post_id, 'model_location', 'a:15:{s:7:\"address\";s:46:\"9900 Culver Blvd, Culver City, California, USA\";s:3:\"lat\";d:34.0207773;s:3:\"lng\";d:-118.396365;s:4:\"zoom\";i:14;s:8:\"place_id\";s:27:\"ChIJHebJ8ii6woARytV9ZRUFROk\";s:4:\"name\";s:16:\"9900 Culver Blvd\";s:13:\"street_number\";s:4:\"9900\";s:11:\"street_name\";s:16:\"Culver Boulevard\";s:17:\"street_name_short\";s:11:\"Culver Blvd\";s:4:\"city\";s:11:\"Culver City\";s:5:\"state\";s:10:\"California\";s:11:\"state_short\";s:2:\"CA\";s:9:\"post_code\";s:5:\"90232\";s:7:\"country\";s:13:\"United States\";s:13:\"country_short\";s:2:\"US\";}'),
	($sample_post_id, '_model_location', 'field_5505e19026111'),
	($sample_post_id, 'default_send_to_email_address', 'info@wp3dmodels.com'),
	($sample_post_id, '_default_send_to_email_address', 'field_5600f79634a31'),
	($sample_post_id, '_yoast_wpseo_primary_model-type', '2'),
	($sample_post_id, '_yoast_wpseo_primary_model-client', '3'),
	($sample_post_id, 'property_info_details_5_title', 'Parking'),
	($sample_post_id, '_property_info_details_5_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_5_value', 'Covered (2 spaces)'),
	($sample_post_id, '_property_info_details_5_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_6_title', 'Heat'),
	($sample_post_id, '_property_info_details_6_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_6_value', 'In-Floor Radiant'),
	($sample_post_id, '_property_info_details_6_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_7_title', 'Fireplace'),
	($sample_post_id, '_property_info_details_7_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_7_value', 'Gas'),
	($sample_post_id, '_property_info_details_7_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_8_title', 'Taxes (2021)'),
	($sample_post_id, '_property_info_details_8_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_8_value', '$12,500 (est)'),
	($sample_post_id, '_property_info_details_8_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_9_title', 'Laundry'),
	($sample_post_id, '_property_info_details_9_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_9_value', 'Washer / Dryer in Unit'),
	($sample_post_id, '_property_info_details_9_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_10_title', 'Landscaping'),
	($sample_post_id, '_property_info_details_10_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_10_value', 'Included'),
	($sample_post_id, '_property_info_details_10_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_11_title', 'Common Areas'),
	($sample_post_id, '_property_info_details_11_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_11_value', 'Pool, Gym, Yoga Studio'),
	($sample_post_id, '_property_info_details_11_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_12_title', 'Terrace'),
	($sample_post_id, '_property_info_details_12_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_12_value', 'Private'),
	($sample_post_id, '_property_info_details_12_value', 'field_5600b588d8fd0'),
	($sample_post_id, 'property_info_details_13_title', 'Walkability '),
	($sample_post_id, '_property_info_details_13_title', 'field_5600b51dd8fcf'),
	($sample_post_id, 'property_info_details_13_value', '8/10'),
	($sample_post_id, '_property_info_details_13_value', 'field_5600b588d8fd0');
");

    if (!is_dir(ABSPATH . 'wp-content/uploads/2021/')) {
        mkdir(ABSPATH . 'wp-content/uploads/2021/');
        mkdir(ABSPATH . 'wp-content/uploads/2021/05/');
    } elseif (!is_dir(ABSPATH . 'wp-content/uploads/2021/05')) {
        mkdir(ABSPATH . 'wp-content/uploads/2021/05/');
    }

    foreach (array_slice(scandir(ABSPATH . 'wp-content/plugins/wp3d-models-free/assets/sample/'), 2) as $file) {
        copy(ABSPATH . 'wp-content/plugins/wp3d-models-free/assets/sample/' . $file, ABSPATH . 'wp-content/uploads/2021/05/' . $file);
    }

}
