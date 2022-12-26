<?php
/**
 * Filter post statuses.
 *
 * @since      1.2.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Filter post statuses class.
 */
class PostStatusFilters
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'admin_init', 'init' );
	}

	/**
	 * Hook to views content action.
	 */
	public function init()
	{
		$this->filter( 'pre_get_posts', 'filter_posts' );

		$post_types = array_unique( array_merge( $this->get_data( 'wpar_post_types', [ 'post' ] ), $this->get_data( 'post_types_list_single', [ 'post' ] ) ) );
		foreach( $post_types as $post_type ) {
			$this->action( "views_edit-{$post_type}", 'content_filter' );
		}
	}

	/**
	 * Add view to filter list for Pending republish posts.
	 *
	 * @param array $views An array of available list table views.
	 */
	public function content_filter( $views )
	{
		global $typenow;

		$enabled = $this->do_filter( 'show_filter_link', true, $typenow );
		$current = empty( $_GET['republish_pending'] ) ? '' : ' class="current" aria-current="page"';
		$get_posts = get_posts(
			[
				'post_type'      => $typenow,
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'post_status'    => [ 'publish', 'future', 'draft' ],
				'meta_query'     => [
					'relation' => 'AND',
					[
		    	    	'key'		=> 'wpar_filter_republish_status',
    			        'compare'	=> 'EXISTS'
					],
		    	    [
		    	    	'key'     => '_wpar_filter_republish_datetime',
		    	    	'compare' => 'EXISTS'
		    	    ]
				]
			]
		);

		if ( count( $get_posts ) > 0 && $enabled ) {
	    	$views['republish_pending'] = sprintf(
	    		'<a href="%1$s"%2$s>%3$s <span class="count">(%4$s)</span></a>',
	    		add_query_arg(
	    			[
	    				'post_type'      => $typenow,
	    				'republish_pending' => 1,
	    			],
	    		    admin_url( 'edit.php' ) 
	    	    ),
	    		$current,
	    		esc_html__( 'Republish Pending', 'wp-auto-republish' ),
	    		number_format_i18n( count( $get_posts ) )
	    	);
	    }
    
		return $views;
	}

	/**
	 * Filter posts in admin by Post meta value.
	 *
	 * @param \WP_Query $query The wp_query instance.
	 */
	public function filter_posts( $query )
	{
		if ( ! is_admin() ) {
			return;
		}

		if ( ! empty( $_GET['republish_pending'] ) ) {
		    $meta_query = [
				'relation' => 'AND',
		    	[
					'key'		=> 'wpar_filter_republish_status',
					'compare'	=> 'EXISTS'
				],
		    	[
		    		'key'     => '_wpar_filter_republish_datetime',
		    		'compare' => 'EXISTS'
		    	]
		    ];

			$query->set( 'orderby', 'meta_value' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_key', '_wpar_filter_republish_datetime' );
			$query->set( 'meta_query', $meta_query );
		}
	}
}