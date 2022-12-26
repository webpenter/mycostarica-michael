<?php 
/**
 * Admin notices.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Base;

use Wpar\Helpers\Hooker;
use Wpar\Base\BaseController;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Notice class.
 */
class AdminNotice extends BaseController
{
	use Hooker;
	
	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'admin_notices', 'install_notice' );
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
		    $this->action( 'admin_footer-post.php', 'block_editor_notice__premium_only' );
		}
	}
	
	/**
	 * Show internal admin notices.
	 */
	public function install_notice()
	{
		global $pagenow;
		
		$permalink_structure = get_option( 'permalink_structure' );
		$show_notice = $this->do_filter( 'show_permalink_notice', true );
		
		if ( preg_match( '(%year%|%monthnum%|%day%|%hour%|%minute%|%second%)', $permalink_structure ) === 1 ) {
			if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    if ( $pagenow === 'options-permalink.php' && $show_notice ) { ?>
			        <div class="notice notice-warning">
			        	<p><strong><?php printf( __( 'WARNING: As it seems that your permalinks structure contains date, please use %1$s instead of %2$s respectively.', 'wp-auto-republish' ), '<code>%wpar_year%</code>, <code>%wpar_monthnum%</code>, <code>%wpar_day%</code>, <code>%wpar_hour%</code>, <code>%wpar_minute%</code>, <code>%wpar_second%</code>', '<code>%year%</code>, <code>%monthnum%</code>, <code>%day%</code>, <code>%hour%</code>, <code>%minute%</code>, <code>%second%</code>' ); ?></strong></p>
			        </div> <?php
				}
			}

			if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { 
			    if ( $show_notice ) { ?>
			        <div class="notice notice-warning">
			    	    <p><strong><?php printf( __( 'WARNING: As it seems that your permalinks structure contains date, please do not use %s plugin (see FAQ no. 9) or upgrade to the Premium version.', 'wp-auto-republish' ), $this->name ); ?></strong></p>
			        </div> <?php
				}
			}
		}

		// Show a warning to sites running PHP < 5.6
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			deactivate_plugins( $this->plugin );
			echo '<div class="error"><p>' . sprintf( __( 'Your version of PHP is below the minimum version of PHP required by %s plugin. Please contact your host and request that your version be upgraded to 5.6 or later.', 'wp-auto-republish' ), $this->name ) . '</p></div>';
		}

		// Check transient, if available display notice
		if ( get_transient( 'wpar-show-notice-on-activation' ) !== false ) { 
			if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) { ?>
			    <div class="notice notice-success">
				    <p><strong><?php printf( __( 'Thanks for installing %1$s v%2$s plugin. Click <a href="%3$s">here</a> to configure plugin settings.', 'wp-auto-republish' ), $this->name, $this->version, admin_url( 'admin.php?page=wp-auto-republish' ) ); ?></strong></p>
			    </div> <?php
			}
			delete_transient( 'wpar-show-notice-on-activation' );
		}
	}

	/**
	 * Show internal admin notices.
	 */
	public function block_editor_notice__premium_only()
	{
		global $wp_version;
		
		if ( get_transient( 'wpar_instant_republish_done' ) !== false ) { 
			if ( in_array( $_REQUEST['post_type'], [ 'post', 'page' ] ) ) {
			    if ( version_compare( $wp_version, '5.0', '>=' ) ) { ?>
                    <script>
			            ( function( wp ) {
                            wp.data.dispatch( 'core/notices' ).createSuccessNotice( // Can be one of: success, info, warning, error.
                                '<?php _e( 'Post republished and rescheduled (if any).', 'wp-auto-republish' ); ?>', // Text string to display.
                                {
                                    isDismissible: true, // Whether the user can dismiss the notice.
                                }
                            );
                        } )( window.wp );
					</script><?php
				}
			}
		}
	}
}