<?php
/**
 * The file for Cron Health check.
 *
 * @since      1.2.2
 * @package    WP Auto Republish
 * @subpackage Wpar\Tools
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Tools;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Health check class.
 */
class HealthCheck
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'init', 'generate_task' );
		$this->action( 'wpar/process_health_check', 'health_check' );
	}

	/**
	 * Initialize health check tasks.
	 */
	public function generate_task()
	{
		$interval = $this->do_filter( 'health_check_cron_interval', 30 );
		if ( false === $this->get_next_action( 'wpar/process_health_check' ) ) {
			$this->set_recurring_action( strtotime( '+30 minutes' ), MINUTE_IN_SECONDS * $interval, 'wpar/process_health_check' );
		}
	}

    /**
	 * Run the event once.
	 */
	public function health_check()
	{
		if ( get_transient( 'wpar_health_check_lock' ) === false ) {
			// run post republish query
			$this->handle();

			// lock republish query
			set_transient( 'wpar_health_check_lock', true, 10 );
    	}
	}

	/**
	 * Run single republish process.
	 */
	private function handle()
	{
		// global republish
		$this->regenerate_cron( $this->get_data( 'wpar_post_types', [ 'post' ] ) );
		$this->remove_metas( $this->get_data( 'wpar_post_types', [ 'post' ] ) );

		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
		    // single republish
		    $this->regenerate_cron( $this->get_data( 'post_types_list_single', [ 'post' ] ), 'single', true );
		    $this->remove_metas( $this->get_data( 'post_types_list_single', [ 'post' ] ), 'single', true );
		}
	}

	/**
	 * Re-Generate missed events.
	 * 
	 * @param array   $post_types  Available Post Types
	 * @param string  $type        Cron Type
	 * @param bool    $single      Single Cron
	 */
	private function regenerate_cron( $post_types, $type = 'global', $single = false )
	{
		$key = '_wpar_global_republish_datetime';
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
	    	if ( $type == 'single' ) {
	    		$key = '_wpar_repost_schedule_datetime';
	    	}
	    }

		$args = [
			'post_type'   => $post_types,
			'numberposts' => -1,
			'post_status' => 'publish',
			'meta_query'  => [
				'relation' => 'AND',
				[
				    'key'		=> 'wpar_' . $type . '_republish_status',
    			    'compare'	=> 'EXISTS',
			    ],
				[
					'key'		=> $key,
					'value'		=> current_time( 'mysql' ),
					'compare'	=> '>',
					'type'      => 'DATETIME'
				]
			]
		];

		$args = $this->do_filter( $type . '_health_check_args', $args );

		//error_log( print_r( $args, true ) );
	
		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( ! $single ) {
					// check if global cron event is not exists
					if ( ! $this->get_next_action( 'wpar/global_republish_single_post', [ $post->ID ] ) ) {
                        // get republish time from post meta
						$datetime = $this->get_meta( $post->ID, '_wpar_global_republish_datetime' );

						// schedule single post republish event
						$this->set_single_action( get_gmt_from_date( $datetime, 'U' ), 'wpar/global_republish_single_post', [ $post->ID ] );
					}
				}

				if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    	if ( $single ) {
			    		// check if single cron event is not exists
			    		if ( ! $this->get_next_action( 'wpar/run_single_republish', [ $post->ID ] ) ) {
			    			// get republish time from post meta
			    			$datetime = $this->get_meta( $post->ID, '_wpar_repost_schedule_datetime' );
    
			    			// schedule single post republish event
			    			$this->set_single_action( get_gmt_from_date( $datetime, 'U' ), 'wpar/run_single_republish', [ $post->ID ] );
			    		}
			        }
			    }
			}
		}
	}

	/**
	 * Delete missed events post metas and publish them.
	 * 
	 * @param array   $post_types  Available Post Types
	 * @param string  $type        Action Type
	 * @param bool    $single      Single Event
	 */
	private function remove_metas( $post_types, $type = 'global', $single = false )
	{
		$key = '_wpar_global_republish_datetime';
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
	    	if ( $type == 'single' ) {
	    		$key = '_wpar_repost_schedule_datetime';
	    	}
	    }

		$args = [
			'post_type'   => $post_types,
			'numberposts' => -1,
			'post_status' => 'publish',
			'meta_query'  => [
				'relation' => 'AND',
				[
				    'key'		=> 'wpar_' . $type . '_republish_status',
    			    'compare'	=> 'EXISTS',
			    ],
				[
					'key'		=> $key,
					'value'		=> current_time( 'mysql' ),
					'compare'	=> '<=',
					'type'      => 'DATETIME'
				]
			]
		];

		$args = $this->do_filter( $type . '_remove_metas_args', $args );

		//error_log( print_r( $args, true ) );
	
		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( ! $single ) {
					// check if global cron event is not exists
					if ( ! $this->get_next_action( 'wpar/global_republish_single_post', [ $post->ID ] ) ) {
						// delete old post meta
						$this->delete_meta( $post->ID, 'wpar_global_republish_status' );
						$this->delete_meta( $post->ID, '_wpar_global_republish_datetime' );
						
						if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
						    $this->delete_meta( $post->ID, 'wpar_filter_republish_status' );
						    $this->delete_meta( $post->ID, '_wpar_filter_republish_datetime' );
						}
                    }
				}

				if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
				    if ( $single ) {
				    	// check if single cron event is not exists
				    	if ( ! $this->get_next_action( 'wpar/run_single_republish', [ $post->ID ] ) ) {
				    		// delete old post meta
				    		$this->delete_meta( $post->ID, '_wpar_repost_schedule_datetime' );
				    		$this->delete_meta( $post->ID, '_wpar_filter_republish_datetime' );
    
				    		// immediate republish of post
				    		$this->do_action( 'republish_single_post', $post->ID );
				    	}
			        }
				}
			}    
		}
	}
}