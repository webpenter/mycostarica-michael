<?php
/**
 * Recreate ActionScheduler tables if missing.
 *
 * @since      1.2.3
 * @package    WP Auto Republish
 * @subpackage Wpar\Tools
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Tools;

use Wpar\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Database Table class.
 */
class DatabaseTable
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'plugins_loaded', 'maybe_recreate_actionscheduler_tables', 5 );
	}

	/**
	 * Recreate ActionScheduler tables if missing.
	 */
	public function maybe_recreate_actionscheduler_tables()
	{
		global $wpdb;

		if (
			! class_exists( 'ActionScheduler_HybridStore' )
			|| ! class_exists( 'ActionScheduler_StoreSchema' )
			|| ! class_exists( 'ActionScheduler_LoggerSchema' )
		) {
			return;
		}

		$table_list = [
			'actionscheduler_actions',
			'actionscheduler_logs',
			'actionscheduler_groups',
			'actionscheduler_claims',
		];

		$found_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'" );
		foreach ( $table_list as $table_name ) {
			if ( ! in_array( $wpdb->prefix . $table_name, $found_tables, true ) ) {
				$this->recreate_actionscheduler_tables();
				return;
			}
		}
	}

	/**
	 * Force the data store schema updates.
	 */
	private function recreate_actionscheduler_tables()
	{
		$store = new \ActionScheduler_HybridStore();
		add_action( 'action_scheduler/created_table', [ $store, 'set_autoincrement' ], 10, 2 );

		$store_schema  = new \ActionScheduler_StoreSchema();
		$logger_schema = new \ActionScheduler_LoggerSchema();
		$store_schema->register_tables( true );
		$logger_schema->register_tables( true );

		remove_action( 'action_scheduler/created_table', [ $store, 'set_autoincrement' ], 10 );
	}
}