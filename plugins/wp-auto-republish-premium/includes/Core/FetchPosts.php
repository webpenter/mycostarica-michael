<?php
/**
 * Fetch eligible posts.
 *
 * @since      1.2.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Republication class.
 */
class FetchPosts
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
		$this->action( 'init', 'generate_task' );
		$this->action( 'wpar/global_republish_fetch_posts', 'run_republish_process' );
	}

	/**
	 * Generate Action event if not already exists.
	 */
	public function generate_task()
	{
		$interval = $this->do_filter( 'global_cron_interval', 5 );
		if ( false === $this->get_next_action( 'wpar/global_republish_fetch_posts' ) ) {
			$this->set_recurring_action( strtotime( '+5 minutes' ), MINUTE_IN_SECONDS * $interval, 'wpar/global_republish_fetch_posts' );
		}
	}

	/**
	 * Run auto republish process.
	 */
	public function run_republish_process()
	{
		if ( $this->is_enabled( 'enable_plugin', true ) && $this->valid_next_run() ) {
			if ( get_transient( 'wpar_global_lock' ) === false ) {
				// run post republish query
				$this->get_old_posts();
				// lock republish query
				set_transient( 'wpar_global_lock', true, 10 );
                // update log reference
				update_option( 'wpar_global_last_run', current_time( 'timestamp', 0 ) );
			}
    	}
	}

	/**
	 * Get eligible posts.
	 */
	private function get_old_posts()
	{
		$post_types = $this->get_data( 'wpar_post_types', [ 'post' ] );
        if ( ! empty( $post_types ) ) {
    		foreach( $post_types as $post_type ) {
				if ( ! $this->has_future_posts( $post_type ) ) {
					$this->query_posts( $post_type );
				}
			}

			$this->complete();
    	}
	}

	/**
	 * Get eligible post ids for every available post types
	 *
	 * @param string $post_type WordPress post types
	 */
	private function query_posts( $post_type )
	{
		$timestamp = current_time( 'timestamp', 0 );
		$overwrite = $this->get_data( 'wpar_exclude_by_type', 'none' );
		$taxonomies = $this->get_data( 'wpar_post_taxonomy', [] );
		$post_age = $this->get_data( 'wpar_republish_post_age', 120 );

		if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			if ( ! in_array( $post_age, [ '30', '45', '60', '90', '120', '180', '240', '365', '730', '1095' ] ) ) {
				$post_age = 14400;
			}
		}
	
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			if ( $post_age == 'custom' ) {
				$post_age = $this->get_data( 'republish_post_custom_age', 120 );
			}
		}

		$cats = $tags = $terms = [];
    	$args = [
    		'post_status' => 'publish',
    		'post_type'   => $post_type,
    		'numberposts' => -1
    	];

		if ( $post_age != 0 ) {
			$args['date_query'][]['before'] = $this->do_filter( 'post_before_date', date( 'Y-m-d', strtotime( "-$post_age days", $timestamp ) ), $timestamp );
		}
		
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$after_date = strtotime( str_replace( '/', '-', $this->get_data( 'republish_post_age_start', date( 'Y-m-d', strtotime( '-15 years', $timestamp ) ) ) ) );
			$after_date = date( 'Y-m-d', $after_date );

			$post_age_start = $this->get_data( 'republish_post_age_start_method', 'specific_date' );
			$custom_start_interval = $this->get_data( 'republish_custom_age_start', 60 );
			
			if ( $post_age_start == 'last_hours' ) {
				$after_date = date( 'Y-m-d H:i:s', strtotime( "-$custom_start_interval minutes", $timestamp ) );
				unset( $args['date_query'] );
			}

			$args['date_query'][]['after'] = $this->do_filter( 'post_after_date', $after_date, $timestamp );
			$args['date_query'][]['inclusive'] = true;
		}

    	if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
    		if ( ! in_array( $post_type, [ 'post', 'page', 'attachment' ] ) ) {
    			$args['meta_query'] = [
    				'relation' => 'AND',
    				[
						'key'		=> 'wpar_global_republish_status',
    				    'compare'	=> 'NOT EXISTS',
					],
					[
    				    'key'		=> '_wpar_post_republish_occurrence',
    				    'compare'	=> 'NOT EXISTS',
					]
    			];
    		} else {
				$args['meta_query'] = [
    				[
						'key'		=> 'wpar_global_republish_status',
    				    'compare'	=> 'NOT EXISTS',
					],
    			];
			}
    	}

    	if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
    		$args['meta_query'] = [
				'relation' => 'AND',
				[
					'key'		=> '_wpar_exclude_auto_republish',
    			    'compare'	=> 'NOT EXISTS'
				],
				[
				    'key'		=> 'wpar_global_republish_status',
    			    'compare'	=> 'NOT EXISTS'
				],
				[
				    'key'		=> 'wpar_single_republish_status',
    			    'compare'	=> 'NOT EXISTS'
				]
			];
		}

    	if ( $overwrite != 'none' && ! empty( $taxonomies ) ) {
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
						if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
						    $terms[$taxonomy_name][] = $term_id;
						}
					}
    		    }
			}
			
    	    if ( $overwrite == 'include' ) {
                if ( ! empty( $cats ) ) {
    		        $args['category__in'] = $cats;
    		    }

    		    if ( ! empty( $tags ) ) {
    				$args['tag__in'] = $tags;
    			}
    		} else if ( $overwrite == 'exclude' ) {
    			if( ! empty( $cats ) ) {
    		        $args['category__not_in'] = $cats;
    		    }

    		    if ( ! empty( $tags ) ) {
    		    	$args['tag__not_in'] = $tags;
    			}
			}
			
			if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
		    	if ( ! empty( $terms ) ) {
					$array = [];
    	    		$operator = 'IN';
    	    		if ( $overwrite == 'exclude' ) {
						$operator = 'NOT IN';
		    		}

		    		if ( count( $terms ) > 1 ) {
		    			$array['relation'] = 'OR';
		    		}

    	    		foreach ( $terms as $taxonomy => $term_ids ) {
    	    	    	$array[] = [
    	    	    		'taxonomy' => $taxonomy,
    	    	    		'field'    => 'term_id',
    	    	    		'terms'    => $term_ids,
    	    	    		'operator' => $operator,
		    			];
		    		}

    	    	    $args['tax_query'] = $array;
		    	}
		    }
		}
		
		$args = $this->do_filter( 'query_args', $args );
    
    	//error_log( print_r( $args, true ) );
    
    	// store post objects
		$this->query[] = get_posts( $args );
	}

	/**
	 * Complete
	 */
	private function complete()
	{
		$timestamp = current_time( 'timestamp', 0 );

		// update future reference
		update_option( 'wpar_last_global_cron_run', $timestamp );

		$query = $this->query;
		$post_types = $this->get_data( 'wpar_post_types', [ 'post' ] );
		$number_posts = $this->get_data( 'number_of_posts', 1 );
		$orderby = $this->get_data( 'wpar_republish_orderby', 'date' );
		$order = $this->get_data( 'wpar_republish_method', 'old_first' );
		$overwrite = $this->get_data( 'wpar_exclude_by_type', 'none' );
		$exclude_ids = $this->get_data( 'wpar_override_category_tag' );
    	$exclude_ids = preg_replace( [ '/[^\d,]/', '/(?<=,),+/', '/^,+/', '/,+$/' ], '', $exclude_ids );
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
    	    	'orderby'     => $orderby,
			];

			if ( ! empty( $order ) ) {
    	    	$args['order'] = 'ASC';
    	    	if ( $order == 'new_first' ) {
    	    		$args['order'] = 'DESC';
    	    	}
			}
			
			if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
		    	if ( $args['orderby'] != 'date' ) {
    	        	$args['orderby'] = 'date';
    	        }
		    }
        
    	    if ( ! empty( $exclude_ids ) ) {
    	    	if ( $overwrite == 'include' ) {
    	    	    $args['post__in'] = array_diff( $post_ids, explode( ',', $exclude_ids ) );
    	    	} elseif ( $overwrite == 'exclude' ) {
    	    		$args['post__in'] = array_unique( array_merge( $post_ids, explode( ',', $exclude_ids ) ) );
    	    	}
			}
			
			$args = $this->do_filter( 'post_query_args', $args );

			//error_log( print_r( $args, true ) );

			// get the required date time
			$datetime = $this->next_schedule( $timestamp, 'local' );

			$posts = get_posts( $args );
			
    	    if ( ! empty( $posts ) ) {
    	        foreach ( $posts as $post ) {
					// delete previosly scheduled hook if exists any.
					$this->unschedule_all_actions( 'wpar/global_republish_single_post', [ $post->ID ] );

					// schedule single post republish event
					$this->set_single_action( get_gmt_from_date( $datetime, 'U' ), 'wpar/global_republish_single_post', [ $post->ID ] );
							
					// update required post metas
					$this->update_meta( $post->ID, 'wpar_global_republish_status', 'pending' );
					$this->update_meta( $post->ID, '_wpar_global_republish_datetime', $datetime );
						
					if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
						// update required post metas
						$this->update_meta( $post->ID, 'wpar_filter_republish_status', 'pending' );
						$this->update_meta( $post->ID, '_wpar_filter_republish_datetime', $datetime );
                        
						// set log action
						$this->do_action( 'insert_log', $post->ID, 'cron', true, '', strtotime( $datetime ), 'dashicons-backup' );
					}
    	    	}
    	    }
		}
	}

	/**
	 * Generate Single cron time.
	 * 
	 * @param int     $timestamp Local Timestamp
	 * @param array   $weekdays  Available weekdays
	 * @param string  $format    Datetime format
	 * 
	 * @return int|string  Generated UTC timestamp
	 */
	private function next_schedule( $timestamp, $format = 'GMT' )
	{
		$cur_time = strtotime( date( 'H:i:s', $timestamp ) );
		
		$weekdays = $this->get_data( 'wpar_days', [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ] );
		$start_time = strtotime( $this->get_data( 'wpar_start_time', '05:00:00' ) );
		$end_time = strtotime( $this->get_data( 'wpar_end_time', '23:59:59' ) );
		$slop = $this->get_data( 'wpar_random_republish_interval', 14400 );
		if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			if ( ! in_array( $slop, [ '3600', '7200', '14400', '21600' ] ) ) {
				$slop = 14400;
			}
		}

		if ( $start_time >= $end_time ) {
			$start_time = strtotime( '05:00:00' );
		}

		$i = 1;
		while( $i <= 7 ) {
			$next_timestamp = strtotime( '+' . $i . ' days', $timestamp );
			$next_date = lcfirst( date( 'D', $next_timestamp ) );

			if ( in_array( $next_date, $weekdays ) ) {
			    break;
			}
			$i++;
		}

		$gap = mt_rand( 30, $slop );
		$final_end_time = $start_time + $gap;
		if( $final_end_time > $end_time ) {
			$final_end_time = $end_time;
		}
		$rand_time = mt_rand( $start_time, $final_end_time );
		
		$final_timestamp = $timestamp;
		if ( ! in_array( lcfirst( date( 'D', $timestamp ) ), $weekdays ) ) {
			$final_timestamp = $next_timestamp;
		}

		$new_time = $cur_time + $gap;
		if ( ( $new_time >= $start_time ) && ( $new_time <= $end_time ) ) {
			$datetime = $final_timestamp + $gap;
		} else {
	    	if ( $new_time > $end_time ) {
	    		$datetime = strtotime( date( 'Y-m-d', $next_timestamp ) . ' ' . date( 'H:i:s', $rand_time ) );
	    	} elseif ( $new_time < $start_time ) {
				$datetime = strtotime( date( 'Y-m-d', $final_timestamp ) . ' ' . date( 'H:i:s', $rand_time ) );
	    	} else {
	    		$datetime = $final_timestamp + $gap;
	    	}
		}
		
		$formatted_date = date( 'Y-m-d H:i:s', $datetime );
		if ( $format == 'local' ) {
			return $formatted_date;
		}

		return get_gmt_from_date( $formatted_date, 'U' );
	}

	/**
	 * Check if current run is actually eligible.
	 */
	private function valid_next_run()
	{
		$last = get_option( 'wpar_last_global_cron_run' );
		$current_time = current_time( 'timestamp', 0 );
		$interval = $this->get_data( 'wpar_minimun_republish_interval', 43200 );
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
	    	if ( $interval == 'custom' ) {
	    		$interval = MINUTE_IN_SECONDS * $this->get_data( 'republish_custom_interval', 60 );
			}
	    }
		
		$proceed = false; // switch
		if ( $this->slot_available( $current_time ) ) {
	    	if ( false === $last ) {
	    		$proceed = true;
	    	} elseif ( is_numeric( $last ) ) {
	    		if ( ( $current_time - $last ) >= $interval ) {
	    			$proceed = true;
	    		}
	    	}
	    }

		return $proceed;
	}

	/**
	 * Check if weekdays are available.
	 * 
	 * @param int $timestamp Local Timestamp
	 * 
	 * @return bool
	 */
	private function slot_available( $timestamp )
	{
		$start_time = strtotime( $this->get_data( 'wpar_start_time', '05:00:00' ) );
 		$end_time = strtotime( $this->get_data( 'wpar_end_time', '23:59:59' ) );
		$cur_time = strtotime( date( 'H:i:s', $timestamp ) );
		$weekdays = $this->get_data( 'wpar_days' );
		$next_date = lcfirst( date( 'D', $timestamp ) );
		$available = false;

		if ( ( $cur_time >= $start_time ) && ( $cur_time <= $end_time ) ) {
			if ( ! empty( $weekdays ) && in_array( $next_date, $weekdays ) ) {
			    $available = true;
			}
		}

		return $available;
	}

	/**
     * Check if has any future posts.
     * 
     * @since v1.1.7
     */
    private function has_future_posts( $post_type )
    {
		// cureent timestmap
		$timestamp = current_time( 'timestamp', 0 );

        // get future posts
        $posts = $this->do_filter( 'has_future_post_args', get_posts( [
			'numberposts' => -1,
			'post_type'   => $post_type,
            'sort_order'  => 'ASC',
            'post_status' => 'future',
            'date_query'  => [
                'year'  => date( 'Y', $timestamp ),
                'month' => date( 'n', $timestamp ),
                'day'   => date( 'j', $timestamp ),
            ],
		] ), $post_type );
		
        if ( ! empty( $posts ) && count( $posts ) > 0 && $this->do_filter( 'has_future_post_check', false ) ) {
            return true;
		}
		
        return false;
    }
}