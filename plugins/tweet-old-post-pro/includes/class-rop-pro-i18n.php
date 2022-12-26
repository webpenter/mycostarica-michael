<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://themeisle.com/
 * @since      8.0.0
 *
 * @package    Rop
 * @subpackage Rop/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      8.0.0
 * @package    Rop
 * @subpackage Rop/includes
 * @author     ThemeIsle <friends@themeisle.com>
 */
class Rop_Pro_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    8.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tweet-old-post',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
		add_filter( 'rop_available_services', array( $this, 'load_service_locals' ), 100 );

	}

	/**
	 * Localize service labels.
	 *
	 * @param array $services Services available.
	 *
	 * @return mixed Services localized.
	 */
	public function load_service_locals( $services ) {
		$services['linkedin']['credentials']['client_id']['description']     = Rop_Pro_I18n::get_labels( 'accounts.lk_app_secret_title' );
		$services['linkedin']['credentials']['secret']['description']        = Rop_Pro_I18n::get_labels( 'accounts.lk_app_id_title' );
		$services['linkedin']['description']                                 = Rop_Pro_I18n::get_labels( 'accounts.lk_app_desc' );
		$services['tumblr']['credentials']['consumer_key']['description']    = Rop_Pro_I18n::get_labels( 'accounts.tmb_app_secret_title' );
		$services['tumblr']['credentials']['consumer_secret']['description'] = Rop_Pro_I18n::get_labels( 'accounts.tmb_app_id_title' );
		$services['tumblr']['description']                                   = Rop_Pro_I18n::get_labels( 'accounts.tmb_app_desc' );
		$services['pinterest']['credentials']['app_id']['description']           = Rop_Pro_I18n::get_labels( 'accounts.pin_app_id_title' );
		$services['pinterest']['credentials']['secret']['description']           = Rop_Pro_I18n::get_labels( 'accounts.pin_secret_title' );
		$services['pinterest']['description']                                = Rop_Pro_I18n::get_labels( 'accounts.pinterest_app_desc' );
		return $services;
	}

	/**
	 * Get labels by key or return all of them.
	 *
	 * @param string $key Access key.
	 *
	 * @return array|mixed|string String localized
	 */
	public static function get_labels( $key = '' ) {
		$labels = array(
			'accounts'   => array(
				'lk_app_secret_title'  => __( 'Please add the Client ID from your LinkedIn app.', 'tweet-old-post' ),
				'lk_app_id_title'      => __( 'Please add the Client Secret from your LinkedIn app.', 'tweet-old-post' ),
				'lk_app_desc'          => sprintf(
					__(
						'You can check
				 %1$shere%2$s how you get these details.',
						'tweet-old-post'
					),
					'<a href="https://docs.revive.social/article/406-how-to-create-a-linkedin-app-for-revive-old-post" target="_blank">',
					'</a>'
				),
				'pin_app_id_title'     => __( 'Your Pinterest application id', 'tweet-old-post' ),
				'pin_secret_title'     => __( 'Your Pinterest application secret', 'tweet-old-post' ),
				'pinterest_app_desc'   => sprintf(
					__(
						'You can check
				 %1$shere%2$s how you get these details.',
						'tweet-old-post'
					),
					'<a href="https://docs.revive.social/article/932-how-to-create-a-pinterest-application-for-revive-old-post" target="_blank">',
					'</a>'
				),
				'tmb_app_secret_title' => __( 'Please add the Consumer Key from your Tumblr app.', 'tweet-old-post' ),
				'tmb_app_id_title'     => __( 'Please add the Consumer Secret from your Tumblr app.', 'tweet-old-post' ),
				'tmb_app_desc'         => sprintf( __( 'You can check %1$shere%2$s how you get these details.', 'tweet-old-post' ), '<a href="https://docs.revive.social/article/405-how-to-create-a-tumblr-application-for-revive-old-post" target="_blank">', '</a>' ),
			),
			'magic_tags' => array(
				'comments' => __( ' comments', 'tweet-old-post' ),
				'sales'    => __( ' sales', 'tweet-old-post' ),
				'example'  => __( '{title} written by {author} on {date}. Currently {comment_count}!', 'tweet-old-post' ),
			),
			'errors'     => array(
				'reconnect_linkedin'      => __( 'You are using an old method of sharing to LinkedIn. Please delete your currently connected LinkedIn account and reconnect it using the "Sign in to LinkedIn" button.', 'tweet-old-post' ),
				'instagram_quota_reached' => __( 'The Instagram quota has been reached for your account. This quota will refresh automatically by Instagram within the next 24 hours. If you continue to receive this error then please try lowering the frequency of shares for Instagram in the Revive Old Posts scheduling settings.', 'tweet-old-post' ),
			),
		);
		if ( empty( $key ) ) {
			return $labels;
		}
		/**
		 * Allow accesing labels by key.
		 */
		$keys = explode( '.', $key );
		if ( count( $keys ) === 1 ) {
			if ( isset( $labels[ $keys[0] ] ) ) {
				return $labels[ $keys[0] ];
			}
		}
		if ( count( $keys ) === 2 ) {
			if ( isset( $labels[ $keys[0] ][ $keys[1] ] ) ) {
				return $labels[ $keys[0] ][ $keys[1] ];
			}
		}

		return '';
	}


}
