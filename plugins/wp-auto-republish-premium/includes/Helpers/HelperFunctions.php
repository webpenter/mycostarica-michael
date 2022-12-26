<?php
/**
 * Helper functions.
 *
 * @since      1.1.3
 * @package    WP Auto Republish
 * @subpackage Wpar\Helpers
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Helpers;

use Wpar\Helpers\SettingsData;

defined( 'ABSPATH' ) || exit;

/**
 * Meta & Option class.
 */
trait HelperFunctions
{
	use SettingsData;
	
	/**
	 * Get all registered public post types.
	 *
	 * @param bool $public Public type True or False.
	 * @return array
	 */
	protected function get_post_types()
	{
        $post_types = get_post_types( [ 'public' => true, '_builtin' => true ], 'objects' );
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$post_types = get_post_types( [ 'public' => true ], 'objects' );
		}
		$data = [];
		foreach( $post_types as $post_type ) {
			if ( ! is_object( $post_type ) )
			    continue;															
			
			if ( isset( $post_type->labels ) ) {
				$label = $post_type->labels->name ? $post_type->labels->name : $post_type->name;
			} else {
				$label = $post_type->name;
			}
			
			if ( $label == 'Media' || $label == 'media' || $post_type->name == 'elementor_library' )
				continue; // skip media
				
			$data[$post_type->name] = $label;
		}

		return $data;
	}

	/**
	 * Get all registered taxonomies.
	 *
	 * @param bool  $public  Builtin post types True or False.
	 * @param bool  $hide    Hide empty taxonomies True or False.
	 * @return array
	 */
    protected function get_all_taxonomies( $args, $hide = false, $builtin = true )
	{
		$post_types = get_post_types( $args, 'objects' );
		$post_types = is_array( $post_types ) ? $post_types : [];
		$data = $attribute_taxonomy_array = [];

		if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			foreach( $attribute_taxonomies as $attribute_taxonomy ) {
				$attribute_taxonomy_array[] = "pa_".$attribute_taxonomy->attribute_name;
			}
		}
		$wc_taxonomy_array = [ 'product_shipping_class', 'product_visibility', 'product_type', 'post_format' ];
		$taxonomy_array = array_merge( $attribute_taxonomy_array, $wc_taxonomy_array );
	
		// If $post_types value is not empty
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				if ( ! is_object( $post_type ) )
					continue;
	
				if ( isset( $post_type->labels ) ) {
					$label = $post_type->labels->name ? $post_type->labels->name : $post_type->name;
				} else {
					$label = $post_type->name;
				}
	
				$post_type = $post_type->name;
				$categories_array = [];
	
				if ( $label == 'Media' || $label == 'media' || $post_type == 'elementor_library' )
					continue; // skip media
	
				$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	
				// Loop on all taxonomies
				foreach( $taxonomies as $taxonomy ) {
					if ( is_object( $taxonomy ) && ! in_array( $taxonomy->name, $taxonomy_array ) ) {
						if ( $builtin && ( $post_type != 'post' || ! in_array( $taxonomy->name, [ 'category', 'post_tag' ] ) ) ) {
							continue;
						}
						$categories = get_terms( $taxonomy->name, [ 'hide_empty' => $hide ] ); // Get categories
	                    foreach( $categories as $category ) {
							if ( is_object_in_taxonomy( $post_type, $taxonomy->name ) ) {
	                            $categories_array[$post_type . '|' . $taxonomy->name . '|' . $category->term_id] = ucwords( $taxonomy->label ) . ': ' . $category->name;
						    }
					    }
					}
				}

				if ( ! empty( $categories_array ) ) {
					$data[$post_type]['label'] = $label;
					$data[$post_type]['categories'] = $categories_array;
					unset( $categories_array );
				}
			}
		}

		return $data;
	}

	/**
	 * Check plugin settings if enabled
	 * 
	 * @return bool
	 */
	protected function is_enabled( $name, $prefix = false )
	{
		if ( $prefix ) {
			$name = 'wpar_' . $name;
		}

		if ( $this->get_data( $name ) == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * Check current user roles.
	 * 
	 * @return bool
	 */
	protected function user_has_cap__premium_only()
	{
		$user = wp_get_current_user();
		$role = $user->roles;

        if ( is_array( $role ) ) {
            return array_intersect( $role, $this->get_data( 'wpar_single_roles', [ 'administrator' ] ) ) ? true : false;
        }

        return in_array( $role, $this->get_data( 'wpar_single_roles', [ 'administrator' ] ) );
    }

	/**
	 * Insert the plugins settings in proper place.
	 *
	 * @param  array   $array     Default setting fields.
	 * @param  integer $position  Insertion position.
	 * @param  array   $insert    Field.
	 * @return array
	 */
	protected function insert_settings__premium_only( $array, $position, $insert )
    {
		$array = array_merge( array_slice( $array, 0, $position ), $insert, array_slice( $array, $position ) );
		
		return $array;
	}

	/**
	 *  Check if any social plugin settings enabled
	 * 
	 * @return bool
	 */
	protected function is_social_enabled__premium_only()
    {
		if ( $this->is_enabled( 'facebook_enable' ) || $this->is_enabled( 'twitter_enable' ) || $this->is_enabled( 'linkedin_enable' ) ) {
		    return true;
	    }

		return false;
	}

	/**
	 * Create the recurring action event.
	 *
	 * @param  integer $timestamp            Timestamp.
	 * @param  integer $interval_in_seconds  Interval in Seconds.
	 * @param  string  $hook                 Action Hook.
	 * @param  array   $args                 Parameters.
	 * @param  string  $group                Group Name.
	 * @return string
	 */
	protected function set_recurring_action( $timestamp, $interval_in_seconds, $hook, $args = [], $group = 'wp-auto-republish' )
    {
		$action_id = \as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );

		return $action_id;
	}

	/**
	 * Create the single action event.
	 *
	 * @param  integer $timestamp  Timestamp.
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 * @return string
	 */
	protected function set_single_action( $timestamp, $hook, $args = [], $group = 'wp-auto-republish' )
    {
		$action_id = \as_schedule_single_action( $timestamp, $hook, $args, $group );

		return $action_id;
	}

	/**
	 * Unschedule all action events.
	 *
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 */
	protected function unschedule_all_actions( $hook, $args = [], $group = 'wp-auto-republish' )
    {
		\as_unschedule_all_actions( $hook, $args, $group );
	}

	/**
	 * Unschedule last action event.
	 *
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 */
	protected function unschedule_last_action( $hook, $args = [], $group = 'wp-auto-republish' )
    {
		\as_unschedule_action( $hook, $args, $group );
	}

	/**
	 * Check if next action is exists.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function get_next_action( $hook, $args = [], $group = 'wp-auto-republish' )
    {
		return \as_next_scheduled_action( $hook, $args, $group );
	}

	/**
	 * Check if next action is exists.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function get_next_action_by_data( $hook, $timestamp, $args, $group = 'wp-auto-republish' )
    {
		return \as_get_scheduled_actions( [
			'hook' => $hook,
			'args' => $args,
			'date' => $timestamp,
			'date_compare' => '=',
			'group' => $group,
			'status' => 'pending',
			'per_page' => 1
		] );
	}
}