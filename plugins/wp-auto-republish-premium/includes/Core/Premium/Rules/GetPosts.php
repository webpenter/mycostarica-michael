<?php
/**
 * The file for Fetch Posts.
 *
 * @since      1.2.5
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium\Rules
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Rules;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Get Posts class.
 */
class GetPosts
{
	use HelperFunctions, Hooker;

	/**
	 * Query Data
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access private
	 */
	private $query = [];

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/run_republish_rule_event', 'run_republish_process' );
	}

    /**
	 * Run single republish event once.
	 */
    public function run_republish_process( $post_id )
	{
		if ( get_transient( 'wpar_single_lock_' . $post_id ) === false ) {
			// run republish process
			$this->get_eligible_posts( $post_id );

			// lock republish query
			set_transient( 'wpar_single_lock_' . $post_id, true, 10 );
		}
	}

	/**
	 * Get eligible posts.
	 */
	private function get_eligible_posts( $post_id )
	{
		$post_types = $this->build_meta( $post_id, '_wpar_repost_post_types', [], true );
        if ( ! empty( $post_types ) ) {
    		foreach ( $post_types as $post_type ) {
				$this->fetch_posts( $post_type, $post_id );
			}

			$this->generate_action( $post_id );
			
			// Update next post schedule if nescessary
		    $this->update_single_schedule( $post_id );
    	}
	}

	/**
	 * Post update process Wrapper.
	 * 
	 * @since v1.2.2
	 * @param int   $post_id  Post ID
	 */
	public function fetch_posts( $post_type, $post_id ) 
	{
		$timestamp = current_time( 'timestamp', 0 );
		$taxonomies = $this->build_meta( $post_id, '_wpar_repost_taxonomies', [], true ); 
		$eligibility_age = $this->build_meta( $post_id, '_wpar_repost_eligibility_age', '' );

		$cats = $tags = $terms = [];
    	$args = [
    		'post_status' => 'publish',
    		'post_type'   => $post_type,
    		'numberposts' => -1,
			'meta_query'  => [
				'relation' => 'AND',
				[
					'key'		=> 'wpar_global_republish_status',
					'compare'	=> 'NOT EXISTS'
				],
				[
					'key'		=> 'wpar_single_republish_status',
					'compare'	=> 'NOT EXISTS'
				],
				[
					'key'		=> 'wpar_exclude_republish_rule',
					'compare'	=> 'NOT EXISTS'
				]
			]
    	];

		if ( $eligibility_age != '' ) {
			$args['date_query'][]['before'] = $this->do_filter( 'post_before_date_republish_rule', date( 'Y-m-d', strtotime( "-$eligibility_age days", $timestamp ) ), $timestamp );
		}

    	if ( ! empty( $taxonomies ) ) {
    		foreach ( $taxonomies as $taxonomy ) {
    			$get_item = explode( '|', $taxonomy );
				$type = $get_item[0];
				$taxonomy_name = $get_item[1];
    			$term_id = $get_item[2];
    			if ( $post_type === $type && is_object_in_taxonomy( $post_type, $taxonomy_name ) ) {
					if ( $taxonomy_name == 'category' ) {
                        $cats[] = $term_id;
    		        } else if ( $taxonomy_name == 'post_tag' ) {
                        $tags[] = $term_id;
				    } else {
						$terms[$taxonomy_name][] = $term_id;
					}
    		    }
			}
			
    	    if ( ! empty( $cats ) ) {
    		    $args['category__in'] = $cats;
    		}

    		if ( ! empty( $tags ) ) {
    			$args['tag__in'] = $tags;
    		}
			
			if ( ! empty( $terms ) ) {
				$array = [];
		    	if ( count( $terms ) > 1 ) {
		    		$array['relation'] = 'OR';
		    	}

    	    	foreach ( $terms as $taxonomy => $term_ids ) {
    	    		$array[] = [
    	    			'taxonomy' => $taxonomy,
    	    			'field'    => 'term_id',
    	    			'terms'    => $term_ids,
    	    			'operator' => 'IN',
		    		];
		    	}

    	    	$args['tax_query'] = $array;
		    }
		}

		$args = $this->do_filter( 'query_args_rule', $args );
    
    	//error_log( print_r( $args, true ) );
    
    	// store post objects
		$this->query[] = get_posts( $args );
	}

	/**
	 * Complete
	 */
	private function generate_action( $post_id )
	{
		$timestamp = current_time( 'timestamp', 0 );

		$query = $this->query;
		$post_types = $this->build_meta( $post_id, '_wpar_repost_post_types', [], true );
		$post_order = $this->build_meta( $post_id, '_wpar_repost_post_order', 'old_first' );
		$post_orderby = $this->build_meta( $post_id, '_wpar_repost_post_orderby', 'date' );
		$randomness = $this->build_meta( $post_id, '_wpar_repost_post_randomness', 3600 );
		$number_posts = $this->build_meta( $post_id, '_wpar_repost_number_posts', 1 );
		$republish_action = $this->build_meta( $post_id, '_wpar_repost_republish_action', 'repost' );
		$post_ids = [];
		
		if ( ! empty( $query ) ) {
			$posts_list = array_merge( ...$query );
			$post_ids = wp_list_pluck( $posts_list, 'ID' );
		}

    	//error_log( print_r( $post_ids, true ) );
    
        if ( ! empty( $post_ids ) ) {
    	    $args = [
				'post_type'   => $post_types,
				'post_status' => 'publish',
    	    	'post__in'    => $post_ids,
    	    	'numberposts' => $number_posts,
    	    	'orderby'     => $post_orderby,
			];

			if ( ! empty( $post_order ) ) {
    	    	$args['order'] = 'ASC';
    	    	if ( $post_order == 'new_first' ) {
    	    		$args['order'] = 'DESC';
    	    	}
			}
			
			$args = $this->do_filter( 'post_query_args_rule', $args );

			//error_log( print_r( $args, true ) );

			$posts = get_posts( $args );
			
    	    if ( ! empty( $posts ) ) {
    	        foreach ( $posts as $post ) {
					// delete previosly scheduled hook if exists any.
					$this->unschedule_all_actions( 'wpar/global_republish_single_post', [ $post->ID ] );

					// get the required date time
			        $datetime = time() + mt_rand( 0, $randomness );

					// schedule single post republish event
					$this->set_single_action( $datetime, 'wpar/global_republish_single_post', [ $post->ID ] );
							
					// update required post metas
					$this->update_meta( $post->ID, 'wpar_global_republish_status', 'pending' );
					$this->update_meta( $post->ID, '_wpar_global_republish_datetime', get_date_from_gmt( date( 'Y-m-d H:i:s', $datetime ) ) );
					$this->update_meta( $post->ID, 'wpar_filter_republish_status', 'pending' );
					$this->update_meta( $post->ID, '_wpar_filter_republish_datetime', get_date_from_gmt( date( 'Y-m-d H:i:s', $datetime ) ) );
					$this->update_meta( $post->ID, 'wpar_republish_rule_action', $republish_action );

					// set log action
					$this->do_action( 'insert_log', $post->ID, 'cron', true, '', get_date_from_gmt( date( 'Y-m-d H:i:s', $datetime ), 'U' ), 'dashicons-backup' );
    	    	}
    	    }
		}
	}

	/**
	 * Set single Re-Post next schedule
	 * 
	 * @param int $post_id Post ID
	 */
	private function update_single_schedule( $post_id )
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
				if ( $get_status != 'default' ) {
					wp_update_post( [ 'ID'=> $post_id, 'post_status'=> 'draft' ] );
				}
			}
		} else if ( $end == 'after' ) {
			if ( count( $reposted ) > $occurence ) {
				$new_schedule = date( 'm/d/Y', $schedule );
				$schedule_generate = false;
				if ( $get_status != 'default' ) {
					wp_update_post( [ 'ID'=> $post_id, 'post_status'=> 'draft' ] );
				}
			}
		}
	
		$full_datetime = date( 'Y-m-d', strtotime( $new_schedule ) ) . ' ' . $datetime;
		
		$this->do_action( 'republish_rule_updated', $post_id, strtotime( $full_datetime ), $schedule_generate );
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
}