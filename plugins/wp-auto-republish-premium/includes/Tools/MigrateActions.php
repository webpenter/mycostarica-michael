<?php
/**
 * Migrate Events from WP Cron to Action Scheduler.
 *
 * @since      1.2.3
 * @package    WP Auto Republish
 * @subpackage Wpar\Tools
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Tools;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Sction Migration class.
 */
class MigrateActions
{
	use Hooker, HelperFunctions;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/after_plugin_activate', 'regenerate_actions' );
	}

	/**
	 * Purge single post cache.
	 */
	public function regenerate_actions()
	{
		// remove action scheduler schema if already exists.
        delete_option( 'schema-ActionScheduler_StoreSchema' );

		$crons = _get_cron_array();
		foreach ( $crons as $timestamp => $data ) {
			foreach ( $data as $hook => $schedule ) {
				foreach ( $schedule as $id => $info ) {
					if ( $hook === 'wpar/global_republish_single_post' ) {
						if ( empty( $this->get_next_action_by_data( $hook, $timestamp, $info['args'] ) ) ) {
							// schedule global post republish event
							$this->set_single_action( $timestamp, $hook, $info['args'] );
							\wp_unschedule_event( $timestamp, $hook, $info['args'] );
						}
					}

					if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
					    if ( $hook === 'wpar/run_single_republish' ) {
					    	if ( empty( $this->get_next_action_by_data( $hook, $timestamp, $info['args'] ) ) ) {
					    		// schedule single post republish event
			                    $this->set_single_action( $timestamp, $hook, $info['args'] );
					    		\wp_unschedule_event( $timestamp, $hook, $info['args'] );
					    	}
					    }
					}
				}
			}
		}
	}
}