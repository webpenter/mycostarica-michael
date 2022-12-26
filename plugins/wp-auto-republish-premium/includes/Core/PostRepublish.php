<?php
/**
 * The Main file.
 *
 * @since      1.1.0
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
class PostRepublish
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/global_republish_single_post', 'do_republish' );
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$this->filter( 'wpar/update_process_args', 'get_title_name__premium_only', 10, 2 );
			$this->filter( 'wpar/clone_process_args', 'get_title_name__premium_only', 10, 2 );
		}
	}

	/**
	 * Trigger post update process.
	 * 
	 * @since v1.1.7
	 * @param int   $post_id   Post ID
	 */
	public function do_republish( $post_id )
	{
		if ( get_transient( 'wpar_global_pending_lock_' . $post_id ) === false ) {
			// check if meta exists
			$action = $this->get_meta( $post_id, 'wpar_republish_rule_action' );
			
			// run if post republish is actually enabled
			if ( $this->valid_republish( $post_id ) || $action ) {
				// check if given post is not published.
			    if ( 'publish' === get_post_status( $post_id ) ) {
				    $this->handle( $post_id );
			    }
			}
			
			// delete metas
			$this->delete_meta( $post_id, 'wpar_global_republish_status' );
			$this->delete_meta( $post_id, '_wpar_global_republish_datetime' );
			$this->delete_meta( $post_id, 'wpar_filter_republish_status' );
			$this->delete_meta( $post_id, '_wpar_filter_republish_datetime' );
			$this->delete_meta( $post_id, 'wpar_republish_rule_action' );
	    	    
	    	// lock republish query
	    	set_transient( 'wpar_global_pending_lock_' . $post_id, true, 10 );
	    }
	}

	/**
	 * Handle Trigger post update process.
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 */
	private function handle( $post_id, $action = 'repost' )
	{
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$action = $this->do_filter( 'global_republish_action', $this->get_update_action__premium_only( $post_id ), $post_id );
	    	$action_meta = $this->get_meta( $post_id, 'wpar_republish_rule_action' );
			if ( $action_meta ) {
				$action = $action_meta;
			}
			
			if ( $action == 'clone' ) {
	    		$this->clone_old_post__premium_only( $post_id );
	    	}
		}
		
		if ( $action == 'repost' ) {
			$this->update_old_post( $post_id );
		}
	}

	/**
	 * Run post update process.
	 * 
	 * @param int   $post_id  Post ID
	 * @param bool  $single   Check if it is a single republish event
	 * @param bool  $instant  Check if it is one click republish event
	 * 
	 * @return int $post_id
	 */
	protected function update_old_post( $post_id, $single = false, $instant = false )
	{
		$post = get_post( $post_id );
    	$timestamp = current_time( 'timestamp', 0 );

    	$pub_date = $this->get_meta( $post->ID, '_wpar_original_pub_date' );
    	if ( ! $pub_date && ( $post->post_status !== 'future' ) ) {
    		$this->update_meta( $post->ID, '_wpar_original_pub_date', $post->post_date );
    	}
		
		$new_time = $this->get_publish_time( $post->ID, $single, $instant );

		// remove kses filters
		kses_remove_filters();

        $args = [
	    	'ID'             => $post->ID,
	    	'post_date'      => $new_time,
	    	'post_date_gmt'  => get_gmt_from_date( $new_time )
	    ];
	    
		$args = $this->do_filter( 'update_process_args', $args, $post->ID, $post );
    
		//error_log( print_r( $args, true ) );
		
		wp_update_post( $args );

		$this->set_occurence( $post );

		if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
		    $this->do_action( 'clear_site_cache' );
		}
		
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			// update reference
			$this->update_meta( $post->ID, 'wpar_republish_meta_query', $new_time );

			$this->do_action( 'insert_log', $post->ID, 'republish' );
			$this->do_action( 'old_post_republished', $post->ID, $post, $new_time, 'republish' );
		}

		// reinit kses filters
		kses_init_filters();

		return $post_id;
	}

	/**
	 * Run post cloning process.
	 * 
	 * @since v1.1.7
	 * @param int   $post_id    Post ID
	 * @param bool  $single     Check if a single republish event
	 * @param bool  $instant    Check if one click republish event
	 * @param bool  $scheduled  Check if scheduled republish event
	 * 
	 * @return int $post_id
	 */
	protected function clone_old_post__premium_only( $post_id, $single = false, $instant = false, $scheduled = false )
	{
		$post = get_post( $post_id );
		$new_time = $this->get_publish_time( $post->ID, $single, $instant, $scheduled );
		$post_name = ( post_exists( $post->post_title ) ) ? $post->post_title . '-' . mt_rand( 200, 99999 ) : $post->post_name;

		// remove kses filters
		kses_remove_filters();

		/**
		 * new post data array
		 */
		$args = [
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $post->post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => ( $scheduled ) ? $post->post_name : sanitize_title( $this->do_filter( 'clone_post_link', $post_name, $post->ID ) ),
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => ( $scheduled ) ? 'future' : 'publish',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
			'post_mime_type' => $post->mime_type,
			'post_date'      => $new_time,
	    	'post_date_gmt'  => get_gmt_from_date( $new_time )
		];

		$args = $this->do_filter( 'clone_process_args', $args, $post->ID, $post );

		//error_log( print_r( $args, true ) );
	
		/**
		 * insert the post by wp_insert_post() function
		 */
		$new_post_id = wp_insert_post( $args );
	
		/**
		 * get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'slugs' ] );
			wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
		}

		/**
		 * get all current post metas ad set them to the new post
		 */
		$metas = get_post_custom( $post_id );
        foreach( $metas as $key => $values ) {
			if ( strpos( $key, 'wpar_' ) === false ) {
            	foreach ( $values as $value ) {
            		$this->add_meta( $new_post_id, $key, $value );
		    	}
		    }
		}

		// update reference for admin column
		$this->update_meta( $post->ID, 'wpar_republish_meta_query', $new_time );
		$this->update_meta( $new_post_id, 'wpar_original_post_id', $post->ID );

		$new_post = get_post( $new_post_id );

		if ( ! $scheduled ) {
		    $this->do_action( 'insert_log', $post->ID, 'clone' );
		    $this->do_action( 'old_post_republished', $new_post_id, $new_post, $new_time, 'clone' );
		}

		// reinit kses filters
		kses_init_filters();

		return $new_post_id;
	}

	/**
	 * Get new post published time.
	 * 
	 * @since v1.1.7
	 * @param int     $post_id  Post ID
	 * @param bool    $single 
	 * 
	 * @return string
	 */
	protected function get_update_action__premium_only( $post_id, $single = false )
	{
		$action = $this->get_data( 'wpar_republish_action', 'repost' );
		if ( $single ) {
	    	$action = $this->get_data( 'wpar_single_republish_action', 'repost' );
	    }

		$get_action = $this->get_meta( $post_id, '_wpar_repost_post_update_action' );
		if ( ! empty( $get_action ) && ( $get_action != 'default' ) ) {
			$action = $get_action;
		}

		return $action;
	}

	/**
	 * Get New Post title and URL if present.
	 * 
	 * @since v1.1.7
	 * @param array  $single  Republish arguments
	 * @param int    $post_id Post ID
	 * 
	 * @return array
	 */
	public function get_title_name__premium_only( $args, $post_id )
	{
		$post = get_post( $post_id );
		$action = $this->get_meta( $post->ID, 'wpar_republish_rule_action' );
        if ( $action ) {
			return $args;
		}

    	$title_on = $this->get_meta( $post->ID, '_wpar_repost_post_titles_active' );
		$update_url = $this->get_meta( $post->ID, '_wpar_repost_post_update_url' );
    	$get_new_title = $this->get_meta( $post->ID, '_wpar_repost_post_titles' );
    	$build_title = explode( ';;;', $get_new_title );
    	$build_title = array_diff( $build_title, [ $post->post_title ] );
		
		if ( count( $build_title ) >= 1 ) {
            $index = array_rand( $build_title, 1 );
    		$new_title = wp_strip_all_tags( $build_title[$index] );
			
			if ( isset( $title_on ) && $title_on == 'yes' && isset( $new_title ) ) {
				$args['post_title'] = $this->do_filter( 'republished_post_title', $new_title, $post_id );
				
				if ( isset( $update_url ) && $update_url == 'yes' ) {
					$post_name = ( post_exists( $new_title ) ) ? $new_title . '-' . mt_rand( 200, 99999 ) : $new_title;
					$args['post_name'] = sanitize_title( $this->do_filter( 'republished_post_link', $post_name, $post_id ) );
				}
    		}
		}

		return $args;
	}

	/**
	 * Get new post published time.
	 * 
	 * @since v1.1.7
	 * @param int   $post_id   Post ID
	 * @param bool  $single    Check if a single republish event
	 * @param bool  $instant   Check if one click republish event
	 * @param bool  $scheduled Check if scheduled republish event
	 * 
	 * @return string
	 */
	private function get_publish_time( $post_id, $single = false, $instant = false, $scheduled = false )
	{
		$post = get_post( $post_id );
    	$timestamp = current_time( 'timestamp', 0 );
		$interval = MINUTE_IN_SECONDS * mt_rand( 1, 15 );

    	if ( $this->get_data( 'wpar_republish_post_position', 'one' ) == 'one' ) {
    		$datetime = $this->get_meta( $post_id, '_wpar_global_republish_datetime' );
			if ( ! empty( $datetime ) && ( $timestamp >= strtotime( $datetime ) ) ) {
			    $new_time = $datetime;
			} else {
				$new_time = current_time( 'mysql' );
			}
    	} else {
    		$lastposts = get_posts( [
    			'post_type'      => $post->post_type,
                'numberposts'    => 1,
    			'offset'         => 1,
    			'post_status'    => 'publish',
				'order'          => 'DESC',
				'orderby'        => 'date',
    		] );
    		if ( ! empty( $lastposts ) ) {
    		    foreach ( $lastposts as $lastpost ) {
					$post_date = strtotime( $lastpost->post_date );
					$post_date = $post_date + $interval;
    		    	$new_time = date( 'Y-m-d H:i:s', $post_date );
    		    }
    	    } else {
    			$new_time = current_time( 'mysql' );
    	    }
		}
		
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			if ( $single ) {
				$datetime = $this->get_meta( $post_id, '_wpar_repost_schedule_datetime' );
				if ( ! empty( $datetime ) && ( $timestamp >= strtotime( $datetime ) ) ) {
				    $new_time = $datetime;
				} else {
				 	$new_time = current_time( 'mysql' );
				}
			}
			
			if ( $instant ) {
				$new_time = current_time( 'mysql' );
			}
			
			if ( $scheduled ) {
				$new_time = date( 'Y-m-d H:i:s', $timestamp + 86400 );
			}
		}

		return $this->do_filter( 'next_scheduled_timestamp', $new_time, $post_id, $single, $instant, $scheduled );
	}

	/**
	 * Custom post type support.
	 *
	 * @param object $post WP Post object.
	 */
    private function set_occurence( $post )
	{
        $repeat = $this->get_meta( $post->ID, '_wpar_post_republish_occurrence' );
    	if ( ! empty( $repeat ) && is_numeric( $repeat ) ) {
    		$repeat++;
    	} else {
    		$repeat = 1;
    	}
		
		$this->update_meta( $post->ID, '_wpar_post_republish_occurrence', $repeat );
	}

	/**
	 * Check if republish is not disabled.
	 *
	 * @param int $post_id The post ID.
	 * 
	 * @return bool
	 */
	private function valid_republish( $post_id )
	{
		if ( ! $this->is_enabled( 'enable_plugin', true ) ) {
			return false;
		}

		$post_types = $this->get_data( 'wpar_post_types', [ 'post' ] );

		// get single republish event status
		$global_pending = $this->get_meta( $post_id , 'wpar_global_republish_status' );
		$single_pending = $this->get_meta( $post_id , 'wpar_single_republish_status' );

		$proceed = false;
        if ( in_array( get_post_type( $post_id ), $post_types ) && $global_pending && ! $single_pending ) {
			$proceed = true;
		}

		return $proceed;
	}
}