<?php
/**
 * Rewrite post permalinks.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\SettingsData;

defined( 'ABSPATH' ) || exit;

/**
 * Permalink Rewrite class.
 */
class RewritePermainks
{
	use Hooker, SettingsData;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'init','rewrite_tag' );
        $this->filter( 'post_link', 'post_permalink', 10, 2 );
	}

	/**
	 * Register rewrite tags.
	 */
	public function rewrite_tag()
	{
		add_rewrite_tag( '%wpar_year%', '([0-9]{4})' );
		add_rewrite_tag( '%wpar_monthnum%', '([0-9]{2})' );
		add_rewrite_tag( '%wpar_day%', '([0-9]{2})' );
		add_rewrite_tag( '%wpar_hour%', '([0-9]{2})' );
		add_rewrite_tag( '%wpar_minute%', '([0-9]{2})' );
		add_rewrite_tag( '%wpar_second%', '([0-9]{2})' );
	}

	/**
	 * Filter post permalinks.
	 *
	 * @param string $permalink   Original post permalink.
	 * @param object $post        WordPress Post object.
	 *
	 * @return string             Filtered permalink
	 */
	public function post_permalink( $permalink, $post ) {
		$original_date = $this->get_meta( $post->ID, '_wpar_original_pub_date' );
		if ( ! $original_date ) {
		    $original_date = $post->post_date;
		}
		
		$year = date( 'Y', strtotime( $original_date ) );
		$month = date( 'm', strtotime( $original_date ) );
		$day = date( 'd', strtotime( $original_date ) );
		$hour = date( 'H', strtotime( $original_date ) );
		$minute = date( 'i', strtotime( $original_date ) );
		$second = date( 's', strtotime( $original_date ) );

		$rewritecode = [
			'%wpar_year%',
			'%wpar_monthnum%',
			'%wpar_day%',
			'%wpar_hour%',
			'%wpar_minute%',
			'%wpar_second%'
		];

		$rewritereplace = [
			$year,
			$month,
			$day,
			$hour,
			$minute,
			$second
		];

		if( $post->post_type === 'post' ) {
		    return str_replace( $rewritecode, $rewritereplace, $permalink );
		}

		return $permalink;
	}
}