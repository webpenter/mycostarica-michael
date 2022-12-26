<?php
/**
 * The file houses the sharing logic for Instagram.
 *
 * The Instagram account is imported along with Facebook Pages and Groups in the Lite version.
 * But the sharing for instagram lives here.
 *
 * @link       https://themeisle.com/
 * @since      8.7.0
 *
 * @package    Rop_Pro
 * @subpackage Rop_Pro/includes/admin/services
 */

/**
 * Class Rop_Facebook_Service
 *
 * @since   8.0.0
 * @link    https://themeisle.com/
 */
class Rop_Pro_Instagram_Service {


	/**
	 * Method for publishing to Instagram service.
	 *
	 * Https://developers.facebook.com/docs/instagram-api/reference/ig-user/media
	 * Https://developers.facebook.com/docs/instagram-api/reference/ig-user/media_publish
	 *
	 * @since  8.7.0
	 * @access public
	 *
	 * @param array $post_details The post details to be published by the service.
	 * @param array $hashtags Hashtags.
	 * @param array $account_details The account details.
	 *
	 * @return mixed
	 */
	public static function share( $post_details, $hashtags, $account_details = array() ) {

		$ig_user_id = $account_details['id'];
		$access_token = $account_details['access_token'];
		$logger = new Rop_Logger();

		// Check if the rate limit has been reached. This is currently 25, but might increase in the future.
		// https://developers.facebook.com/docs/instagram-api/reference/ig-user/content_publishing_limit
		if ( self::instagram_quota_reached( $ig_user_id, $access_token ) === true ) {
			$logger->alert_error( Rop_Pro_I18n::get_labels( 'errors.instagram_quota_reached' ) );
			return false;
		}

		$post_id = $post_details['post_id'];
		$attachment_url = $post_details['post_image'];

		if ( empty( $attachment_url ) ) {
			$error_message = sprintf( Rop_I18n::get_labels( 'errors.no_image_found' ), get_the_title( $post_id ), $account_details['user'] );
			$logger->alert_error( $error_message );
			return false;
		}

		// Test attachment url for local development.
		// Facebook cURLs the attachment url so it needs to be on a live server, so we have below for testing on local.
		// $attachment_url = 'https://loremflickr.com/1000/720';

		$caption = urlencode( $post_details['content'] . ' ' . self::get_url( $post_details ) . $hashtags );

		$url = sprintf(
			'https://graph.facebook.com/%1$s/media?image_url=%2$s&caption=%3$s&access_token=%4$s',
			$ig_user_id,
			$attachment_url,
			$caption,
			$access_token
		);

		$media_container_creation_response = wp_remote_post(
			$url,
			array(
				'method'  => 'POST',
				'timeout' => 60,
			)
		);

		$media_container_creation_response_body = json_decode( wp_remote_retrieve_body( $media_container_creation_response ), true );

		if ( ! empty( $media_container_creation_response_body['id'] ) ) {

			$media_publish_url = sprintf(
				'https://graph.facebook.com/%1$s/media_publish?creation_id=%2$s&access_token=%3$s',
				$ig_user_id,
				$media_container_creation_response_body['id'],
				$access_token
			);

			$media_container_publish_response = wp_remote_post(
				$media_publish_url,
				array(
					'method'  => 'POST',
					'timeout' => 60,
				)
			);

			$media_container_publish_response_body = json_decode( wp_remote_retrieve_body( $media_container_publish_response ), true );

			if ( ! empty( $media_container_publish_response_body['id'] ) ) {

				$logger->alert_success(
					sprintf(
						'Successfully shared %s to %s ',
						html_entity_decode( get_the_title( $post_id ), ENT_QUOTES ),
						$account_details['user']
					)
				);

				return true;
			} else {
				$logger->alert_error( 'Error posting to Instagram: ' . $media_container_publish_response );
				return false;
			}
		} elseif ( ! empty( $media_container_creation_response_body['error'] ) ) {
			$logger->alert_error( $media_container_creation_response_body['error'] );
			return false;
		} else {
			$logger->alert_error( $media_container_creation_response );
			return false;
		}

	}

	/**
	 * Method to generate url for service post share.
	 *
	 * @param array $post_details The post details to be published by the service.
	 *
	 * @return string
	 * @since   8.7.0
	 * @access  private
	 */
	private static function get_url( $post_details ) {

		$link = ( ! empty( $post_details['post_url'] ) ) ? ' ' . $post_details['post_url'] : '';

		if ( empty( $link ) ) {
			return '';
		}

		if ( ! $post_details['short_url'] ) {
			return $link;
		}
		if ( empty( $post_details['short_url_service'] ) ) {
			return $link;
		}

			$post_format_helper = new Rop_Post_Format_Helper();

		if ( $post_details['short_url_service'] === 'wp_short_url' ) {
			return $link;
		}

		$link = ' ' . $post_format_helper->get_short_url( $post_details['post_url'], $post_details['short_url_service'], $post_details['shortner_credentials'] );

		return $link;
	}

	/**
	 * Method to check Instagram rate limit
	 *
	 * Https://developers.facebook.com/docs/instagram-api/reference/ig-user/content_publishing_limit
	 *
	 * @access  private
	 * @since   8.7.0
	 * @param array $ig_user_id The id of the instagram profile on Facebook's end.
	 * @param array $access_token The access token.
	 * @return bool If the quota has been reached or not.
	 */
	private static function instagram_quota_reached( $ig_user_id, $access_token ) {

		$logger = new Rop_Logger();

		$rate_limit_url = sprintf(
			'https://graph.facebook.com/%1$s/content_publishing_limit?fields=config,quota_usage&access_token=%2$s',
			$ig_user_id,
			$access_token
		);

		$quota_response = wp_remote_get(
			$rate_limit_url,
			array(
				'timeout' => 10,
			)
		);

		$quota_response_body = json_decode( wp_remote_retrieve_body( $quota_response ), true );

		if ( ! empty( $quota_response_body['data'][0] ) ) {

			// Details are always in the first array element
			$total_quota = (int) $quota_response_body['data'][0]['config']['quota_total'];
			$current_usage = (int) $quota_response_body['data'][0]['quota_usage'];

			return $current_usage >= $total_quota;

		} else {
			$logger->info( "Couldn't read 'data' key value of the Instagram quota response: " . print_r( $quota_response_body, true ) );
			return false;
		}

	}
}
