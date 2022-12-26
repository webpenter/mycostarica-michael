<?php
/**
 * Premium Features.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Actions class.
 */
class ExtendedActions
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->filter( 'wpar/minimum_republish_interval', 'custom_interval', 99 );
		$this->filter( 'wpar/republish_eligibility_age', 'post_ages' );
		$this->filter( 'wpar/random_republish_interval', 'random_interval' );
		$this->filter( 'wpar/republish_orderby_items', 'republish_orderby' );
	}

	/**
	 * Add custom republish intervals.
	 */
	public function custom_interval( $items )
	{
		unset( $items['custom'] );
		$items['custom'] = __( 'Custom Interval', 'wp-auto-republish' );

		return $items;
	}

	/**
	 * Add custom post ages.
	 */
	public function post_ages( $items )
	{
		for ( $i = 1; $i<=18; $i++ ) {
			unset( $items['premium_' . $i] );
		}

		$elements = [
			'0'    => __( 'No Age Limit', 'wp-auto-republish' ),
			'1'    => __( '1 Day', 'wp-auto-republish' ),
			'2'    => __( '2 Days', 'wp-auto-republish' ),
			'3'    => __( '3 Days', 'wp-auto-republish' ),
			'5'    => __( '5 Days', 'wp-auto-republish' ),
			'7'    => __( '7 Days', 'wp-auto-republish' ),
			'10'   => __( '10 Days', 'wp-auto-republish' ),
			'14'   => __( '14 Days', 'wp-auto-republish' ),
			'21'   => __( '21 Days', 'wp-auto-republish' ),
			'28'   => __( '28 Days', 'wp-auto-republish' ),
			'1460' => __( '1460 Days (4 Years)', 'wp-auto-republish' ),
			'1825' => __( '1825 Days (5 Years)', 'wp-auto-republish' ),
			'2190' => __( '2190 Days (6 Years)', 'wp-auto-republish' ),
			'2555' => __( '2555 Days (7 Years)', 'wp-auto-republish' ),
			'2920' => __( '2920 Days (8 Years)', 'wp-auto-republish' ),
			'3285' => __( '3285 Days (9 Years)', 'wp-auto-republish' ),
			'3650' => __( '3650 Days (10 Years)', 'wp-auto-republish' )
		];

		foreach ( $elements as $key => $value ) {
			$items[$key] = $value;
		}
		
		ksort( $items );

		$items['custom'] = __( 'Custom Age Limit', 'wp-auto-republish' );

		return array_unique( $items );
	}

	/**
	 * Add custom republish intervals.
	 */
	public function random_interval( $items )
	{
		for ( $i = 1; $i<=10; $i++ ) {
			unset( $items['premium_' . $i] );
		}

		$elements = [
			'60'      => __( 'No Randomness', 'wp-auto-republish' ),
			'300'     => __( 'Upto 5 Minutes', 'wp-auto-republish' ),
			'600'     => __( 'Upto 10 Minutes', 'wp-auto-republish' ),
			'900'     => __( 'Upto 15 Minutes', 'wp-auto-republish' ),
			'1200'    => __( 'Upto 20 Minutes', 'wp-auto-republish' ),
			'1800'    => __( 'Upto 30 Minutes', 'wp-auto-republish' ),
			'2700'    => __( 'Upto 45 Minutes', 'wp-auto-republish' ),
			'28800'   => __( 'Upto 8 hours', 'wp-auto-republish' ),
			'43200'   => __( 'Upto 12 hours', 'wp-auto-republish' ),
			'86400'   => __( 'Upto 24 hours', 'wp-auto-republish' )
		];

		foreach ( $elements as $key => $value ) {
			$items[$key] = $value;
		}

		ksort( $items );

		return array_unique( $items );
	}

	/**
	 * Add custom republish intervals.
	 */
	public function republish_orderby()
	{
		$items = [];
		$elements = [
			'date'           => __( 'Post Date', 'wp-auto-republish' ),
			'ID'             => __( 'Post ID', 'wp-auto-republish' ),
			'author'         => __( 'Post Author', 'wp-auto-republish' ),
			'title'          => __( 'Post Title', 'wp-auto-republish' ),
			'rand'           => __( 'Random Selection', 'wp-auto-republish' ),
			'comment_count'  => __( 'Comment Count', 'wp-auto-republish' ),
			'relevance'      => __( 'Relevance', 'wp-auto-republish' ),
			'menu_order'     => __( 'Menu Order', 'wp-auto-republish' ),
		];

		foreach ( $elements as $key => $value ) {
			$items[$key] = $value;
		}

		return $items;
	}
}