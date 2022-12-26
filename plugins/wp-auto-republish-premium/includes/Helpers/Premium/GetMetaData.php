<?php
/**
 * Fetch next republish schedule.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Helpers\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Helpers\Premium;

use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Post Meta class.
 */
trait GetMetaData
{
	use HelperFunctions;

	/**
	 * Get post metadata.
	 * 
	 * @param int    $post_id  Post ID
	 * @param string $type     Output type
	 * @param bool   $column   Is admin column output
	 * 
	 * @return string
	 */
	protected function get_schedule( $post_id, $type = 'raw', $column = false )
	{
		$option = $this->get_meta( $post_id, '_wpar_repost_option' );
		$number = $this->get_meta( $post_id, '_wpar_repost_after_number' );
		$time = $this->get_meta( $post_id, '_wpar_repost_after_time' );
		$repeats = $this->get_meta( $post_id, '_wpar_repost_repeats' );
		$every = $this->get_meta( $post_id, '_wpar_repost_every' );
		$weekly = maybe_unserialize( $this->get_meta( $post_id, '_wpar_repost_on' ) );
		if ( empty( $weekly ) ) $weekly = [];
		
		$monthly = $this->get_meta( $post_id, '_wpar_repost_by' );
		$end = $this->get_meta( $post_id, '_wpar_repost_end' );
		$occurence = $this->get_meta( $post_id, '_wpar_repost_end_after' );
		$return = '';
		
		$repost_monthly_on = $this->build_meta( $post_id, '_wpar_repost_monthly_on', [], true );
		$on = strtotime( $this->get_meta( $post_id, '_wpar_repost_on_date' ) );
		$end_on = $this->get_meta( $post_id, '_wpar_repost_end_on' );
		$schedule = $this->get_meta( $post_id, '_wpar_repost_schedule' );
		$repost_at_time = $this->get_meta( $post_id, '_wpar_repost_schedule_datetime' );
		$schedule = ( $schedule ) ? strtotime( $schedule ) : false;
		$datetime = ( $repost_at_time ) ? strtotime( $repost_at_time ) : false;
		$today = date( 'm/d/Y', current_time( 'timestamp', 0 ) );
		$format = get_option( 'date_format' );
		
		switch( $option ) {
			case 'days':
			case 'date':
				if ( ! $column ) {
			    	if ( strtotime( $today ) == $schedule ) {
			    		$return .= sprintf( '%1$s, <strong>%2$s</strong>', __( 'Set today', 'wp-auto-republish' ), date_i18n( $format, $schedule ) );
			    	} elseif( strtotime( $today ) >= $schedule ) {
			    		$return .= sprintf( __( '%1$s <strong>%2$s</strong>', 'wp-auto-republish' ), __( 'Was set on', 'wp-auto-republish' ), date_i18n( $format, $schedule ) );
			    	} else {
			    		$return .= sprintf( '%1$s <strong>%2$s</strong>', __( 'Set on', 'wp-auto-republish' ), date_i18n( $format, $schedule ) );
			    	}
			    }
				break;
			
			case 'repeat':
				switch( $repeats ) {
					case 'yearly':
						$return .= sprintf(
							'%1$s <strong>%2$s</strong>',
							__( 'Set', 'wp-auto-republish' ),
							( $every > 1 ) ? sprintf( __( 'every %s years', 'wp-auto-republish' ), $every ) : __( 'Yearly', 'wp-auto-republish' )
						);
						break;
					
					case 'monthly':
						$return .= sprintf(
							'%1$s <strong>%2$s</strong> %3$s <strong>%4$s</strong> on <strong>%5$s</strong>',
							__( 'Set', 'wp-auto-republish' ),
							( $every > 1 ) ? sprintf( __( 'every %s months', 'wp-auto-republish' ), $every ) : __( 'Monthly', 'wp-auto-republish' ),
							__( 'on', 'wp-auto-republish' ),
							( $monthly == 'month' ) ? __( 'Day ', 'wp-auto-republish' ) . date_i18n( 'd', $schedule ) : __( 'the ', 'wp-auto-republish' ) . $this->get_nth_week( $schedule ) . ' ' . $this->get_day_by_name( lcfirst( date( 'l', $schedule ) ) ),
							( count( $repost_monthly_on ) == 12 ) ? __( 'All Months', 'wp-auto-republish' ) : implode( ', ', array_map( 'ucfirst', $repost_monthly_on ) )
						);
						break;
					
					case 'weekly':
						$return .= sprintf(
							'%1$s <strong>%2$s</strong> %3$s <strong>%4$s</strong>',
							__( 'Set', 'wp-auto-republish' ),
							( $every > 1 ) ? sprintf( __( 'every %s weeks', 'wp-auto-republish' ), $every ) : __( 'Weekly', 'wp-auto-republish' ),
							__( 'on', 'wp-auto-republish' ),
							$this->get_day_by_name( lcfirst( date( 'l', $schedule ) ) )
						);
						break;
					
					case 'daily':
						$return .= sprintf(
							'%1$s <strong>%2$s</strong>',
							__( 'Set', 'wp-auto-republish' ),
							( $every > 1 ) ? sprintf( __( 'every %s days', 'wp-auto-republish' ), $every ) : __( 'Daily', 'wp-auto-republish' )
						);
						break;

					case 'hourly':
						$return .= sprintf(
							'%1$s <strong>%2$s</strong>',
							__( 'Set', 'wp-auto-republish' ),
							( $every > 1 ) ? sprintf( __( 'every %s hours', 'wp-auto-republish' ), $every ) : __( 'Hourly', 'wp-auto-republish' )
						);
						break;

					default:
					case 'minutes':
						$return .= sprintf(
							'%1$s <strong>%2$s</strong>',
							__( 'Set', 'wp-auto-republish' ),
							( $every > 1 ) ? sprintf( __( 'every %s minutes', 'wp-auto-republish' ), $every ) : __( 'In Minutes', 'wp-auto-republish' )
						);
						break;
				}

				if ( $end == 'after' ) {
					$return .= sprintf( ' @ <strong>%1$s</strong> %2$s', $occurence, __( 'times', 'wp-auto-republish' ) );
				} else if ( $end == 'on' ) {
					$return .= sprintf( ', %1$s <strong>%2$s</strong>', __( 'until', 'wp-auto-republish' ), date_i18n( $format, strtotime( $end_on ) ) );
				}

				if ( ! $column ) {
				    $return .= sprintf( '. %1$s <strong>%2$s</strong>', __( 'Next republish schedule is on', 'wp-auto-republish' ), date_i18n( $format, $schedule ) );
				}
			break;
		}

		if ( $column && ! empty( $return ) ) {
			$return = sprintf( __( 'Republish %s' ), $return );
		}
		
		if ( $type == 'raw' ) {
			return $schedule;
		}
	
		if ( $type == 'datetime' ) {
			return $datetime;
		}

		return $return;
	}
	
	/**
	 * Generate week number.
	 * 
	 * @param int    $timestamp Post date
	 * 
	 * @return string
	 */
	private function get_nth_week( $timestamp )
	{
		$timestamp = is_numeric( $timestamp ) ? $timestamp : strtotime( $timestamp );
		$weekday   = date( 'l', $timestamp );
		$month     = date( 'M', $timestamp );   
		$ord       = 0;
	
		while( date( 'M', ( $timestamp = strtotime( '-1 week', $timestamp ) ) ) == $month ) {
			$ord++;
		}
	
		$weekdays = [ 'first', 'second', 'third', 'fourth', 'fifth' ];

		return $weekdays[$ord];
	}

	/**
	 * Generate week name.
	 * 
	 * @param string   $name  Weekday name
	 * @return string  $name  translated Weekday name
	 */
	private function get_day_by_name( $name )
	{
		$array = [
			'sunday'    => __( 'Sunday', 'wp-auto-republish' ),
			'monday'    => __( 'Monday', 'wp-auto-republish' ),
			'tuesday'   => __( 'Tuesday', 'wp-auto-republish' ),
			'wednesday' => __( 'Wednesday', 'wp-auto-republish' ),
			'thursday'  => __( 'Thursday', 'wp-auto-republish' ),
			'friday'    => __( 'Friday', 'wp-auto-republish' ),
			'saturday'  => __( 'Saturday', 'wp-auto-republish' )
		];
		
		return $array[$name];
	}
}