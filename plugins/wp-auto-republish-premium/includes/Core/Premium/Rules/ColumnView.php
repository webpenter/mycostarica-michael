<?php
/**
 * Admin column.
 *
 * @since      1.2.5
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium\Rules
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Rules;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\Premium\GetMetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Admin column class.
 */
class ColumnView
{
	use Hooker, GetMetaData;

	/**
	 * Register functions.
	 */
	public function register() 
	{
		$this->filter( 'manage_edit-republish_rule_columns', 'column_title', 10, 1 );;
		$this->filter( 'manage_republish_rule_posts_columns', 'column_title', 10, 1 );
		$this->action( 'manage_republish_rule_posts_custom_column', 'column_data', 10, 2 );
		$this->action( 'admin_head-edit.php', 'style' );
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
			case 'republish_rule':
				$timestamp = current_time( 'timestamp', 0 );
				$post = get_post( $post_id );

				$format = $this->do_filter( 'admin_column_date_format', 'M j, Y' );
				$full_format = $this->do_filter( 'admin_column_full_datetime_format', get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ) );

			    $schedule = $this->get_meta( $post_id, 'wpar_republish_rule_next_timestamp' );
				$time = '';

				if ( $schedule ) {
					$new_timestamp = strtotime( $schedule );
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
				} else {
					$time = __( 'Rule is not scheduled.', 'wp-auto-republish' );
				}

				if ( $post->post_status !== 'publish' ) {
					$time = __( 'Rule is not published.', 'wp-auto-republish' );
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
		$column['republish_rule'] = __( 'Republish Scheduled', 'wp-auto-republish' );
		
		return $column;
	}
	
	/**
	 * Column custom CSS.
	 */
	public function style()
	{
		echo '<style type="text/css">.fixed th.column-republish_rule { width: 200px; }</style>'."\n";
	}
}