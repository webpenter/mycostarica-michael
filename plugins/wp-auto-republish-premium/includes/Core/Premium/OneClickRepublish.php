<?php
/**
 * Premium Features.
 *
 * @since      1.2.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Core\Premium\SingleRepublish;

defined( 'ABSPATH' ) || exit;

/**
 * Actions class.
 */
class OneClickRepublish extends SingleRepublish
{
	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'admin_init', 'one_click_republish' );
		$this->action( 'publish_future_post', 'republish_post' );
		$this->action( 'admin_notices', 'success_notice' );
		$this->filter( 'post_row_actions', 'row_actions', 20, 2 );
		$this->filter( 'page_row_actions', 'row_actions', 20, 2 );
		$this->filter( 'display_post_states', 'post_states', 20, 2 );
		$this->filter( 'wp_insert_post_data', 'lock_status', 99, 2 );
	}

	/**
	 * Register post row action.
	 * 
	 * @param array   $actions  Row actions
	 * @param object  $post     Post Object
	 * 
	 * @return array  $actions  Filtered Row actions
	 */
	public function row_actions( $actions, $post )
	{
		if ( ! $this->is_enabled( 'enable_instant_republishing', true ) ) {
			return $actions;
		}

		$post_types = $this->do_filter( 'republish_action_post_types', $this->get_data( 'post_types_list_single', [ 'post' ] ), $post );
		if ( ! $this->user_has_cap__premium_only() || ! in_array( $post->post_type, $post_types ) ) {
			return $actions;
		}
		
		$build_url = add_query_arg( [ 'post_type' => $post->post_type, 'wpar_action' => 'republish', 'wpar_post_id' => $post->ID ], admin_url( 'edit.php' ) );
		$build_share_url = add_query_arg( [ 'post_type' => $post->post_type, 'wpar_action' => 'share', 'wpar_post_id' => $post->ID ], admin_url( 'edit.php' ) );
	
		if ( ! in_array( $post->post_status, [ 'trash', 'auto-draft', 'future' ] ) ) {
		    $actions['wpar_republish'] = '<a href="' . wp_nonce_url( add_query_arg( 'wpar_type', 'instant', $build_url ), 'wpar_republish_' . $post->ID ) . '" aria-label="'. esc_attr( sprintf( __( 'Republish %s now', 'wp-auto-republish' ), _draft_or_post_title( $post ) ) ) . '">' . __( 'Republish', 'wp-auto-republish' ) . '</a>';
			if ( $this->do_filter( 'show_clone_action', true ) ) {
			    $actions['wpar_clone'] = '<a href="' . wp_nonce_url( add_query_arg( 'wpar_type', 'duplicate', $build_url ), 'wpar_republish_' . $post->ID ) . '" aria-label="'. esc_attr( sprintf( __( 'Republish %s now', 'wp-auto-republish' ), _draft_or_post_title( $post ) ) ) . '">' . __( 'Clone', 'wp-auto-republish' ) . '</a>';
			}
			if ( $this->do_filter( 'show_rewrite_action', true ) ) {
			    $actions['wpar_scheduled'] = '<a href="' . wp_nonce_url( add_query_arg( 'wpar_type', 'scheduled', $build_url ), 'wpar_republish_' . $post->ID ) . '" aria-label="'. esc_attr( sprintf( __( 'Republish %s now', 'wp-auto-republish' ), _draft_or_post_title( $post ) ) ) . '">' . __( 'Rewrite', 'wp-auto-republish' ) . '</a>';
			}
			if ( $this->do_filter( 'show_share_action', true ) && $this->is_social_enabled__premium_only() ) {
			    $actions['wpar_share'] = '<a href="' . wp_nonce_url( $build_share_url, 'wpar_republish_' . $post->ID ) . '" aria-label="'. esc_attr( sprintf( __( 'Share %s now', 'wp-auto-republish' ), _draft_or_post_title( $post ) ) ) . '">' . __( 'Share', 'wp-auto-republish' ) . '</a>';
			}
		}
	
		return $actions;
	}

	/**
	 * Trigger One Click republish event.
	 */
	public function one_click_republish()
	{
		if ( ! isset( $_GET['wpar_action'], $_GET['wpar_post_id'] ) ) {
			return;
		}

		if ( ! empty( $_GET['wpar_post_id'] ) && ! empty( $_GET['wpar_action'] ) ) {

			// check nonce
			check_admin_referer( 'wpar_republish_' . esc_attr( sanitize_key( $_GET['wpar_post_id'] ) ) );
		    	
			$post = get_post( esc_attr( absint( $_GET['wpar_post_id'] ) ) );

		    if ( 'republish' === esc_attr( $_GET['wpar_action'] ) ) {
		    	if ( 'scheduled' === esc_attr( $_GET['wpar_type'] ) ) {
		    		// run republish process
		    		$new_post_id = $this->clone_old_post__premium_only( $post->ID, false, false, true );
		    		
		    		$this->update_meta( $new_post_id, 'wpar_filter_republish_status', 'pending' );
		    		$this->update_meta( $new_post_id, '_wpar_filter_republish_datetime', get_post( $new_post_id )->post_date );
		    					
		    		wp_safe_redirect( get_edit_post_link( $new_post_id, 'edit' ) );
		    	    exit;
		    	
		    	} else {
                    // check republish type
		    		if ( 'instant' === esc_attr( $_GET['wpar_type'] )  ) {
		    			// run republish  process
		    			$post_id = $this->run_update_process( $post->ID, false, true );
		    			//$this->update_single_schedule( $post->ID );
		    		} elseif ( 'duplicate' === esc_attr( $_GET['wpar_type'] )  ) {
		    			// run republish  process
		    			$post_id = $this->clone_old_post__premium_only( $post->ID, false, true );
		    		}
		    
		    		$post_types = get_post_type_object( get_post_type( $post_id ) );
		    
		    		// set temporary transient for admin notice
		    		set_transient( 'wpar_instant_republish_done', ucfirst( $post_types->labels->singular_name ) );
		    
		    		wp_safe_redirect( remove_query_arg( [ 'wpar_action', 'wpar_post_id', 'wpar_type', '_wpnonce' ] ) );
		    		exit;
		    	}
		    } elseif ( 'share' === esc_attr( $_GET['wpar_action'] ) ) {
                // do social share
				$this->do_action( 'do_social_share', $post->ID, $post );

				$post_types = get_post_type_object( get_post_type( $post->ID ) );

				// set temporary transient for admin notice
				set_transient( 'wpar_instant_social_share_done', ucfirst( $post_types->labels->singular_name ) );

				wp_safe_redirect( remove_query_arg( [ 'wpar_action', 'wpar_post_id', 'wpar_type', '_wpnonce' ] ) );
		    	exit;
			}
		}
	}

	/**
	 * Filter admin states to show scheduled republish nag.
	 * 
	 * @param array   $states   Post States
	 * @param object  $post     Post Object
	 */
	public function post_states( $states, $post )
	{
		// If post status is not future, return default states
		if ( ! in_array( $post->post_status, [ 'future', 'trash' ] ) ) {
			return $states;
		}

		$scheduled_pending = $this->get_meta( $post->ID, 'wpar_original_post_id' );
    	if ( $scheduled_pending ) {
		    // Adds the label of the current post status
		    $states['wpar_scheduled'] = sprintf( __( 'Republish Scheduled (#%s)', 'wp-auto-republish' ), $scheduled_pending );
		}

		return $states;
	}

	/**
	 * Lock post info on update.
	 * 
	 * @param object   $data     Old Data
	 * @param object   $postarr  Current Data
	 * 
	 * @return object  $data
	 */
	public function lock_status( $data, $postarr )
	{
		// allow only trash post status
		if ( $postarr['post_status'] === 'trash' ) {
			return $data;
		}

		$timestamp = current_time( 'timestamp', 0 );
		$new_time = date( 'Y-m-d H:i:s', $timestamp + 86400 ); // add extra 1 day

        // check if is a actual scheduled republishing
		$scheduled_pending = $this->get_meta( $postarr['ID'], 'wpar_original_post_id' );
    	if ( isset( $postarr['post_date'] ) && $scheduled_pending ) {
		    $data['post_status'] = 'future';
			// prevent backwards timestamp
			if ( $timestamp >= strtotime( $postarr['post_date'] ) ) {
				$data['post_date'] = $new_time;
				$data['post_date_gmt'] = get_gmt_from_date( $new_time );
				$data['post_modified'] = $new_time;
				$data['post_modified_gmt'] = get_gmt_from_date( $new_time );
			}

			// update required post meta
			$this->update_meta( $postarr['ID'], '_wpar_filter_republish_datetime', $postarr['post_date'] );
		}

		return $data;
	}

	/**
	 * Trigger on future post publish event.
	 * 
	 * @param int   $post_id  WP Post ID
	 */
	public function republish_post( $post_id )
	{
		$orig_id = $this->get_meta( $post_id, 'wpar_original_post_id' );
		
		// break early if given post is not an actual scheduled post created by this plugin.
		if ( ! $orig_id ) {
			return $post_id;
		}

		$orig = get_post( $orig_id );
		$post = get_post( $post_id );
		
		$post->post_name = $orig->post_name;
		$post->guid = $orig->guid;
		$post->post_parent = $orig->post_parent;
		$post->comment_count = $orig->comment_count;
		$post->post_status = 'publish';
		$post->post_date = current_time( 'mysql' );
		$post->post_date_gmt = current_time( 'mysql', 1 );

		$this->delete_meta( $post->ID, 'wpar_original_post_id' );
		$this->delete_meta( $post->ID, 'wpar_filter_republish_status' );

		wp_delete_post( $orig->ID, true );

		// remove kses filters
		kses_remove_filters();

		wp_update_post( $post );

		// reinit kses filters
		kses_init_filters();
		
		$this->do_action( 'insert_log', $post->ID, 'scheduled' );
		$this->do_action( 'old_post_republished', $post->ID, $post, $post->post_date, 'scheduled' );

		return $post_id;
	}

	/**
	 * Show internal admin notices.
	 */
	public function success_notice()
	{
		if ( get_transient( 'wpar_instant_republish_done' ) !== false ) { ?>
			<div class="notice notice-success is-dismissible"><p><?php printf( __( '%s republished and rescheduled (if any).', 'wp-auto-republish' ), get_transient( 'wpar_instant_republish_done' ) ); ?></p></div><?php
			delete_transient( 'wpar_instant_republish_done' );
		}

		if ( get_transient( 'wpar_instant_social_share_done' ) !== false ) { ?>
			<div class="notice notice-success is-dismissible"><p><?php printf( __( '%s sucessfully shared to the Social Media(s).', 'wp-auto-republish' ), get_transient( 'wpar_instant_social_share_done' ) ); ?></p></div><?php
			delete_transient( 'wpar_instant_social_share_done' );
		}
	}

}