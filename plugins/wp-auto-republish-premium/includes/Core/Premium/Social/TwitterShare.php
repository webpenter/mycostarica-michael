<?php
/**
 * The file for Twitter Share.
 *
 * @since      1.2.2
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Social;

use Wpar\Helpers\Ajax;
use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;
use Abraham\TwitterOAuth\TwitterOAuth;
use Wpar\Helpers\Premium\SocialHelpers;

defined( 'ABSPATH' ) || exit;

/**
 * Twitter Share class.
 */
class TwitterShare
{
	use Ajax, Hooker, HelperFunctions, SocialHelpers;

	/**
	 * Twitter Callback URL.
	 *
	 * @var string
	 */
	private $callback_url = 'https://api.wpautorepublish.com/auth/twitter';

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->ajax( 'generate_twitter_auth_url', 'generate_auth_url' );
		$this->action( 'admin_post_wpar_add_twitter_account', 'get_auth_data' );
		$this->action( 'wpar/old_post_republished', 'process_start', 30, 1 );
		$this->action( 'wpar/do_social_share', 'process_start', 5, 1 );
        $this->filter( 'wpar/display_twitter_accounts', 'twitter_accounts_list' );
	}

	/**
	 * Generate Twitter Authentication URL.
	 */
	public function generate_auth_url()
	{
		// security check
		$this->verify_nonce();

		$callback_url = add_query_arg( 'state', base64_encode( trailingslashit( admin_url() ) ), $this->callback_url );

		if ( isset( $_POST['app_id'], $_POST['app_secret'] ) ) {
			try {
			    $connection = $this->connection( $_POST['app_id'], $_POST['app_secret'] );
	    	    $request_token = $connection->oauth( 'oauth/request_token', [ 'oauth_callback' => esc_url( $callback_url ) ] );
	    
			    if ( isset( $request_token['oauth_token'], $request_token['oauth_token_secret'] ) ) {
			    	// save temp twitter data
			        update_option( 'wpar_twitter_temp_credentials', [
			        	'app_id' => sanitize_text_field( $_POST['app_id'] ),
			        	'app_secret' => sanitize_text_field( $_POST['app_secret'] )
			        ] );
			    	
			    	// get Auth URL
	    	    	$url = $connection->url( 'oauth/authorize', [ 'oauth_token' => $request_token['oauth_token'] ] );
	    	    	
	    	    	$this->success( [
	    	    		'redirect_url' => $url
	            	] );
	            } else {
	            	$this->error();
			    }
			} catch ( \Exception $e ) {
		    	$this->error( __( 'Error: Wrong App ID or App Secret or Check you Callback URL!', 'wp-auto-republish' ) );
			}
		} else {
			$this->error();
		}
	}

	/**
	 * Fetch and save Twitter profile data to db.
	 */
	public function get_auth_data()
	{
		if ( isset( $_GET['oauth_token'], $_GET['oauth_verifier'] ) ) {
			// get temporary credentials
			$temp_credentials = get_option( 'wpar_twitter_temp_credentials' );

		    // generate access token to fetch profile data
			$access_token = $this->connection( $temp_credentials['app_id'], $temp_credentials['app_secret'], $_GET['oauth_token'], $_GET['oauth_verifier'] )->oauth( 'oauth/access_token', [ 'oauth_verifier' => $_GET['oauth_verifier'] ] );
			
			// get user data using access tokens
			$content = $this->connection( $temp_credentials['app_id'], $temp_credentials['app_secret'], $access_token['oauth_token'], $access_token['oauth_token_secret'] )->get( 'account/verify_credentials' );
			
			// get current user
			$current_user = wp_get_current_user();

			$args = [
				'id' => $content->id,
				'name' => $content->name,
				'user_name' => $content->screen_name,
				'status' => true,
				'valid' => true,
				'template' => '',
				'app_id' => $temp_credentials['app_id'],
				'app_secret' => $temp_credentials['app_secret'],
				'oauth_token' => $access_token['oauth_token'],
				'oauth_token_secret' => $access_token['oauth_token_secret'],
				'timestamp' => current_time( 'timestamp', 0 ),
				'added_by' => $current_user->user_login
			];

			$this->update_db( $args, 'twitter' );

			// delete temp twitter data
			delete_option( 'wpar_twitter_temp_credentials' );

			// set temporary transient for admin notice
		    set_transient( 'wpar_account_auth_success', 'twitter' );

			wp_safe_redirect( add_query_arg( 'page', 'wp-auto-republish', admin_url( 'admin.php' ) ) );
			exit;
		}
	}

	/**
	 * Process API Interaction with Twitter and Share Post.
	 * 
	 * @param string      $app_id               Twitter App ID.
	 * @param string      $app_secret           Twitter App Secret.
	 * @param string|null $oauth_token Twitter  Twitter Auth Token.
	 * @param string      $oauth_token_secret   Twitter Auth Token Secret.
	 * @return object
	 */
	private function connection( $app_id, $app_secret, $oauth_token = null, $oauth_token_secret = null )
	{
		$connection = new TwitterOAuth( $app_id, $app_secret, $oauth_token, $oauth_token_secret );

		return $connection;
	}

    /**
	 * Get the list of all twitter accounts.
	 * 
	 * @return string
	 */
	public function twitter_accounts_list()
	{
		$accounts = get_option( 'wpar_twitter_accounts_db' );
		if ( $accounts === false ) {
            return false;
		}

		$accounts = unserialize( $accounts );
		if ( empty( $accounts ) ) {
            return false;
		}
		
		$html = '<table class="form-table">
			<thead>
				<tr valign="top">
				    <td class="social-td"><strong>' . __( 'Timestamp', 'wp-auto-republish' ) . '</strong></td>
				    <td class="social-td" scope="row"><strong>' . __( 'Name', 'wp-auto-republish' ) . '</strong></td>
				    <td class="social-td" scope="row"><strong>' . __( 'Handle', 'wp-auto-republish' ) . '</strong></td>
				    <td class="social-td" scope="row"><strong>' . __( 'Added By', 'wp-auto-republish' ) . '</strong></td>
					<td class="social-td" scope="row"><strong>' . __( 'Status', 'wp-auto-republish' ) . '</strong></td>
					<td class="social-td" scope="row"><strong>' . __( 'Template', 'wp-auto-republish' ) . '</strong></td>
				    <td class="social-td" scope="row"><strong>' . __( 'Action', 'wp-auto-republish' ) . '</strong></td>
				</tr>
			</thead>
			<tbody>';

		foreach ( $accounts as $items => $item ) {
			$enabled = ( $item['status'] ) ? __( 'Enabled', 'wp-auto-republish' ) : __( 'Disabled', 'wp-auto-republish' );
			$css_class = ( $item['status'] ) ? 'enabled' : 'disabled';

			$delete = '<a href="#" class="wpar-delete-social-single" data-selector="wpar-twitter" data-account-type="twitter" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Deleting...', 'wp-auto-republish' ) . '" data-notice="' . sprintf( __( 'It will delete this Twitter Account: %s. Do you want to continue?', 'wp-auto-republish' ), $item['name'] . ' (@' . $item['user_name'] . ')' ) . '">' . __( 'Delete', 'wp-auto-republish' ) . '</a>';

			$activate = '<a href="#" class="wpar-social-single-update" data-selector="wpar-twitter" data-account-type="twitter" data-action="wpar_process_social_account_update" data-update-action="activate" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Activating...', 'wp-auto-republish' ) . '" data-next="' . __( 'Enabled', 'wp-auto-republish' ) . '">' . __( 'Activate', 'wp-auto-republish' ) . '</a>';
			$deactivate = '<a href="#" class="wpar-social-single-update" data-selector="wpar-twitter" data-account-type="twitter" data-action="wpar_process_social_account_update" data-update-action="deactivate" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Deactivating...', 'wp-auto-republish' ) . '" data-next="' . __( 'Disabled', 'wp-auto-republish' ) . '">' . __( 'Deactivate', 'wp-auto-republish' ) . '</a>';

			$template = '<a href="#" class="wpar-template-social-single" data-selector="wpar-twitter" data-account-type="twitter" data-account-id="' . $item['id'] . '" data-charecter="280">' . __( 'View / Edit Template', 'wp-auto-republish' ) . '</a>';

			$button = ( $item['status'] ) ? $deactivate : $activate;
			$button = ( $item['valid'] ) ? $button : '<span class="wpar-social-account-status expired">' . __( 'Token Expired', 'wp-auto-republish' ) . '</span>';

			$html .= '<tr valign="top" id="wpar-twitter-' . $item['id'] . '" class="wpar-social-accounts">
			    <td class="social-td" scope="row" data-label="' . __( 'Timestamp', 'wp-auto-republish' ) . '">' . date_i18n( 'M j, Y @ ' . get_option( 'time_format' ), $item['timestamp'] ) . '</td>
			    <td class="social-td" scope="row" data-label="' . __( 'Name', 'wp-auto-republish' ) . '">' . $item['name'] . '</td>
			    <td class="social-td" scope="row" data-label="' . __( 'Handle', 'wp-auto-republish' ) . '"><a href="https://twitter.com/' . $item['user_name'] . '" target="_blank">@' . $item['user_name'] . '</a></td>
				<td class="social-td" scope="row" data-label="' . __( 'Added By', 'wp-auto-republish' ) . '">' . ucwords( $item['added_by'] ) . '</a></td>
				<td class="social-td wpar-twitter-status-' . $item['id'] . '" scope="row" data-label="' . __( 'Status', 'wp-auto-republish' ) . '"><span class="wpar-social-account-status ' . $css_class . '">' . $enabled . '</span></td>
			    <td class="social-td" scope="row" data-label="' . __( 'Template', 'wp-auto-republish' ) . '">' . $template . '</td>
				<td class="social-td" scope="row" data-label="' . __( 'Action', 'wp-auto-republish' ) . '"><span class="wpar-twitter-action-btn-' . $item['id'] . '">' . $button . '</span> | ' . $delete . '</td>
		    </tr>';
	    }

		$html .= '</tbody>
		</table>';
		
		return $html;
	}

    /**
	 * Get Auth Tokens & Secret of all activated accounts.
	 * 
	 * @return bool|array
	 */
	private function get_activated_accounts()
	{
		$accounts = get_option( 'wpar_twitter_accounts_db' );
		if ( $accounts === false ) {
            return false;
		}

		$accounts = unserialize( $accounts );
		if ( empty( $accounts ) ) {
            return false;
		}

		$credentials = [];
		foreach ( $accounts as $items => $item ) {
			if ( $item['status'] ) {
				$credentials[$item['id']] = [
					'app_id' => $item['app_id'],
				    'app_secret' => $item['app_secret'],
					'auth_token' => $item['oauth_token'], 
					'auth_token_secret' => $item['oauth_token_secret'],
					'template' => $item['template']
				];
			}
		}

		if ( empty( $credentials ) ) {
            $credentials = false;
		}

		return $credentials;
	}

	/**
	 * Start Twitter share process.
	 * 
	 * @param int $post_id  WP Post ID.
	 */
	public function process_start( $post_id )
	{
		$enable = $this->is_enabled( 'twitter_enable' );
		$accounts = $this->get_activated_accounts();
		if ( $enable && $accounts !== false ) {
		    foreach ( $accounts as $account_id => $key ) {
		    	$this->process_share( $key['app_id'], $key['app_secret'], $key['auth_token'], $key['auth_token_secret'], $key['template'], $account_id, $post_id );
		    }
		}
	}

	/**
	 * Process API Interaction with Twitter and Share Post.
	 * 
	 * @param string $app_id      Twitter App ID.
	 * @param string $app_secret  Twitter App Secret.
	 * @param int    $token       Twitter Auth Token.
	 * @param int    $secret      Twitter Auth Token Secret.
	 * @param int    $account_id  Twitter Account ID.
	 * @param int    $post_id     WP Post ID.
	 */
	private function process_share( $app_id, $app_secret, $token, $secret, $account_template, $account_id, $post_id )
	{
		$post = get_post( $post_id );
		$post_types = $this->get_data( 'twitter_post_types_display', [ 'post' ] );
		$taxonomies = $this->get_data( 'twitter_social_taxonomy' );
		$add_thumbnail = $this->get_data( 'twitter_thumbnail', 'yes' );
		$template = $this->get_data( 'twitter_template' );
		$content_source = $this->get_data( 'twitter_content_source' );
		$disable_twitter_share = $this->get_meta( $post->ID, '_wpar_disable_twitter_share' );

		if ( ! in_array( $post->post_type, $post_types ) || $disable_twitter_share == 'yes' ) {
			return;
		}

		if ( ! empty( $account_template ) ) {
			$template = $account_template;
		}

		$args = [
            'status' => $this->social_template( $template, $post->ID, $this->get_hashtags( $taxonomies, $post->ID ), $content_source, $this->do_filter( 'twitter_content_limit', mt_rand( 250, 280 ) ) )
		];

		try {
		    // generate access token to fetch profile data
			$connection = $this->connection( $app_id, $app_secret, $token, $secret );

			if ( has_post_thumbnail( $post->ID ) && $add_thumbnail =='yes' ) {
				// upload media
				$media = $connection->upload( 'media/upload', [ 'media' => get_attached_file( get_post_thumbnail_id( $post->ID ) ) ] );

				if ( isset( $media->media_id_string ) && is_numeric( $media->media_id_string ) ) {
				    $args['media_ids'] = $media->media_id_string;
		        }
			}

			$result = $connection->post( 'statuses/update', $args );
			
			//error_log( print_r( $result, true ) );
			//error_log( print_r( $connection, true ) );
			
			if ( $connection->getLastHttpCode() == 200 ) {
				$this->set_post_metadata( $post_id, [
					'share_id' => $result->id,
					'publish_date' => current_time( 'timestamp', 0 ),
					'account_id' => $account_id
				], 'twitter' );

				// insert log entry
		    	$this->do_action( 'insert_log', $post_id, 'twitter' );
		    } else {
		    	if ( $result->errors[0]->code == 89 ) {
					$this->expire_token( $account_id, 'twitter' );
				}

				// insert log entry
				$this->do_action( 'insert_log', $post_id, 'twitter', false, sprintf( __( 'Twitter API Connection Problem. Reason: %1$s Error Code: %2$s', 'wp-auto-republish' ), ucwords( $result->errors[0]->message ), $result->errors[0]->code ), false, 'dashicons-no-alt' );
            }
		} catch ( \Exception $e ) {
			// insert log entry
			$this->do_action( 'insert_log', $post_id, 'twitter', false, $e->getMessage(), false, 'dashicons-no-alt' );
		}
	}
}