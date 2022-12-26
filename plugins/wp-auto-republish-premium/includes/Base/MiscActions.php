<?php
/**
 * Misc Action links.
 *
 * @since      1.2.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Base;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Misc Action links class.
 */
class MiscActions
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register() 
	{
		$this->action( 'wpar/after_plugin_uninstall', 'meta_cleanup', 30 );
		$this->action( 'wpar/after_plugin_uninstall', 'remove_actions', 5 );
		$this->action( 'wpar/remove_post_metadata', 'meta_cleanup', 20 );
		$this->action( 'wpar/remove_post_metadata', 'remove_actions', 5 );
		$this->action( 'wpar/deschedule_posts', 'deschedule_posts' );
	}

	/**
	 * Post meta cleanup.
	 */
	public function meta_cleanup()
	{
		$post_types = array_unique( array_merge( $this->get_data( 'wpar_post_types', [ 'post' ] ), $this->get_data( 'post_types_list_single', [ 'post' ] ) ) );
		$args = [
			'post_type'    => $post_types,
			'numberposts'  => -1,
			'post_status'  => [ 'publish', 'future', 'private' ]
		];

		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
	    	foreach ( $posts as $post ) {
				$metas = get_post_custom( $post->ID );
                foreach( $metas as $key => $values ) {
					if ( strpos( $key, 'wpar_' ) !== false ) {
				    	if ( $key != '_wpar_original_pub_date' ) {
				        	$this->delete_meta( $post->ID, $key );
				    	}
				    }
	    		}
	    	}
		}
	}

	/**
	 * Remove actions.
	 */
	public function remove_actions()
	{
		$post_types = array_unique( array_merge( $this->get_data( 'wpar_post_types', [ 'post' ] ), $this->get_data( 'post_types_list_single', [ 'post' ] ) ) );
		$args = [
			'post_type'   => $post_types,
			'numberposts' => -1,
			'post_status' => 'publish',
			'meta_query'  => [
				'relation' => 'OR',
				[
				    'key'		=> 'wpar_global_republish_status',
    			    'compare'	=> 'EXISTS'
			    ],
				[
				    'key'		=> 'wpar_single_republish_status',
    			    'compare'	=> 'EXISTS'
			    ]
			]
		];

		$args = $this->do_filter( 'remove_actions_args', $args );

		//error_log( print_r( $args, true ) );
	
		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				// get republish time from post meta
				$this->unschedule_all_actions( 'wpar/global_republish_single_post', [ $post->ID ] );
				if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
				    $this->unschedule_all_actions( 'wpar/run_single_republish', [ $post->ID ] );
				}
			}
		}
	}

	/**
	 * Remove actions.
	 */
	public function deschedule_posts()
	{
		$post_types = array_unique( array_merge( $this->get_data( 'wpar_post_types', [ 'post' ] ), $this->get_data( 'post_types_list_single', [ 'post' ] ) ) );
		$args = [
			'post_type'   => $post_types,
			'numberposts' => -1,
			'post_status' => [ 'publish', 'future', 'private' ],
			'meta_query'  => [
				[
				    'key'		=> '_wpar_original_pub_date',
    			    'compare'	=> 'EXISTS'
			    ]
			]
		];

		$args = $this->do_filter( 'deschedule_posts_args', $args );

		//error_log( print_r( $args, true ) );
	
		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				// get original published date
				$pub_date = $this->get_meta( $post->ID, '_wpar_original_pub_date' );
				
				// update posts
				wp_update_post( [ 
					'ID'             => $post->ID,
					'post_date'      => $pub_date,
	    	        'post_date_gmt'  => get_gmt_from_date( $pub_date )
				] );

				// delete old meta
				$this->delete_meta( $post->ID, '_wpar_original_pub_date' );
			}
		}
	}
}