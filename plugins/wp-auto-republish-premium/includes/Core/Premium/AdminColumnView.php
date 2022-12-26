<?php
/**
 * Admin column.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\Premium\GetMetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Admin column class.
 */
class AdminColumnView
{
	use Hooker, GetMetaData;

	/**
	 * Register functions.
	 */
	public function register() 
	{
		$this->action( 'admin_init', 'generate_column' );
		$this->action( 'pre_get_posts', 'sort_column' );
		$this->action( 'admin_head-edit.php', 'style' );
	}

	/**
	 * Register admin columns.
	 */
	public function generate_column()
	{
		$post_types = array_unique( array_merge( $this->get_data( 'wpar_post_types', [ 'post' ] ), $this->get_data( 'post_types_list_single', [ 'post' ] ) ) );
		foreach ( $post_types as $post_type ) {
			$this->filter( "manage_edit-{$post_type}_columns", 'column_title', 10, 1 );
			$this->action( "manage_edit-{$post_type}_sortable_columns", 'column_sortable', 10, 2 );
			$this->filter( "manage_{$post_type}_posts_columns", 'column_title', 10, 1 );
			$this->filter( "manage_{$post_type}_sortable_columns", 'column_sortable', 10, 2 );
			$this->action( "manage_{$post_type}_posts_custom_column", 'column_data', 10, 2 );
		}
	}

	/**
	 * Generate column data.
	 * 
	 * @param string   $column   Column name
	 * @param int      $post_id  Post ID
	 * 
	 * @return string  $time
	 */
	public function column_data( $column, $post_id )
	{
		switch ( $column ) {
			case 'republish':
				$timestamp = current_time( 'timestamp', 0 );
				$post = get_post( $post_id );

				$published_date = $this->get_meta( $post_id, '_wpar_original_pub_date' );
				$global_pending = $this->get_meta( $post_id, 'wpar_global_republish_status' );
				$global_schedule = $this->get_meta( $post_id, '_wpar_global_republish_datetime' );
				$single_pending = $this->get_meta( $post_id, 'wpar_single_republish_status' );
				$single_schedule = $this->get_meta( $post_id, '_wpar_repost_schedule_datetime' );

				$format = $this->do_filter( 'admin_column_date_format', 'M j, Y' );
				$full_format = $this->do_filter( 'admin_column_full_datetime_format', get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ) );
				
				$orig_id = $this->get_meta( $post->ID, 'wpar_original_post_id' );

				if ( ! $orig_id ) {
			    	if ( $published_date ) {
			    		$time = sprintf(
			    			'<span title="%1$s">%2$s: <strong>%3$s</strong></span><br />
			    			<span title="%4$s">%5$s: <strong>%6$s</strong></span><br />',
			    			date_i18n( $full_format, strtotime( $post->post_date ) ), __( 'Republished', 'wp-auto-republish' ), date_i18n( $format, strtotime( $post->post_date ) ),
			    			date_i18n( $full_format, strtotime( $published_date ) ), __( 'First Published', 'wp-auto-republish' ), date_i18n( $format, strtotime( $published_date ) )
			    		);
			    	} else {
			    		$time = sprintf(
			    			__( 'Not republished yet.<br />Published %s ago<br />', 'wp-auto-republish' ),
			    			human_time_diff( get_the_time( 'U', $post ), current_time( 'timestamp' ) )
			    		);
			    	}
			    } else {
					$time = '';
				}

				$in_queue = false;
				if ( $global_pending && $global_schedule ) {
				    $in_queue = $global_schedule;
				}

				if ( $single_pending && $single_schedule ) {
					$in_queue = $single_schedule;
				}

				if ( $orig_id ) {
					$in_queue = get_the_time( 'Y-m-d H:i:s' );
					$time .= sprintf( '<span>%1$s: <strong><a href="%2$s" target="_blank">%3$s</a></strong></span><br />',
					    __( 'Original', 'wp-auto-republish' ),
					    get_the_permalink( $orig_id ),
					    sprintf( __( '%s', 'wp-auto-republish' ), wp_trim_words( get_the_title( $orig_id ), 8 ) )
					);
				}

				if ( $in_queue ) {
					$new_timestamp = strtotime( $in_queue );
                    $timediff = $new_timestamp - $timestamp;
					if ( ( $timediff <= 86400 ) && ( $timediff > 0 ) ) {
						$time .= sprintf(
							'<abbr title="%1$s">%2$s %3$s</abbr><br />',
							date_i18n( $full_format, $new_timestamp ),
							__( 'Republication starts in', 'wp-auto-republish' ),
							human_time_diff( $timestamp, $new_timestamp )
						);
					} elseif ( $timediff <= 0 ) {
						$time .= sprintf(
							'<abbr title="%1$s">%2$s</abbr><br />',
							date_i18n( $full_format, $new_timestamp ),
							__( 'Republication in queue', 'wp-auto-republish' )
						);
					} elseif ( $timediff > 86400 ) {
						$time .= sprintf(
							'<abbr title="%1$s">%2$s: <strong>%3$s</strong></abbr><br />',
							date_i18n( $full_format, $new_timestamp ), __( 'Next Republish', 'wp-auto-republish' ), date_i18n( $format, $new_timestamp )
						);
					}
					
                    // show republish schedules
					$time .= $this->get_schedule( $post_id, 'edit', true );
				}

				if ( $post->post_status !== 'publish' ) {
					$time = __( 'Post is not published.', 'wp-auto-republish' );
				}

			echo $time;
			break;
		}
	}

	/**
	 * Column title.
	 * 
	 * @param string   $column  Column name
	 * @return string  $column  Filtered column
	 */
	public function column_title( $column )
	{
		$column['republish'] = __( 'Last Republished', 'wp-auto-republish' );
		
		return $column;
	}
	
	/**
	 * Make Column sortable.
	 * 
	 * @param string   $column  Column name
	 * @return string  $column  Filtered column
	 */
	public function column_sortable( $column )
	{
		$column['republish'] = 'republish';
		
		return $column;
	}
	
	/**
	 * Sort Column.
	 * 
	 * @param object $query  WP_Query Object
	 */
	public function sort_column( $query )
	{
		if ( ! is_admin() ) {
			return;
		}
		
		$orderby = $query->get( 'orderby' );
		$meta_query = [
			[
				'key'     => 'wpar_republish_meta_query',
				'compare' => 'EXISTS'
			]
		];
	
		if ( 'republish' === $orderby ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'wpar_republish_meta_query' );
			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Column custom CSS.
	 */
	public function style()
	{
		echo '<style type="text/css">.fixed th.column-republish { width: 200px; }</style>'."\n";
	}
}