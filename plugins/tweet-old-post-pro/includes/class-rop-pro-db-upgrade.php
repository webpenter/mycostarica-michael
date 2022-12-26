<?php
/**
 * The file that defines the abstract class inherited by all shortners
 *
 * A class that is used to define the shortners class and utility methods.
 *
 * @link       https://themeisle.com/
 * @since      8.0.0
 *
 * @package    Rop
 * @subpackage Rop/includes/admin/helpers
 */

/**
 * Class Rop_Db_Upgrade
 *
 * @since   8.0.0
 * @link    https://themeisle.com/
 */
class Rop_Pro_Db_Upgrade {
	/**
	 * Database version used for upgrading purposes.
	 *
	 * @var string $db_version Database version.
	 */
	private $db_version = '1.0.0';

	/**
	 * The database option key.
	 *
	 * @var string $db_namespace Database namespace.
	 */
	private $db_namespace = 'rop_pro_db_version';

	/**
	 * Method to check if upgrade is required.
	 *
	 * @since   8.0.0
	 * @access  public
	 * @return bool
	 */
	public function is_upgrade_required() {
		$upgrade_check = get_option( 'cwp_top_logged_in_users', '' );

		if ( empty( $upgrade_check ) ) {
			return false;
		} else {

			$db_version = $this->get_db_version();

			if ( empty( $db_version ) ) {
				return true;
			}
			if ( version_compare( $db_version, $this->db_version ) < 0 ) {
				return true;
			}

			return false;
		}

	}

	/**
	 * Get database version.
	 *
	 * @return string Database version string.
	 */
	private function get_db_version() {
		$db_version_cache = wp_cache_get( $this->db_namespace, 'rop' );
		if ( ! empty( $db_version_cache ) ) {
			return $db_version_cache;
		}

		$db_version = get_option( $this->db_namespace, '' );

		return $db_version;
	}

	/**
	 * Method to do the required upgrade.
	 *
	 * @since   8.0.0
	 * @access  public
	 */
	public function do_upgrade() {
		/**
		 * ReRun pro upgrade routine.
		 */
		if ( ! class_exists( 'Rop_Db_Upgrade' ) ) {
			return;
		}
		$upgrade_lite = new Rop_Db_Upgrade();
		$upgrade_lite->do_upgrade();
		update_option( $this->db_namespace, $this->db_version, 'no' );
		wp_cache_delete( $this->db_namespace, 'rop' );
	}

}
