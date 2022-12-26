<?php
/**
 * The file for Facebook Share.
 *
 * @since      1.2.2
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Social;

use Facebook\Facebook;
use Wpar\Helpers\Ajax;
use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;
use Wpar\Helpers\Premium\SocialHelpers;

defined( 'ABSPATH' ) || exit;

/**
 * Email notification class.
 */
class FacebookShare
{
	use Ajax, Hooker, HelperFunctions, SocialHelpers;

	/**
	 * Facebook Callback URL.
	 *
	 * @var string
	 */
	private $callback_url = 'https://api.wpautorepublish.com/auth/facebook';

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->ajax( 'generate_facebook_auth_url', 'generate_auth_url' );
		$this->action( 'admin_post_wpar_add_facebook_account', 'get_auth_data' );
		$this->action( 'wpar/old_post_republished', 'process_start', 30, 1 );
		$this->action( 'wpar/do_social_share', 'process_start', 5, 1 );
		$this->filter( 'wpar/display_facebook_accounts', 'facebook_accounts_list' );
		$this->filter( 'language_attributes', 'opengraph_doctype' );
		$this->action( 'wp_head', 'meta_head', 5 );
	}

	/**
	 * Generate Facebook Authentication URL.
	 */
	public function generate_auth_url()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['app_id'], $_POST['app_secret'] ) ) {
			try {
			    $fb = $this->connection( $_POST['app_id'], $_POST['app_secret'] );
    
			    // save temp facebook data
			    update_option( 'wpar_facebook_temp_credentials', [
			    	'app_id' => sanitize_text_field( $_POST['app_id'] ),
			    	'app_secret' => sanitize_text_field( $_POST['app_secret'] )
			    ] );
    
			    // initiate facebook redirect login helper
				$helper = $fb->getRedirectLoginHelper();
    
                // required permissions
			    $permissions = [ 'pages_show_list', 'publish_to_groups', 'pages_read_engagement', 'pages_manage_posts' ];
    
			    // generate login URL
			    $login_url = $helper->getLoginUrl( $this->callback_url, $permissions );
			    $login_url = add_query_arg( 'state', base64_encode( esc_url( trailingslashit( admin_url() ) ) ), remove_query_arg( 'state', $login_url ) );

			    $this->success( [
			    	'redirect_url' => $login_url
			    ] );
			} catch ( \Exception $e ) {
		    	$this->error( __( 'Error: Wrong App ID or App Secret!', 'wp-auto-republish' ) );
			}
		} else {
			$this->error();
		}
	}

	/**
	 * Fetch and save Facebook profile data to db.
	 */
	public function get_auth_data()
	{
		if ( isset( $_GET['auth_code'] ) ) {
			// get temporary credentials
			$temp_credentials = get_option( 'wpar_facebook_temp_credentials' );

			$fb = $this->connection( $temp_credentials['app_id'], $temp_credentials['app_secret'] );

			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $fb->getOAuth2Client();

			$temp_access_token = $oAuth2Client->getAccessTokenFromCode( $_GET['auth_code'], $this->callback_url );
			$access_token = $oAuth2Client->getLongLivedAccessToken( $temp_access_token );

			// get required data
			$page_info = $this->call_api( '/me?fields=accounts{name,access_token,picture}&limit=100', $temp_access_token );
			$group_info = $this->call_api( '/me/groups?fields=id,name,administrator,picture&limit=100', $temp_access_token );

			// get current user
			$current_user = wp_get_current_user();

			if ( is_array( $page_info->accounts->data ) && count( $page_info->accounts->data ) > 0 ) {
				foreach ( $page_info->accounts->data as $page_item ) {
					$args = [
						'id' => $page_item->id,
						'app_id' => $temp_credentials['app_id'],
						'app_secret' => $temp_credentials['app_secret'],
						'name' => $page_item->name,
						'type' => 'page',
						'status' => true,
						'valid' => true,
						'template' => '',
						'access_token' => $page_item->access_token,
						'timestamp' => current_time( 'timestamp', 0 ),
						'added_by' => $current_user->user_login
					];

					$this->update_db( $args, 'facebook' );
				}
			}

			if ( is_array( $group_info->data ) && count( $group_info->data ) > 0 ) {
				foreach ( $group_info->data as $group ) {
					if ( $group->administrator === true ) {
						$args = [
							'id' => $group->id,
							'app_id' => $temp_credentials['app_id'],
						    'app_secret' => $temp_credentials['app_secret'],
							'name' => $group->name,
							'type' => 'group',
							'status' => true,
							'valid' => true,
							'template' => '',
							'access_token' => $access_token,
							'timestamp' => current_time( 'timestamp', 0 ),
							'added_by' => $current_user->user_login
						];

						$this->update_db( $args, 'facebook' );
					}
				}
			}

			// delete temp facebook data
			delete_option( 'wpar_facebook_temp_credentials' );

			// set temporary transient for admin notice
		    set_transient( 'wpar_account_auth_success', 'facebook' );

			wp_safe_redirect( add_query_arg( 'page', 'wp-auto-republish', admin_url( 'admin.php' ) ) );
			exit;
		}
	}

	/**
	 * Process API Interaction with Facebook and Share Post.
	 * 
	 * @param string  $app_id      Facebook App ID.
	 * @param string  $app_secret  Facebook App Secret.
	 * @return object
	 */
	private function connection( $app_id, $app_secret )
	{
		$connection = new Facebook( [
			'app_id' => $app_id,
			'app_secret' => $app_secret,
			'default_graph_version' => 'v9.0'
		] );

		return $connection;
	}
	
	/**
     * Facebook User Details
	 * 
	 * @param string  $fields       Facebook API Profile Fields.
	 * @param string  $access_token Facebook Access Token.
	 * @return object
     */
    public function call_api( $fields, $access_token )
    {
        $graph_url = 'https://graph.facebook.com' . $fields . '&access_token=' . $access_token;

        $response = wp_remote_get( $graph_url );
        if ( is_wp_error( $response ) ) {
            return false;
        } else {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body );
			
			return $data;
		}
		
        return null;
    }

    /**
	 * Get the list of all facebook accounts.
	 * 
	 * @return string
	 */
	public function facebook_accounts_list()
	{
		$accounts = get_option( 'wpar_facebook_accounts_db' );
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
				    <td class="social-td" scope="row"><strong>' . __( 'Type', 'wp-auto-republish' ) . '</strong></td>
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

			$delete = '<a href="#" class="wpar-delete-social-single" data-selector="wpar-facebook" data-account-type="facebook" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Deleting...', 'wp-auto-republish' ) . '" data-notice="' . sprintf( __( 'It will delete this Facebook %1$s: %2$s. Do you want to continue?', 'wp-auto-republish' ), ucfirst( $item['type'] ), $item['name'] ) . '">' . __( 'Delete', 'wp-auto-republish' ) . '</a>';

			$activate = '<a href="#" class="wpar-social-single-update" data-selector="wpar-facebook" data-account-type="facebook" data-action="wpar_process_social_account_update" data-update-action="activate" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Activating...', 'wp-auto-republish' ) . '" data-next="' . __( 'Enabled', 'wp-auto-republish' ) . '">' . __( 'Activate', 'wp-auto-republish' ) . '</a>';
			$deactivate = '<a href="#" class="wpar-social-single-update" data-selector="wpar-facebook" data-account-type="facebook" data-action="wpar_process_social_account_update" data-update-action="deactivate" data-account-id="' . $item['id'] . '" data-processing="' . __( 'Deactivating...', 'wp-auto-republish' ) . '" data-next="' . __( 'Disabled', 'wp-auto-republish' ) . '">' . __( 'Deactivate', 'wp-auto-republish' ) . '</a>';

			$template = '<a href="#" class="wpar-template-social-single" data-selector="wpar-facebook" data-account-type="facebook" data-account-id="' . $item['id'] . '" data-charecter="63206">' . __( 'View / Edit Template', 'wp-auto-republish' ) . '</a>';

			$button = ( $item['status'] ) ? $deactivate : $activate;
			$button = ( $item['valid'] ) ? $button : '<span class="wpar-social-account-status expired">' . __( 'Token Expired', 'wp-auto-republish' ) . '</span>';

			$html .= '<tr valign="top" id="wpar-facebook-' . $item['id'] . '" class="wpar-social-accounts">
			    <td class="social-td" scope="row" data-label="' . __( 'Timestamp', 'wp-auto-republish' ) . '">' . date_i18n( 'M j, Y @ ' . get_option( 'time_format' ), $item['timestamp'] ) . '</td>
			    <td class="social-td" scope="row" data-label="' . __( 'Account Name', 'wp-auto-republish' ) . '">' . $item['name'] . '</td>
			    <td class="social-td" scope="row" data-label="' . __( 'Type', 'wp-auto-republish' ) . '">' . ucfirst( $item['type'] ) . '</td>
				<td class="social-td" scope="row" data-label="' . __( 'Added By', 'wp-auto-republish' ) . '">' . ucwords( $item['added_by'] ) . '</a></td>
				<td class="social-td wpar-facebook-status-' . $item['id'] . '" scope="row" data-label="' . __( 'Status', 'wp-auto-republish' ) . '"><span class="wpar-social-account-status ' . $css_class . '">' . $enabled . '</span></td>
			    <td class="social-td" scope="row" data-label="' . __( 'Template', 'wp-auto-republish' ) . '">' . $template . '</td>
				<td class="social-td" scope="row" data-label="' . __( 'Action', 'wp-auto-republish' ) . '"><span class="wpar-facebook-action-btn-' . $item['id'] . '">' . $button . '</span> | ' . $delete . '</td>
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
		$accounts = get_option( 'wpar_facebook_accounts_db' );
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
					'account_type' => $item['type'],
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
	 * Start facebook share process.
	 * 
	 * @param int $post_id  WP Post ID.
	 */
	public function process_start( $post_id )
	{
		$enable = $this->is_enabled( 'facebook_enable' );
		$accounts = $this->get_activated_accounts();
		if ( $enable && $accounts !== false ) {
		    foreach ( $accounts as $account_id => $key ) {
		    	$this->process_share( $key['app_id'], $key['app_secret'], $key['access_token'], $key['account_type'], $key['template'], $account_id, $post_id );
		    }
		}
	}

	/**
	 * Process API Interaction with Facebook and Share Post.
	 * 
	 * @param string $app_id       Facebook App ID.
	 * @param string $app_secret   Facebook App Secret.
	 * @param int    $access_token Facebook Auth Token.
	 * @param int    $type         Facebook Account Type.
	 * @param int    $account_id   Facebook Account ID.
	 * @param int    $post_id      WP Post ID.
	 */
	private function process_share( $app_id, $app_secret, $access_token, $type, $account_template, $account_id, $post_id )
	{
		$post = get_post( $post_id );
		$post_types = $this->get_data( 'facebook_post_types_display', [ 'post' ] );
		$taxonomies = $this->get_data( 'facebook_social_taxonomy' );
		$post_as = $this->get_data( 'facebook_post_as', 'link_status' );
		$template = $this->get_data( 'facebook_template' );
		$content_source = $this->get_data( 'facebook_content_source' );
		$disable_facebook_share = $this->get_meta( $post->ID, '_wpar_disable_facebook_share' );
		
		if ( ! in_array( $post->post_type, $post_types ) || $disable_facebook_share == 'yes' ) {
			return;
		}

		if ( ! empty( $account_template ) ) {
			$template = $account_template;
		}

		$content = $this->social_template( $template, $post->ID, $this->get_hashtags( $taxonomies, $post->ID ), $content_source, $this->do_filter( 'facebook_content_limit', mt_rand( 6000, 63206 ) ) );
		if ( $post_as == 'link' ) {
			$data = [
                'link' => esc_url( get_permalink( $post->ID ) ),
            ];
		} elseif ( $post_as == 'status' ) {
			$data = [
				'message' => $content
			];
		} elseif ( $post_as == 'link_status' ) {
			$data = [
				'message' => $content,
				'link' => esc_url( get_permalink( $post->ID ) ),
			];
		}

		$fb = $this->connection( $app_id, $app_secret );

		// page api
        if ( $type == 'page' ) {
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->post( '/' . $account_id . '/feed', $data, $access_token );
                $isError = $response->isError();
                if ( $isError == false ) {
					$graphNode = $response->getGraphNode();
					$this->set_post_metadata( $post_id, [
						'share_id' => $graphNode['id'],
						'publish_date' => current_time( 'timestamp', 0 ),
						'account_id' => $account_id
					], 'facebook' );

					// insert log entry
					$this->do_action( 'insert_log', $post_id, 'facebook_page' );
                }
            } catch ( \Facebook\Exceptions\FacebookResponseException $e ) {
				if ( $e->getCode() == 190 ) {
					$this->expire_token( $account_id, 'facebook' );
				}
                // insert log entry
			    $this->do_action( 'insert_log', $post_id, 'facebook_page', false, $e->getMessage(), false, 'dashicons-no-alt' );
            } catch ( \Facebook\Exceptions\FacebookSDKException $e ) {
				if ( $e->getCode() == 190 ) {
					$this->expire_token( $account_id, 'facebook' );
				}
                // insert log entry
			    $this->do_action( 'insert_log', $post_id, 'facebook_page', false, $e->getMessage(), false, 'dashicons-no-alt' );
            }
		}
		
		// group api
        if ( $type == 'group' ) {
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $fb->post( '/' . $account_id . '/feed', $data, $access_token );
                $isError = $response->isError();
                if ( $isError == false ) {
					$graphNode = $response->getGraphNode();
					$this->set_post_metadata( $post_id, [
						'share_id' => $graphNode['id'],
						'publish_date' => current_time( 'timestamp', 0 ),
						'account_id' => $account_id
					], 'facebook' );

					// insert log entry
					$this->do_action( 'insert_log', $post_id, 'facebook_group' );
				}
            } catch ( \Facebook\Exceptions\FacebookResponseException $e ) {
				if ( $e->getCode() == 190 ) {
					$this->expire_token( $account_id, 'facebook' );
				}
                // insert log entry
			    $this->do_action( 'insert_log', $post_id, 'facebook_group', false, $e->getMessage(), false, 'dashicons-no-alt' );
            } catch ( \Facebook\Exceptions\FacebookSDKException $e ) {
				if ( $e->getCode() == 190 ) {
					$this->expire_token( $account_id, 'facebook' );
				}
                // insert log entry
			    $this->do_action( 'insert_log', $post_id, 'facebook_group', false, $e->getMessage(), false, 'dashicons-no-alt' );
            }
        }
	}

	/**
	 * Add Open Graph Meta Info
	 * 
	 * @param string $output  Default language attributes.
	 * @return string
	 */
	public function opengraph_doctype( $output )
    {
		if ( ! $this->is_enabled( 'facebook_og_tag' ) ) {
			return $output;
		}

        return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
    }

	/**
	 * Add Open Graph Meta Info
	 */
    public function meta_head()
    {
        if ( ! is_singular() || ! $this->is_enabled( 'facebook_og_tag' ) ) {
            return;
        }

        echo '<meta property="og:title" content="' . get_the_title() . '" />';
        echo '<meta property="og:type" content="article" />';
        echo '<meta property="og:url" content="' . get_permalink() . '" />';
        echo '<meta property="og:site_name" content="' . get_bloginfo() . '" />';

        if ( has_post_thumbnail( get_the_ID() ) ) {
            $thumbnail_src = $this->do_filter( 'facebook_og_image', wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' ), get_the_ID() );
            echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '" />';
        };
    }
}