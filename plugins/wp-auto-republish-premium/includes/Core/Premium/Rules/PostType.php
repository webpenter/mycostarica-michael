<?php
/**
 * Clear Single Post Cache.
 *
 * @since      1.2.5
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium\Rules
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Rules;

use Wpar\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Single post cache class.
 */
class PostType
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'init', 'post_type', 0 );
	}

	/**
	 * Purge single post cache.
	 * 
	 * @param int    $post_id  Post ID
	 * @param object $post     WP Post Object
	 */
	public function post_type()
	{
		// Register Custom Post Type
	    $labels = [
	    	'name'                  => _x( 'Republish Rules', 'Post Type General Name', 'wp-auto-republish' ),
	    	'singular_name'         => _x( 'Republish Rule', 'Post Type Singular Name', 'wp-auto-republish' ),
	    	'menu_name'             => __( 'Republish Rules', 'wp-auto-republish' ),
	    	'name_admin_bar'        => __( 'Republish Rule', 'wp-auto-republish' ),
	    	'archives'              => __( 'Rule Archives', 'wp-auto-republish' ),
	    	'attributes'            => __( 'Rule Attributes', 'wp-auto-republish' ),
	    	'parent_item_colon'     => __( 'Parent Rule:', 'wp-auto-republish' ),
	    	'all_items'             => __( 'Republish Rules', 'wp-auto-republish' ),
	    	'add_new_item'          => __( 'Add New Rule', 'wp-auto-republish' ),
	    	'add_new'               => __( 'Add New', 'wp-auto-republish' ),
	    	'new_item'              => __( 'New Rule', 'wp-auto-republish' ),
	    	'edit_item'             => __( 'Edit Rule', 'wp-auto-republish' ),
	    	'update_item'           => __( 'Update Rule', 'wp-auto-republish' ),
	    	'view_item'             => __( 'View Rule', 'wp-auto-republish' ),
	    	'view_items'            => __( 'View Rules', 'wp-auto-republish' ),
	    	'search_items'          => __( 'Search Rule', 'wp-auto-republish' ),
	    	'not_found'             => __( 'Not found', 'wp-auto-republish' ),
	    	'not_found_in_trash'    => __( 'Not found in Trash', 'wp-auto-republish' ),
	    	'featured_image'        => __( 'Featured Image', 'wp-auto-republish' ),
	    	'set_featured_image'    => __( 'Set featured image', 'wp-auto-republish' ),
	    	'remove_featured_image' => __( 'Remove featured image', 'wp-auto-republish' ),
	    	'use_featured_image'    => __( 'Use as featured image', 'wp-auto-republish' ),
	    	'insert_into_item'      => __( 'Insert into rule', 'wp-auto-republish' ),
	    	'uploaded_to_this_item' => __( 'Uploaded to this rule', 'wp-auto-republish' ),
	    	'items_list'            => __( 'Rules list', 'wp-auto-republish' ),
	    	'items_list_navigation' => __( 'Rules list navigation', 'wp-auto-republish' ),
	    	'filter_items_list'     => __( 'Filter rules list', 'wp-auto-republish' ),
		];

	    $args = [
	    	'label'                 => __( 'Republish Rule', 'wp-auto-republish' ),
	    	'description'           => __( 'This Post Type create custom Republish Rules.', 'wp-auto-republish' ),
	    	'labels'                => $labels,
	    	'supports'              => [ 'title' ],
	    	'hierarchical'          => false,
	    	'public'                => false,
	    	'show_ui'               => true,
	    	'show_in_menu'          => 'wp-auto-republish',
	    	'show_in_admin_bar'     => true,
	    	'show_in_nav_menus'     => false,
	    	'can_export'            => true,
	    	'has_archive'           => false,
	    	'exclude_from_search'   => true,
	    	'publicly_queryable'    => false
		];

	    register_post_type( 'republish_rule', $args );
	}
}