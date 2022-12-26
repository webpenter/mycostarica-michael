<?php
/**
 * The file for Social Actions.
 *
 * @since      1.2.2
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Social;

use Wpar\Helpers\Ajax;
use Wpar\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Social Actions class.
 */
class SocialActions
{
	use Ajax, Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->ajax( 'process_view_social_template', 'view_template' );
		$this->ajax( 'process_save_social_template', 'set_template' );
		$this->ajax( 'process_social_account_update', 'update_account' );
		$this->ajax( 'process_delete_social_single', 'remove_account' );
		$this->ajax( 'process_delete_social_db', 'delete_db' );
		$this->action( 'admin_notices', 'admin_notice' );
	}

	/**
	 * Activate or Deactivate Single Social account on request.
	 */
	public function view_template()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['account_id'], $_POST['account_type'] ) ) {
			$key = sanitize_text_field( $_POST['account_id'] );
			$type = sanitize_text_field( $_POST['account_type'] );

            $data = unserialize( get_option( 'wpar_' . $type . '_accounts_db' ) );
			
			$template = false;
			if ( isset( $data[$key]['template'] ) ) {
				$template = $data[$key]['template'];
			}

			$this->success( [
				'template' => $template
			] );
		} else {
			$this->error();
		}
	}

	/**
	 * Activate or Deactivate Single Social account on request.
	 */
	public function set_template()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['account_id'], $_POST['account_type'], $_POST['template'] ) ) {
			$key = sanitize_text_field( $_POST['account_id'] );
			$type = sanitize_text_field( $_POST['account_type'] );
			$template = sanitize_textarea_field( $_POST['template'] );

            $data = unserialize( get_option( 'wpar_' . $type . '_accounts_db' ) );
			
			if ( isset( $data[$key] ) ) {
				$data[$key]['template'] = $template;
			}

			update_option( 'wpar_' . $type . '_accounts_db', maybe_serialize( $data ) );

			$this->success();
		} else {
			$this->error();
		}
	}

	/**
	 * Activate or Deactivate Single Social account on request.
	 */
	public function update_account()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['account_id'], $_POST['account_type'], $_POST['update_action'] ) ) {
			$key = sanitize_text_field( $_POST['account_id'] );
			$type = sanitize_text_field( $_POST['account_type'] );
			$action = sanitize_text_field( $_POST['update_action'] );

            $data = unserialize( get_option( 'wpar_' . $type . '_accounts_db' ) );
			
			if ( isset( $data[$key] ) ) {
				if ( $action == 'activate' ) {
					$data[$key]['status'] = true;
				} elseif ( $action == 'deactivate' ) {
					$data[$key]['status'] = false;
				}
			}

			update_option( 'wpar_' . $type . '_accounts_db', maybe_serialize( $data ) );

			$this->success( [
				'status' => $action
			] );
		} else {
			$this->error();
		}
	}

	/**
	 * Single Social account delete on request.
	 */
	public function remove_account()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['account_id'], $_POST['account_type'] ) ) {
			$key = sanitize_text_field( $_POST['account_id'] );
			$type = sanitize_text_field( $_POST['account_type'] );

            $data = unserialize( get_option( 'wpar_' . $type . '_accounts_db' ) );
			
			if ( isset( $data[$key] ) ) {
			    unset( $data[$key] );
			}

			update_option( 'wpar_' . $type . '_accounts_db', maybe_serialize( $data ) );

			$this->success( [
				'replace_html' => true
			] );
		} else {
			$this->error();
		}
	}

	/**
	 *Social profile database delete on request.
	 */
	public function delete_db()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['account_type'] ) ) {
			delete_option( 'wpar_' . sanitize_text_field( $_POST['account_type'] ) . '_accounts_db' );
			
			$this->success( [
				'replace_html' => true,
			] );
		} else {
            $this->error();
		}
	}

	/**
     * Process reset plugin settings
     */
	public function admin_notice()
	{
    	if ( get_transient( 'wpar_account_auth_success' ) !== false ) { ?>
			<div class="notice notice-success is-dismissible"><p><strong><?php printf( __( 'Success! Your %s Account(s) added successfully.', 'wp-auto-republish' ), ucwords( str_replace( [ '_', '-' ], ' ', get_transient( 'wpar_account_auth_success' ) ) ) ); ?></strong></p></div><?php 
		    delete_transient( 'wpar_account_auth_success' );
	    }
	}
}