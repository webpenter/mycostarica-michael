<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link       https://themeisle.com/
 * @since      2.0.0
 *
 * @package    Rop_Pro
 * @subpackage Rop_Pro/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Rop
 * @subpackage Rop/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class Rop_Pro_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    8.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    8.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    8.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Method called by filter to update default available services.
	 *
	 * @since   8.0.0
	 * @access  public
	 *
	 * @param   array $services_defaults The service defaults.
	 *
	 * @return mixed
	 */
	public function available_services( $services_defaults ) {

		$global_settings = new Rop_Global_Settings();

		$services_defaults['facebook']['allowed_accounts'] = 50;
		$services_defaults['twitter']['allowed_accounts']  = 50;
		$services_defaults['linkedin']                     = array(
			'active'           => $global_settings->license_type() > 0 ? true : false,
			'name'             => 'LinkedIn',
			'two_step_sign_in' => true,
			'allowed_accounts' => 50,
			'credentials'      => array(
				'client_id' => array(
					'name'        => 'Client ID',
					'description' => 'Please add the Client ID from your LinkedIn app.',
				),
				'secret'    => array(
					'name'        => 'Client Secret',
					'description' => '',
				),
			),
		);
		$services_defaults['tumblr']                       = array(
			'active'           => $global_settings->license_type() > 0 ? true : false,
			'name'             => 'Tumblr',
			'allowed_accounts' => 50,
			'two_step_sign_in' => true,
			'credentials'      => array(
				'consumer_key'    => array(
					'name'        => 'Consumer Key',
					'description' => '',
				),
				'consumer_secret' => array(
					'name'        => 'Consumer Secret',
					'description' => '',
				),
			),
		);
		$services_defaults['pinterest']                    = array(
			'active'           => $global_settings->license_type() > 0 ? true : false,
			'name'             => 'Pinterest',
			'two_step_sign_in' => true,
			'credentials'      => array(
				'app_id' => array(
					'name'        => 'App ID',
					'description' => 'Your Pinterest application id',
				),
				'secret' => array(
					'name'        => 'App secret',
					'description' => 'Your Pinterest application secret',
				),
			),
			'allowed_accounts' => 50,
			'description'      => '',
		);

		$services_defaults['gmb'] = array(
			'active'           => $global_settings->license_type() > 0 ? true : false,
			'name'             => 'Gmb',
			'credentials'      => array(
				'access_token' => array(
					'name'        => 'Access Token',
					'description' => 'Your Google My Business application access token',
				),
			),
			'two_step_sign_in' => true,
			'allowed_accounts' => 50,
		);

		$services_defaults['vk'] = array(
			'active'           => $global_settings->license_type() > 0 ? true : false,
			'name'             => 'Vk',
			'credentials'      => array(
				'access_token' => array(
					'name'        => 'Access Token',
					'description' => 'Your VK application access token',
				),
			),
			'two_step_sign_in' => true,
			'allowed_accounts' => 50,
		);

		return $services_defaults;
	}

	/**
	 * Register the repeatable meta box if custom messages are enabled.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function register_meta_box() {
		if ( ! class_exists( 'Rop_Settings_Model' ) ) {
			return;
		}
		$general_settings = new Rop_Settings_Model();
		if ( $general_settings->get_custom_messages() ) {

			$post_types = get_post_types();

			foreach ( $post_types as $post_type ) {
				add_meta_box(
					'rop-custom-messages-group',
					__( 'Revive Old Posts Share Content Variations', 'tweet-old-post' ),
					array(
						$this,
						'custom_meta_box',
					),
					$post_type,
					'normal',
					'default'
				);
			}
		}
	}

	/**
	 * Display the custom messages meta box.
	 *
	 * @since   2.0.0
	 * @access  public
	 */
	public function custom_meta_box() {
		global $post;
		$rop_custom_messages_group = get_post_meta( $post->ID, 'rop_custom_messages_group', true );
		$rop_custom_images_group   = get_post_meta( $post->ID, 'rop_custom_images_group', true );
		wp_nonce_field( 'rop_repeatable_meta_box_nonce', 'rop_repeatable_meta_box_nonce' );

		include ROP_PATH . '/includes/admin/views/custom_fields_view.php';
	}

	/**
	 * Save method for custom messages meta box.
	 *
	 * @since   2.0.0
	 *
	 * @param   int $post_id The post ID.
	 */
	public function custom_repeatable_meta_box_save( $post_id ) {
		if ( ! isset( $_POST['rop_repeatable_meta_box_nonce'] ) ||
			 ! wp_verify_nonce( $_POST['rop_repeatable_meta_box_nonce'], 'rop_repeatable_meta_box_nonce' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$old        = get_post_meta( $post_id, 'rop_custom_messages_group', true );
		$old_images = get_post_meta( $post_id, 'rop_custom_images_group', true );
		$new        = array();
		$new_images = array();
		$text       = $_POST['rop_custom_description'];
		$images     = ( isset( $_POST['rop_custom_image'] ) ) ? $_POST['rop_custom_image'] : array();
		$count      = count( $text );
		for ( $i = 0; $i < $count; $i ++ ) {
			if ( ! empty( trim( $text[ $i ] ) ) ) {
				$new[ $i ]['rop_custom_description'] = stripslashes( strip_tags( $text[ $i ] ) );

				if ( isset( $images[ $i ] ) && ! empty( trim( $images[ $i ] ) ) ) {
					$new_images[ $i ]['rop_custom_image'] = ( is_numeric( $images[ $i ] ) ? absint( $images[ $i ] ) : '' );
				}
			}
		}

		if ( ! empty( $new_images ) && $new_images != $old_images ) {
			update_post_meta( $post_id, 'rop_custom_images_group', $new_images );
		} elseif ( empty( $new_images ) && $old_images ) {
			delete_post_meta( $post_id, 'rop_custom_images_group', $old_images );
		}

		if ( ! empty( $new ) && $new != $old ) {
			update_post_meta( $post_id, 'rop_custom_messages_group', $new );
		} elseif ( empty( $new ) && $old ) {
			delete_post_meta( $post_id, 'rop_custom_messages_group', $old );
		}
	}

	/**
	 * Media attachment custom field.
	 *
	 * @since   2.1.0
	 * @access  public
	 */
	public function rop_media_attachment_field( $form_fields, $post ) {

		$settings        = new Rop_Settings_Model();
		$global_settings = new Rop_Global_Settings();
		$admin           = new Rop_Admin();

		$post_types = wp_list_pluck( $settings->get_selected_post_types(), 'value' );

		if ( ! in_array( 'attachment', $post_types ) ) {
			return $form_fields;
		}

		if ( $global_settings->license_type() < 1 ) {
			return $form_fields;
		}

		$accepted_mime_types = $admin->rop_supported_mime_types()['all'];

		if ( ! in_array( get_post_mime_type( $post->ID ), $accepted_mime_types ) ) {
			return $form_fields;
		}

		$selected = get_post_meta( $post->ID, '_rop_media_share', true );

		if ( $selected == 'on' ) {
			$output = "<input type='checkbox' name='attachments[{$post->ID}][rop_media_share]' id='attachments[{$post->ID}][rop_media_share]' checked>";
		} else {
			$output = "<input type='checkbox' name='attachments[{$post->ID}][rop_media_share]' id='attachments[{$post->ID}][rop_media_share]' >";
		}

		$form_fields['rop_media_share'] = array(
			'label' => __( 'Allow sharing <small>via Revive Old Post</small>', 'tweet-old-post' ),
			'input' => 'html',
			'html'  => $output,
		);

		return $form_fields;
	}

	/**
	 * Save attachment custom field.
	 *
	 * @since   2.1.0
	 * @access  public
	 */
	public function save_rop_media_attachment_field( $post, $attachment ) {

		$settings   = new Rop_Settings_Model;
		$post_types = wp_list_pluck( $settings->get_selected_post_types(), 'value' );

		if ( ! in_array( 'attachment', $post_types ) ) {
			return $post;
		}

		if ( isset( $attachment['rop_media_share'] ) ) {
			update_post_meta( $post['ID'], '_rop_media_share', $attachment['rop_media_share'] );
		} else {
			delete_post_meta( $post['ID'], '_rop_media_share' );
		}

		return $post;
	}

	/**
	 * Enable the shorteners that are now available.
	 *
	 * @since   ?
	 * @access  public
	 */
	public function available_shorteners( $shorteners ) {
		$shorteners['rviv.ly']['active'] = true;
		return $shorteners;
	}


	/**
	 * Use the shortener that is specified.
	 *
	 * @since   ?
	 * @access  public
	 */
	public function shorten_url( $url, $service, $website, $credentials, $instance ) {
		$shortURL = $url;

		// if license has expired, use the default link.
		$global_settings = new Rop_Global_Settings();
		if ( 0 >= $global_settings->license_type() ) {
			return $shortURL;
		}

		switch ( $service ) {
			case 'rviv.ly':
				$response = $instance->callAPI(
					'http://rviv.ly/yourls-api.php',
					array( 'method' => 'post' ),
					array( 'action' => 'shorturl', 'format' => 'simple', 'signature' => substr( md5( $website . md5( 'themeisle' ) ), 0, 10 ), 'url' => $url, 'website' => base64_encode( $website ) ),
					null
				);

				$shortURL = $url;
				if ( intval( $response['error'] ) == 200 ) {
					$shortURL = $response['response'];
				}
				if ( $shortURL == null || $shortURL === '' ) {
					$shortURL = $url;
				}
				break;
		}

		return $shortURL;
	}

	/**
	 * Update the post publish date after a share has occurred.
	 *
	 * @param array $post_id The post ID to update the publish date for.
	 * @return void
	 */
	public function update_post_publish_date( $post_id ) {

		$settings = new Rop_Settings_Model();
		$logger = new Rop_Logger();

		if ( empty( $settings->get_update_post_published_date() ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( $post_type === 'attachment' ) {
			return;
		}

		$args = array(
			'ID'            => $post_id,
			'post_date'     => current_time( 'mysql' ),
			'post_date_gmt' => get_gmt_from_date( current_time( 'mysql' ) ),
		);

		$id = wp_update_post( $args );

		if ( ! empty( $id ) ) {
			$logger->info( 'Post published date updated successfully.' );
		} else {
			$logger->info( 'Post published date update failed.' );
		}

	}
}
