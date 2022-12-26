<?php
/**
 * The file for Email Notification.
 *
 * @since      1.1.7
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Email notification class.
 */
class Notification
{
	use Hooker, HelperFunctions;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/old_post_republished', 'send_email', 50 );
	}

	/**
	 * Send email notification.
	 *
	 * @param int $post_id  Post ID.
	 */
	public function send_email( $post_id )
	{
		$post = get_post( $post_id );
		$post_author = get_user_by( 'id', $post->post_author );
		$disable = $this->get_meta( $post->ID, '_wpar_disable_email' );

		$enable = $this->is_enabled( 'enable_email_notify' );
		$post_types = $this->get_data( 'email_post_types', [ 'post' ] );
		if ( ! in_array( $post->post_type, $post_types ) ) {
			return;
		}

		$recipients = [];
		$recipients = explode( ',', $this->get_data( 'email_recipients' ) );
		if ( $this->is_enabled( 'enable_post_author_email', true ) ) {
			if ( ! in_array( $post_author->data->user_email, $recipients ) ) {
				$recipients[] = $post_author->data->user_email;
			}
		}
		$subject = $this->replace_veriables( $this->get_data( 'email_subject' ), $post );
		$body = $this->replace_veriables( $this->get_data( 'email_message' ), $post, 'body' );
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		$headers = $this->do_filter( 'custom_email_headers', $headers );
	
		if ( $enable && ! $disable ) {
			wp_mail( $recipients, $subject, $body, $headers );
			// add eevent to log
			$this->do_action( 'insert_log', $post->ID, 'email' );
		}
	}

	/**
	 * Replace email variables.
	 *
	 * @param string  $text  Input data.
	 * @param object  $post  WP Post object.
	 * @param string  $type  Sting type. Default subject.
	 * 
	 * @return string
	 */
	private function replace_veriables( $text, $post, $type = 'subject' )
	{
		$text = stripslashes( $text );
		$text = str_replace( '%author_name%', get_the_author_meta( 'display_name', $post->post_author ), $text );
		$text = str_replace( '%post_title%', $post->post_title, $text ); 
		$text = str_replace( '%post_type%', get_post_type( $post->ID ), $text ); 
		$text = str_replace( '%post_link%', get_the_permalink( $post->ID ), $text ); 
		$text = str_replace( '%post_time%', $this->get_meta( $post->ID, '_wpar_original_pub_date' ), $text );
		$text = str_replace( '%republish_time%', $post->post_date, $text );
		$text = str_replace( '%site_name%', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $text ); 
		$text = str_replace( '%site_url%', get_bloginfo( 'url' ), $text );
		$text = str_replace( '%admin_email%', get_bloginfo( 'admin_email' ), $text );
		
		if ( $type == 'subject' ) {
            return stripslashes( strip_tags( $text ) );
		}

		return stripslashes( htmlspecialchars_decode( $text ) );
	}
}