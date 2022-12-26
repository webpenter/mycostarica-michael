<?php
/**
 * The file for Linkedin Share.
 *
 * @since      1.2.2
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Social;

use LinkedIn\Scope;
use LinkedIn\Client;
use Wpar\Helpers\Ajax;
use Wpar\Helpers\Hooker;
use LinkedIn\AccessToken;
use Wpar\Helpers\HelperFunctions;
use Wpar\Helpers\Premium\SocialHelpers;

defined( 'ABSPATH' ) || exit;

/**
 * Linkedin Share class.
 */
class LinkedinShare
{
	use Ajax, Hooker, HelperFunctions, SocialHelpers;

	/**
	 * Linkedin Callback URL.
	 *
	 * @var string
	 */
	private $callback_url = 'https://api.wpautorepublish.com/auth/linkedin';

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->ajax( 'generate_linkedin_auth_url', 'generate_auth_url' );
		$this->action( 'admin_post_wpar_add_linkedin_account', 'get_auth_data' );
		$this->action( 'wpar/old_post_republished', 'process_start', 30, 1 );
		$this->action( 'wpar/do_social_share', 'process_start', 5, 1 );
        $this->filter( 'wpar/display_linkedin_accounts', 'linkedin_accounts_list' );
	}

	/**
	 * Generate Linkedin Authentication URL.
	 */
	public function generate_auth_url()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['app_id'], $_POST['app_secret'] ) ) {
			try {
			    $client = $this->connection( $_POST['app_id'], $_POST['app_secret'] );
				$client->setState( base64_encode( trailingslashit( admin_url() ) ) );
				$client->setRedirectUrl( $this->callback_url );

                // define scope
                $scopes = [ 
					Scope::READ_LITE_PROFILE, 
				    Scope::READ_EMAIL_ADDRESS,
				    Scope::SHARE_AS_USER
				];
                
				$loginUrl = $client->getLoginUrl( $scopes );

                // save temp linkedin data
			    update_option( 'wpar_linkedin_temp_credentials', [
			        'app_id' => sanitize_text_field( $_POST['app_id'] ),
			        'app_secret' => sanitize_text_field( $_POST['app_secret'] )
			    ] );

	    	    $this->success( [
	    	    	'redirect_url' => $loginUrl
	            ] );
			} catch ( \Exception $e ) {
		    	$this->error( __( 'Error: Wrong App ID or App Secret or Check you Callback URL!', 'wp-auto-republish' ) );
			}
		} else {
			$this->error();
		}
	}

	/**
	 * Fetch and save Linkedin profile data to db.
	 */
	public function get_auth_data()
	{
		if ( isset( $_GET['auth_code'] ) ) {
			// get temporary credentials
			$temp_credentials = get_option( 'wpar_linkedin_temp_credentials' );

			$client = $this->connection( $temp_credentials['app_id'], $temp_credentials['app_secret'] );
			$client->setRedirectUrl( $this->callback_url );
			$accessToken = $client->getAccessToken( $_GET['auth_code'] );

			$profile = $client->get(
				'me',
				['fields' => 'id,firstName,lastName']
			);

			// get current user
			$current_user = wp_get_current_user();

			$args = [
				'id' => $profile['id'],
				'name' => $profile['firstName']['localized']['en_US'] . ' ' . $profile['lastName']['localized']['en_US'],
				'status' => true,
				'valid' => true,
				'template' => '',
				'type' => 'profile',
				'app_id' => $temp_credentials['app_id'],
				'app_secret' => $temp_credentials['app_secret'],
				'access_token' => $accessToken->getToken(),
				'timestamp' => current_time( 'timestamp', 0 ),
				'added_by' => $current_user->user_login
			];

			$this->update_db( $args, 'linkedin' );

			// delete temp linkedin data
			delete_option( 'wpar_linkedin_temp_credentials' );

			// set temporary transient for admin notice
		    set_transient( 'wpar_account_auth_success', 'linkedin' );

			wp_safe_redirect( add_query_arg( 'page', 'wp-auto-republish', admin_url( 'admin.php' ) ) );
			exit;
		}
	}

	/**
	 * Process API Interaction with Linkedin and Share Post.
	 * 
	 * @param string   $app_id      Linkedin App ID.
	 * @param string   $app_secret  Linkedin App Secret.
	 * @return object
	 */
	private function connection( $app_id, $app_secret )
	{
		$connection = new Client( $app_id, $app_secret );

		return $connection;
	}

    /**
	 * Get the list of all linkedin accounts.
	 * 
	 * @return string
	 */
	public function linkedin_accounts_list()
	{
		$accounts = get_option( 'wpar_linkedin_accounts_db' );
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
				    <td class="social-td" scope="row"><strong>' . __( 'Account Name', 'wp-auto-republish' ) . '</strong></td>
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

			$delete = '<a href="#" class="wpar-delete-social-single" data-selector="wpar-linkedin" data-account-type="linkedin" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Deleting...', 'wp-auto-republish' ) . '" data-notice="' . sprintf( __( 'It will delete this Linkedin Account: %s. Do you want to continue?', 'wp-auto-republish' ), $item['name'] ) . '">' . __( 'Delete', 'wp-auto-republish' ) . '</a>';

			$activate = '<a href="#" class="wpar-social-single-update" data-selector="wpar-linkedin" data-account-type="linkedin" data-action="wpar_process_social_account_update" data-update-action="activate" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Activating...', 'wp-auto-republish' ) . '" data-next="' . __( 'Enabled', 'wp-auto-republish' ) . '">' . __( 'Activate', 'wp-auto-republish' ) . '</a>';
			$deactivate = '<a href="#" class="wpar-social-single-update" data-selector="wpar-linkedin" data-account-type="linkedin" data-action="wpar_process_social_account_update" data-update-action="deactivate" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Deactivating...', 'wp-auto-republish' ) . '" data-next="' . __( 'Disabled', 'wp-auto-republish' ) . '">' . __( 'Deactivate', 'wp-auto-republish' ) . '</a>';

			$template = '<a href="#" class="wpar-template-social-single" data-selector="wpar-linkedin" data-account-type="linkedin" data-account-id="' . $item['id'] . '" data-charecter="1300">' . __( 'View / Edit Template', 'wp-auto-republish' ) . '</a>';

			$button = ( $item['status'] ) ? $deactivate : $activate;
			$button = ( $item['valid'] ) ? $button : '<span class="wpar-social-account-status expired">' . __( 'Token Expired', 'wp-auto-republish' ) . '</span>';

			$html .= '<tr valign="top" id="wpar-linkedin-' . $item['id'] . '" class="wpar-social-accounts">
			    <td class="social-td" scope="row" data-label="' . __( 'Timestamp', 'wp-auto-republish' ) . '">' . date_i18n( 'M j, Y @ ' . get_option( 'time_format' ), $item['timestamp'] ) . '</td>
			    <td class="social-td" scope="row" data-label="' . __( 'Account Name', 'wp-auto-republish' ) . '">' . $item['name'] . '</td>
			    <td class="social-td" scope="row" data-label="' . __( 'Added By', 'wp-auto-republish' ) . '">' . ucwords( $item['added_by'] ) . '</a></td>
				<td class="social-td wpar-linkedin-status-' . $item['id'] . '" scope="row" data-label="' . __( 'Status', 'wp-auto-republish' ) . '"><span class="wpar-social-account-status ' . $css_class . '">' . $enabled . '</span></td>
			    <td class="social-td" scope="row" data-label="' . __( 'Template', 'wp-auto-republish' ) . '">' . $template . '</td>
				<td class="social-td" scope="row" data-label="' . __( 'Action', 'wp-auto-republish' ) . '"><span class="wpar-linkedin-action-btn-' . $item['id'] . '">' . $button . '</span> | ' . $delete . '</td>
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
		$accounts = get_option( 'wpar_linkedin_accounts_db' );
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
					'access_token' => $item['access_token'],
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
	 * Start Linkedin share process.
	 * 
	 * @param int $post_id  WP Post ID.
	 */
	public function process_start( $post_id )
	{
		$enable = $this->is_enabled( 'linkedin_enable' );
		$accounts = $this->get_activated_accounts();
		if ( $enable && $accounts !== false ) {
		    foreach ( $accounts as $account_id => $key ) {
		    	$this->process_share( $key['app_id'], $key['app_secret'], $key['access_token'], $key['template'], $account_id, $post_id );
		    }
		}
	}

	/**
	 * Process API Interaction with Linkedin and Share Post.
	 * 
	 * @param string $app_id       Linkedin App ID.
	 * @param string $app_secret   Linkedin App Secret.
	 * @param int    $access_token Linkedin Auth Token.
	 * @param int    $account_id   Linkedin Account ID.
	 * @param int    $post_id      WP Post ID.
	 */
	private function process_share( $app_id, $app_secret, $access_token, $account_template, $account_id, $post_id )
	{
		$post = get_post( $post_id );
		$post_types = $this->get_data( 'linkedin_post_types_display', [ 'post' ] );
		$taxonomies = $this->get_data( 'linkedin_social_taxonomy' );
		$post_as = $this->get_data( 'linkedin_post_as', 'link_status' );
		$template = $this->get_data( 'linkedin_template' );
		$content_source = $this->get_data( 'linkedin_content_source' );
		$disable_linkedin_share = $this->get_meta( $post->ID, '_wpar_disable_linkedin_share' );

		if ( ! in_array( $post->post_type, $post_types ) || $disable_linkedin_share == 'yes' ) {
			return;
		}

		if ( ! empty( $account_template ) ) {
			$template = $account_template;
		}

		try {
			$client = $this->connection( $app_id, $app_secret );
		    $client->setAccessToken( $access_token );

			$formated_text = $this->social_template( $template, $post->ID, $this->get_hashtags( $taxonomies, $post->ID ), $content_source, $this->do_filter( 'linkedin_content_limit', mt_rand( 1200, 1300 ) ) );
			if ( $post_as == 'status' ) {
				// send to api
				$result = $this->text_post( $client, $account_id, $formated_text );
            } else if ( $post_as == 'link_status' ) {
                $post_details = get_post( $post_id );
                if ( $content_source == 'excerpt' && has_excerpt( $post_details->ID ) ) {
                    $desc = wp_strip_all_tags( strip_shortcodes( $post_details->post_excerpt ) );
                } else {
                    $desc = wp_strip_all_tags( strip_shortcodes( $post_details->post_content ) );
                }
                // send to api
                $result = $this->link_status_post( $client, $account_id, $formated_text, get_the_title( $post_id ), wp_trim_words( $desc, 10, '...' ), get_permalink( $post_id ) );
            }
            
			//error_log( print_r( $result, true ) );

			if ( isset( $result['id'] ) ) {
			    $this->set_post_metadata( $post_id, [
			    	'share_id' => $result['id'],
			    	'publish_date' => current_time( 'timestamp', 0 ),
			    	'account_id' => $account_id
			    ], 'linkedin' );

				// insert log entry
			    $this->do_action( 'insert_log', $post_id, 'linkedin' );
			} else if ( isset( $result['serviceErrorCode'] ) ) {
				if ( $result['serviceErrorCode'] == 401 ) {
					$this->expire_token( $account_id, 'linkedin' );
				}
				// insert log entry
			    $this->do_action( 'insert_log', $post_id, 'linkedin', false, sprintf( __( 'Error Code: %s', 'wp-auto-republish' ), $result['serviceErrorCode'] . ' - ' . $result['message'] ), false, 'dashicons-no-alt' );
			}
		} catch ( \Exception $e ) {
			// insert log entry
			$this->do_action( 'insert_log', $post_id, 'linkedin', false, $e->getMessage(), false, 'dashicons-no-alt' );
		}
	}

	/**
	 * Process API Interaction with Linkedin and Share Post.
	 * 
	 * @param string $client       Linkedin Client Object.
	 * @param string $person_id    Linkedin Profile ID.
	 * @param string $message      Linkedin Share Message.
	 * @param string $visibility   Linedin Post visibility.
	 * @return array
	 */
	private function text_post( $client, $person_id, $message, $visibility = 'PUBLIC' )
    {
		$share = $client->post(                 
			'ugcPosts',                         
			[                                   
				'author' => 'urn:li:person:' . $person_id,
				'lifecycleState' => 'PUBLISHED',
				'specificContent' => [          
					'com.linkedin.ugc.ShareContent' => [
						'shareCommentary' => [
							'text' => html_entity_decode( $message )
						],
						'shareMediaCategory'=> 'NONE',
					]
				],
				'visibility' => [
					'com.linkedin.ugc.MemberNetworkVisibility' => $visibility
				]
			]
		);
        
        return $share;
    }

	/**
	 * Process API Interaction with Linkedin and Share Post.
	 * 
	 * @param string $client       Linkedin Client Object.
	 * @param string $person_id    Linkedin Profile ID.
	 * @param string $message      Linkedin Share Message.
	 * @param string $link_title   Post Title.
	 * @param string $link_desc    Post Description.
	 * @param string $link_url     WP Post URL.
	 * @param string $visibility   Linedin Post visibility.
	 * @return array
	 */
	private function link_status_post( $client, $person_id, $message, $link_title, $link_desc, $link_url, $visibility = 'PUBLIC' )
    {
		$share = $client->post(                 
			'ugcPosts',                         
			[                                   
				'author' => 'urn:li:person:' . $person_id,
				'lifecycleState' => 'PUBLISHED',
				'specificContent' => [          
					'com.linkedin.ugc.ShareContent' => [
						'shareCommentary' => [
							'text' => html_entity_decode( $message )
						],
						'shareMediaCategory' => 'ARTICLE',
						'media' => [
							[
								'status' => 'READY',
								'description' => [
									'text' => substr( $link_desc, 0, 200 )
								],
								'originalUrl' => $link_url,
								'title' => [
									'text' => html_entity_decode( $link_title )
								]
							]
						]
					]
				],
				'visibility' => [
					'com.linkedin.ugc.MemberNetworkVisibility' => $visibility
				]
			]
		);

		return $share;
    }
}