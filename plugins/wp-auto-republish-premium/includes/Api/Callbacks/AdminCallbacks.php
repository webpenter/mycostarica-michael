<?php 
/**
 * Admin callbacks.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Api\Callbacks
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Api\Callbacks;

use Wpar\Base\BaseController;

defined( 'ABSPATH' ) || exit;

/**
 * Admin callbacks class.
 */
class AdminCallbacks extends BaseController
{
	/**
	 * Call dashboard template.
	 */
	public function adminDashboard()
	{
		$options = get_option( 'wpar_plugin_settings' );
		$last = get_option( 'wpar_last_global_cron_run' );
        $format = get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' );

		return require_once( "$this->plugin_path/templates/admin.php" );
	}
}