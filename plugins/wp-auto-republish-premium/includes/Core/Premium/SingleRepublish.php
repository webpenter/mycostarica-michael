<?php
/**
 * The file for Single Republish.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Core\PostRepublish;

defined( 'ABSPATH' ) || exit;

/**
 * Single post republish class.
 */
class SingleRepublish extends PostRepublish
{
	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/single_post_updated', 'set_single_event', 10, 3 );
		$this->action( 'wpar/run_single_republish', 'run_process' );
		$this->action( 'wpar/republish_single_post', 'update_process' );
		$this->action( 'wpar/old_post_republished', 'post_metadata', 10, 4 );
		$this->action( 'wpar/old_post_republished', 'trigger_publish', 20, 3 );
		$this->filter( 'wpar/single_republish_process', 'filter_update_process', 10, 2 );
	}

	/**
	 * Generate Single Cron if required
	 * 
	 * @param int  $post_id   Post ID
	 * @param int  $datetime  Cron schedule
	 * @param bool $generate  Generate if necessary
	 */
	public function set_single_event( $post_id, $datetime, $generate )
	{
		// clear cron event if any
		$this->unschedule_all_actions( 'wpar/run_single_republish', [ $post_id ] );

		// delete old post meta
		$this->delete_meta( $post_id, 'wpar_single_republish_status' );
		$global_pending = $this->get_meta( $post_id, 'wpar_global_republish_status' );
		if ( ! $global_pending ) {
		    $this->delete_meta( $post_id, 'wpar_filter_republish_status' );
		}
		
		$interval = $this->do_filter( 'random_single_republish_interval', 5 ) * MINUTE_IN_SECONDS;
		$repeats = $this->get_meta( $post_id, '_wpar_repost_repeats' );
		if ( ! in_array( $repeats, [ 'minutes', 'hourly' ] ) ) {
			$datetime = $datetime + mt_rand( 0, $interval );
		}

		$formatted_datetime = date( 'Y-m-d H:i:s', $datetime );
		$timestamp = current_time( 'timestamp', 1 );
		$new_datetime = get_gmt_from_date( $formatted_datetime, 'U' );

		if ( $this->can_republish( $post_id ) && ( $new_datetime > $timestamp ) && $generate ) {
			// schedule single post republish event
			$this->set_single_action( $new_datetime, 'wpar/run_single_republish', [ $post_id ] );
		  
			// update required post metas
			$this->update_meta( $post_id, 'wpar_single_republish_status', 'pending' );
			$this->update_meta( $post_id, 'wpar_filter_republish_status', 'pending' );
			$this->update_meta( $post_id, '_wpar_repost_schedule_datetime', $formatted_datetime );
		    $this->update_meta( $post_id, '_wpar_filter_republish_datetime', $formatted_datetime );

			$this->do_action( 'insert_log', $post_id, 'single_cron', true, '', $datetime, 'dashicons-clock' );
		}
	}

    /**
	 * Run single republish event once.
	 */
    public function run_process( $post_id )
	{
		if ( get_transient( 'wpar_single_lock_' . $post_id ) === false ) {
			// run republish process
			$this->update_process( $post_id );

			// lock republish query
			set_transient( 'wpar_single_lock_' . $post_id, true, 10 );
		}
	}

	/**
	 * Post update process Wrapper.
	 * 
	 * @since v1.2.2
	 * @param int   $post_id  Post ID
	 */
	public function update_process( $post_id ) 
	{
		// check if single republish is actually pending
		$pending = $this->get_meta( $post_id , 'wpar_single_republish_status' );

		// delete post meta
		$this->delete_meta( $post_id, 'wpar_single_republish_status' );
		$this->delete_meta( $post_id, 'wpar_filter_republish_status' );
		
		// run if single republish can run
		if ( $this->can_republish( $post_id ) && $pending ) {
			// check if given post is not published.
			if ( 'publish' === get_post_status( $post_id ) ) {
		        // run republish process
		        $this->run_update_process( $post_id, true );
			}
		    	
		    // Update next post schedule if nescessary
		    $this->update_single_schedule( $post_id );
		}
	}

	/**
	 * Run post update process.
	 * 
	 * @since v1.1.7
	 * @param int   $post_id  Post ID
	 * @param bool  $single   Check if it is a single republish event
	 * @param bool  $instant  Check if it is one click republish event
	 */
	protected function run_update_process( $post_id, $single = false, $instant = false )
	{
		$action = $this->do_filter( 'single_republish_action', $this->get_update_action__premium_only( $post_id, true ), $post_id );
	    if ( $action == 'repost' ) {
	        $this->update_old_post( $post_id, $single, $instant );
	    } elseif ( $action == 'clone' ) {
	        $this->clone_old_post__premium_only( $post_id, $single, $instant );
	    }

		return $post_id;
	}

	/**
	 * Set single Re-Post next schedule
	 * 
	 * @param int $post_id Post ID
	 */
	protected function update_single_schedule( $post_id )
	{
		$option = $this->get_meta( $post_id, '_wpar_repost_option' );
		$number = $this->get_meta( $post_id, '_wpar_repost_after_number' );
		$time = $this->get_meta( $post_id, '_wpar_repost_after_time' );
		$repeats = $this->get_meta( $post_id, '_wpar_repost_repeats' );
		$every = $this->get_meta( $post_id, '_wpar_repost_every' );
		$weekly = maybe_unserialize( $this->get_meta( $post_id, '_wpar_repost_on' ) );
		if ( empty( $weekly ) ) $weekly = [];
		
		$monthly = $this->get_meta( $post_id, '_wpar_repost_by' );
		$monthly_on = maybe_unserialize( $this->get_meta( $post_id, '_wpar_repost_monthly_on' ) );
		if ( empty( $monthly_on ) ) $monthly_on = [];
	
		$end = $this->get_meta( $post_id, '_wpar_repost_end' );
		$occurence = $this->get_meta( $post_id, '_wpar_repost_end_after' );
		
		$on = strtotime( $this->get_meta( $post_id, '_wpar_repost_on_date' ) );
		$end_on = $this->get_meta( $post_id, '_wpar_repost_end_on' );
		$get_status = $this->get_meta( $post_id, '_wpar_repost_last_republish_status' ); 

		$schedule = strtotime( $this->get_meta( $post_id, '_wpar_repost_schedule' ) );
		$today = date( 'm/d/Y', current_time( 'timestamp', 0 ) );
		
		$reposted = unserialize( $this->get_meta( $post_id, '_wpar_repost_date' ) );
		if ( empty( $reposted ) ) $reposted = [];
	
		$repost_at_time = $this->get_meta( $post_id, '_wpar_repost_at_specific_time' );
		$datetime = ( $repost_at_time ) ? date( 'H:i:s', strtotime( $repost_at_time ) ) : '00:00:00';
		$schedule_generate = true;

		if ( in_array( $option, [ 'date', 'days', 'disable' ] ) ) {
			$this->update_meta( $post_id, '_wpar_repost_done', 'yes' );
		    return;
		}
	
		switch( $repeats ) {
			case 'yearly':
				$new_schedule = date( 'm/d/Y', strtotime( '+'. $every .' years', $schedule ) );
				break;
			
			case 'monthly':
				$new_schedule = date( 'm/d/Y', strtotime( '+'. $every .' months', $schedule ) );
                if( $monthly == 'week' ) {
					$new_schedule = date( 'm/d/Y', strtotime( $this->literal_date( date( 'Y-m-d', $schedule ), strtotime( $new_schedule ) ) ) );
				}

				// Check if next month is turned on
				if( ! empty( $monthly_on ) ) {
					while( ! in_array( strtolower( date( 'F', strtotime( $new_schedule ) ) ), $monthly_on ) ) {
						$new_schedule = date( 'm/d/Y', strtotime( '+1 month', strtotime( $new_schedule ) ) );
						
						if( $monthly == 'week' ) {
							$new_schedule = date( 'm/d/Y', strtotime( $this->literal_date( date( 'Y-m-d', $schedule ), strtotime( $new_schedule ) ) ) );
						}
					}
				}
				break;
			
			case 'weekly':
				$new_schedule = date( 'm/d/Y', strtotime( '+'. $every .' weeks', $schedule ) );
				break;
			
			case 'daily':
				$new_schedule = date( 'm/d/Y', strtotime( '+'. $every .' days', $schedule ) );
				break;

			case 'hourly':
				$full_timestamp = $this->generate_next_time( $post_id, date( 'Y-m-d', $schedule ), $datetime, $every, 'hours' );
				$new_schedule = date( 'm/d/Y', $full_timestamp );
				$datetime = date( 'H:i:s', $full_timestamp );
				break;

			case 'minutes':
			default:
			    $full_timestamp = $this->generate_next_time( $post_id, date( 'Y-m-d', $schedule ), $datetime, $every, 'minutes' );
			    $new_schedule = date( 'm/d/Y', $full_timestamp );
			    $datetime = date( 'H:i:s', $full_timestamp );
			    break;
		}
		
		if ( $end == 'on' ) {
			if ( strtotime( $new_schedule ) >= strtotime( $end_on ) ) {
				$new_schedule = date( 'm/d/Y', $schedule );
				$schedule_generate = false;
				$this->update_meta( $post_id, '_wpar_repost_done', 'yes' );
				if ( $get_status != 'default' ) {
					wp_update_post( [ 'ID'=> $post_id, 'post_status'=> $get_status ] );
				}
			}
		} else if ( $end == 'after' ) {
			if ( count( $reposted ) > $occurence ) {
				$new_schedule = date( 'm/d/Y', $schedule );
				$schedule_generate = false;
				$this->update_meta( $post_id, '_wpar_repost_done', 'yes' );
				if ( $get_status != 'default' ) {
					wp_update_post( [ 'ID'=> $post_id, 'post_status'=> $get_status ] );
				}
			}
		}
	
		$full_datetime = date( 'Y-m-d', strtotime( $new_schedule ) ) . ' ' . $datetime;
		
		$this->update_meta( $post_id, '_wpar_repost_at_specific_time', $datetime );
		$this->update_meta( $post_id, '_wpar_repost_schedule', $new_schedule );
		$this->update_meta( $post_id, '_wpar_repost_schedule_datetime', $full_datetime );
		$this->update_meta( $post_id, '_wpar_filter_republish_datetime', $full_datetime );

		$this->do_action( 'single_post_updated', $post_id, strtotime( $full_datetime ), $schedule_generate );
	}

	/**
	 * Generate Next Cron Time for Hour and Minute type single republishing.
	 * 
	 * @since v1.1.8
	 * @param int    $post_id    Post ID
	 * @param string $date       Original Date
	 * @param string $time       Original Time
	 * @param int    $every      No. of repeats
	 * @param string $repeats    Repeat type
	 * 
	 * @return int   $timestamp  Valid Datetime
	 */
	private function generate_next_time( $post_id, $date, $time, $every, $repeats )
	{
		$datetime = $date . ' ' . $time;
		$new_date = strtotime( '+'. $every . ' ' . $repeats, strtotime( $datetime ) );
		$timestamp = $new_date;

		$start_time = $this->get_meta( $post_id, '_wpar_repost_start_time' );
		$end_time = $this->get_meta( $post_id, '_wpar_repost_end_time' );
		$repeats = $this->get_meta( $post_id, '_wpar_repost_repeats' );

		if ( ! empty( $start_time ) && ! empty( $end_time ) && in_array( $repeats, [ 'minutes', 'hourly' ] ) ) {
			$start_datetime = strtotime( $date . ' ' . $start_time );
			$end_datetime = strtotime( $date . ' ' . $end_time );

			if ( ( $new_date >= $start_datetime ) && ( $new_date <= $end_datetime ) ) {
			    $timestamp = $new_date;
			} else {
				$timestamp = strtotime( date( 'Y-m-d', strtotime( '+1 day', strtotime( $datetime ) ) ) . ' ' . $start_time );
			}
		}

		return $timestamp;
	}

	/**
	 * Generate formatted date.
	 * 
	 * @param string $timestamp
	 * 
	 * @return string 
	 */
	private function literal_date( $timestamp, $new_schedule )
	{
		$timestamp = is_numeric( $timestamp ) ? $timestamp : strtotime( $timestamp );
		$weekday   = date( 'l', $timestamp );
		$month     = date( 'M', $timestamp );   
		$ord       = 0;
	
		while( date( 'M', ( $timestamp = strtotime( '-1 week', $timestamp ) ) ) == $month ) {
			$ord++;
		}
	
		$lit = [ 'first', 'second', 'third', 'fourth', 'fifth' ];

		return strtolower( $lit[$ord] . ' ' . $weekday . ' of ' . date( 'F Y', $new_schedule ) );
	}
	
	/**
	 * Update required post metas.
	 *
	 * @param int    $post_id  Post ID.
	 * @param object $post     WP Post object.
	 */
	public function post_metadata( $post_id, $post, $republished_time, $action )
	{
		$original_post_id = $post_id;
		if ( $action == 'clone' ) {
			$original_post_id = $this->get_meta( $post_id, 'wpar_original_post_id' );
			$this->delete_meta( $post_id, 'wpar_original_post_id' );
		}

		$reposted = unserialize( $this->get_meta( $original_post_id, '_wpar_repost_date' ) );
		if ( empty( $reposted ) ) $reposted = [];
	
		$reposted[] = $republished_time;

		$this->update_meta( $original_post_id, '_wpar_repost_date', maybe_serialize( $reposted ) );
	}
	
    /**
	 * Build post update process.
	 *
	 * @since v1.1.7
	 * @param bool  $enable  Check if republish disabled
	 * @param int   $post_id Post ID
	 * 
	 * @return bool 
	 */
	public function filter_update_process( $enable, $post_id )
	{
		$timestamp = current_time( 'timestamp', 0 );
		$cur_time = strtotime( date( 'H:i:s', $timestamp ) );

		$start_time = $this->get_meta( $post_id, '_wpar_repost_start_time' );
		$end_time = $this->get_meta( $post_id, '_wpar_repost_end_time' );
		$repeats = $this->get_meta( $post_id, '_wpar_repost_repeats' );

		if ( ! empty( $start_time ) && ! empty( $end_time ) && in_array( $repeats, [ 'minutes', 'hourly' ] ) ) {
			if ( ( $cur_time >= strtotime( $start_time ) ) && ( $cur_time <= strtotime( $end_time ) ) ) {
			    $enable = true;
			} else {
				$enable = false;
			}
		}

		return $enable;
	}

	/**
	 * Trigger publish event.
	 * 
	 * @since v1.1.7
	 * 
	 * @param int    $post_id  Post ID
	 * @param object $post     WP Post Object
	 */
	public function trigger_publish( $post_id, $post, $time )
	{
		$original_post_id = $this->get_meta( $post_id, 'wpar_original_post_id' );
        if ( $original_post_id ) {
			return;
		}

		$cache = true;
		if ( ! $this->is_enabled( 'enable_silent_republishing', true ) ) {
			$cache = false;
			if ( $post->post_status == 'publish' ) {
                // set post status to draft forcefully
				$post->post_status = $this->do_filter( 'fake_post_status', 'draft' );
			}
			
			$old_status = $post->post_status;

			// clean post cache
			\clean_post_cache( $post->ID );

			$do_publicize = $this->do_filter( 'enable_jetpack_publicize', true, $post_id );
			if ( $do_publicize ) {
			    // update jetpack meta to available it for publicize
			    $this->update_meta( $post->ID, '_publicize_pending', true );
			    $this->delete_meta( $post->ID, '_wpas_done_all' );
			}

			// do post status change event
			\wp_transition_post_status( 'publish', $old_status, $post );
				
			$this->do_action( 'insert_log', $post->ID, 'trigger' );
		}

		$clear_cache = $this->do_filter( 'enable_single_post_cache', $cache, $post_id );
		if ( $post->post_status !== 'future' && $clear_cache ) {
			$this->do_action( 'single_post_cache', $post->ID, $post );
            $this->do_action( 'insert_log', $post->ID, 'cache' );
		}
	}

	/**
	 * Check if republish is not disabled.
	 *
	 * @param int $post_id The post ID.
	 * 
	 * @return bool
	 */
	private function can_republish( $post_id )
	{
		if ( ! $this->is_enabled( 'enable_single_republishing', true ) ) {
			return false;
		}

		$post_types = $this->get_data( 'post_types_list_single', [ 'post' ] );
		$option = $this->get_meta( $post_id, '_wpar_repost_option' );
		$proceed = false;
		
        if ( in_array( get_post_type( $post_id ), $post_types ) ) {
            if ( in_array( $option, [ 'days', 'date', 'repeat' ] ) ) {
				$proceed = true;
		    }
		}

		return $proceed;
	}
}