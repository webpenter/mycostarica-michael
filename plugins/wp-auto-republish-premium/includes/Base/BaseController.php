<?php 
/**
 * Base controller class.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Base;

/**
 * Base Controller class.
 */
class BaseController
{
	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Plugin URL.
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Plugin basename.
	 *
	 * @var string
	 */
	public $plugin;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Enable debug.
	 *
	 * @var bool
	 */
	public $debug;
	
	/**
     * The constructor.
     */
	public function __construct()
	{
		$this->plugin_path = plugin_dir_path( $this->dirname_r( __FILE__, 2 ) );
		$this->plugin_url = plugin_dir_url( $this->dirname_r( __FILE__, 2 ) );
		$this->plugin = plugin_basename( $this->dirname_r( __FILE__, 3 ) ) . '/wp-auto-republish.php';
		$this->version = '1.2.5.1';
		$this->debug = false;
		$this->name = 'WP Auto Republish';

		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			if ( wpar_load_fs_sdk()->is_paying() ) {
				$this->name = 'WP Auto Republish Premium';
			} else {
				$this->name = 'WP Auto Republish Premium <small>Trial</small>';
			}
		}
	}

	/**
	 * PHP < 7.0.0 compatibility
	 */
	private function dirname_r( $path, $count = 1 ) {
		if ( $count > 1 ) {
		   return dirname( $this->dirname_r( $path, --$count ) );
		} else {
		   return dirname( $path );
		}
	}
}