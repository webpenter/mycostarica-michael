<?php
/**
 * The file that defines the class in charge of
 * preparing the queue post format.
 *
 * A class that is used to format the content with respect to post format options.
 *
 * @since      2.2.0
 *
 * @package    Rop Pro
 */
class Rop_Pro_Post_Format_Helper {

	/**
	 * Utility method to replace magic tags with their values.
	 *
	 * @since   2.2.0
	 * @access  public
	 *
	 * @param   string $content The content to use.
	 * @param   int    $post_id The post ID.
	 *
	 * @return string
	 */
	public function rop_replace_magic_tags( $content, $post_id ) {

		$post_object = get_post( $post_id );

		if ( ! is_object( $post_object ) ) {
			// This would mean that the post most likely doesn't exist
			return $content;
		}

		$post_title    = $post_object->post_title;
		$post_url      = get_permalink( $post_id );
		$comment_count = $post_object->comment_count;

		// Let the user control when to show this output.
		$show_after_n_comments = apply_filters( 'rop_show_after_n_comments', 0 );

		if ( $comment_count > $show_after_n_comments ) {
			  $comment_count = $comment_count . Rop_Pro_I18n::get_labels( 'magic_tags.comments' );
		} else {
			$comment_count = '';
		}

		$post_author   = get_the_author_meta( 'display_name', $post_object->post_author );
		$publish_date  = date( 'F jS, Y', strtotime( $post_object->post_date ) );
		$login_url     = wp_login_url();

		// E-commerce magic tags
		$regular_price = '';
		$sale_price = '';
		$total_sales = '';
		$stock_status = '';
		$stock_quantity = '';

		if ( function_exists( 'bigcommerce' ) && get_post_type( $post_id ) === 'bigcommerce_product' ) {

			$bc_product = new \BigCommerce\Post_Types\Product\Product( $post_id );
			$all_product_data = $bc_product->get_source_data();

			$regular_price = $all_product_data->price;
			$sale_price = $all_product_data->sale_price;

			// Let the user control when to show this output.
			$show_after_n_sales = apply_filters( 'rop_show_after_n_sales', 0 );
			$total_sales = $all_product_data->total_sold;

			if ( $total_sales > $show_after_n_sales ) {
				$total_sales = $total_sales . Rop_Pro_I18n::get_labels( 'magic_tags.sales' );
			} else {
				$total_sales = '';
			}

			if ( $all_product_data->inventory_tracking !== 'none' ) {
				$stock_quantity = ( $all_product_data->inventory_level > 0 ) ? $all_product_data->inventory_level : '';
				$stock_status = ( $stock_quantity > 0 ) ? 'In Stock' : '';
			}
		}

		if ( class_exists( 'woocommerce' ) && get_post_type( $post_id ) === 'product' ) {

			$product = wc_get_product( $post_id );

			$regular_price = html_entity_decode( strip_tags( wc_price( $product->get_regular_price() ) ) );

			$sale_price = ! empty( $product->get_sale_price() ) ? html_entity_decode( strip_tags( wc_price( $product->get_sale_price() ) ) ) : '';

			if ( $product->is_type( 'variable' ) ) {
				$min_price = $product->get_variation_regular_price( 'min', true );
				$min_price = html_entity_decode( strip_tags( wc_price( $min_price ) ) );
				$max_price = $product->get_variation_regular_price( 'max', true );
				$max_price = html_entity_decode( strip_tags( wc_price( $max_price ) ) );

				$min_sale_price = $product->get_variation_sale_price( 'min', true );
				$min_sale_price = html_entity_decode( strip_tags( wc_price( $min_sale_price ) ) );
				$max_sale_price = $product->get_variation_sale_price( 'max', true );
				$max_sale_price = html_entity_decode( strip_tags( wc_price( $max_sale_price ) ) );

				$sale_price = $min_sale_price . ' - ' . $max_price;
				$regular_price = $min_price . ' - ' . $max_price;
			}

			$total_sales = get_post_meta( $post_id, 'total_sales', true );

			// Let the user control when to show this output.
			$show_after_n_sales = apply_filters( 'rop_show_after_n_sales', 0 );

			if ( $total_sales > $show_after_n_sales ) {
				$total_sales = $total_sales . Rop_Pro_I18n::get_labels( 'magic_tags.sales' );
			} else {
				$total_sales = '';
			}

			$login_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

			if ( $product->get_manage_stock() ) {
				$stock_status = ( $product->get_stock_status() === 'instock' ) ? 'In Stock' : '';
				$stock_quantity = ( $product->get_stock_quantity() > 0 ) ? $product->get_stock_quantity() : '';
			}
		}

		$replacements = array(
			'{post_id}'        => $post_id,
			'{title}'          => $post_title,
			'{url}'            => $post_url,
			'{comment_count}'  => $comment_count,
			'{author}'         => $post_author,
			'{date}'           => $publish_date,
			'{week_day}'       => date_i18n( 'l' ),
			'{the_date}'       => date_i18n( get_option( 'date_format' ) ),
			'{login}'          => $login_url,
			'{price}'          => ( isset( $regular_price ) ) ? $regular_price : '',
			'{sale_price}'     => ( isset( $sale_price ) ) ? $sale_price : '',
			'{sales}'          => ( isset( $total_sales ) ) ? $total_sales : '',
			'{stock_status}'   => ( isset( $stock_status ) ) ? $stock_status : '',
			'{stock_quantity}' => ( isset( $stock_quantity ) ) ? $stock_quantity : '',

		);

		$replaced = str_replace( array_keys( $replacements ), array_values( $replacements ), $content );

		return $replaced;
	}


	  /**
	   * Utility method to get the selected custom post types from general settings.
	   *
	   * @since   2.2.0
	   * @access  private
	   *
	   * @return array
	   */
	private static function get_selected_custom_post_types() {

		$setting = new Rop_Settings_Model;

		$selected_post_types = wp_list_pluck( $setting->get_selected_post_types(), 'value' );

		if ( empty( $selected_post_types ) ) {
			return;
		}

		// Lets drop known post types.
		$known_post_types = array( 'post', 'page', 'attachment', 'product' );
		$selected_custom_post_types = array_diff( $selected_post_types, $known_post_types );

		return $selected_custom_post_types;
	}

	  /**
	   * Utility method to get custom post type taxonomies.
	   *
	   * @since   2.2.0
	   * @access  private
	   *
	   * @param   string $type The type of taxonomy we want.
	   *
	   * @return array
	   */
	private static function get_custom_taxonomies( $type ) {

		$custom_post_types = self::get_selected_custom_post_types();

		if ( empty( $custom_post_types ) ) {
			return;
		}

		foreach ( $custom_post_types as $custom_post_type ) {

			$taxonomy_names = get_object_taxonomies( $custom_post_type, 'objects' );

			foreach ( $taxonomy_names as $key => $object ) {

				if ( $object->hierarchical == true && $type == 'hierarchical' ) {
					$custom_taxonomies[] = $key;
				} elseif ( $object->hierarchical == false && $type == 'non-hierarchical' ) {
					$custom_taxonomies[] = $key;
				}
			}
		}

		return $custom_taxonomies;
	}

	  /**
	   * Utility method to get custom post type taxonomies for taxonomy hashtags.
	   *
	   * @since   2.2.0
	   * @access  public
	   *
	   * @param   string $type The type of taxonomy we want.
	   * @param   int    $post_id The post id.
	   *
	   * @return array
	   */
	public function get_cpt_taxonomies( $type, $post_id ) {

		$current_post_type = get_post_type( $post_id );
		$custom_post_types = self::get_selected_custom_post_types();

		if ( empty( $custom_post_types ) ) {
			return;
		}

		// If this is not a custom post type.
		if ( ! in_array( $current_post_type, $custom_post_types ) ) {
			return;
		}

		$custom_taxonomies = self::get_custom_taxonomies( $type );

		if ( empty( $custom_taxonomies ) ) {
			return;
		}

		foreach ( $custom_post_types as $custom_post_type ) {

			$taxonomy_names = get_object_taxonomies( $custom_post_type );
			// If the current post type in the loop is the type of post we're currently working with in the queued post entry
			if ( $custom_post_type == $current_post_type ) {
				// Loop through all available custom taxonomies for our custom post types on the website
				foreach ( $taxonomy_names as $taxonomy_name ) {
					// Then loop through each custom taxonomy
					foreach ( $custom_taxonomies as $custom_taxonomy ) {
						// When there is a match on loop iteration...
						if ( $taxonomy_name == $custom_taxonomy ) {
							// Check if the current post in the queue entry has that custom taxonomy attached to it(due to empty parameter)
							if ( has_term( '', $taxonomy_name, $post_id ) ) {
								// If so, add it to the array
								$taxonomies[] = $taxonomy_name;
							}
						}
					}
				}
			}
		}

		return $taxonomies;
	}

	  /**
	   * Utility method to generate the category hashtags.
	   *
	   * @since   2.2.0
	   * @access  public
	   *
	   * @param   int $post_id The post id.
	   *
	   * @return array
	   */
	public function pro_get_categories_hashtags( $post_id ) {

		$taxonomies[] = 'category';

		if ( class_exists( 'woocommerce' ) && get_post_type( $post_id ) == 'product' ) {
			$taxonomies[] = 'product_cat';
		}

		// Get the hierarchical taxonomies if this is a custom post type
		$cpt_taxonomies = $this->get_cpt_taxonomies( 'hierarchical', $post_id );

		if ( ! empty( $cpt_taxonomies ) ) {
			foreach ( $cpt_taxonomies as $cpt_taxonomy ) {
				$taxonomies[] = $cpt_taxonomy;
			}
		}

		foreach ( $taxonomies as $taxonomy ) {
			$categories = get_the_terms( $post_id, $taxonomy );
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$categories = wp_list_pluck( $categories, 'name' );
			}
		}

		return $categories;
	}

	  /**
	   * Utility method to generate the tags hashtags.
	   *
	   * @since   2.2.0
	   * @access  public
	   *
	   * @param   int $post_id The post id.
	   *
	   * @return array
	   */
	public function pro_get_tags_hashtags( $post_id ) {

		$taxonomies[] = 'post_tag';

		if ( class_exists( 'woocommerce' ) && get_post_type( $post_id ) == 'product' ) {
			$taxonomies[] = 'product_tag';
		}

		// Get the non-hierarchical taxonomies if this is a custom post type
		$cpt_taxonomies = $this->get_cpt_taxonomies( 'non-hierarchical', $post_id );

		if ( ! empty( $cpt_taxonomies ) ) {
			foreach ( $cpt_taxonomies as $cpt_taxonomy ) {
				$taxonomies[] = $cpt_taxonomy;
			}
		}

		foreach ( $taxonomies as $taxonomy ) {
			$tags = get_the_terms( $post_id, $taxonomy );
			if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
				$tags = wp_list_pluck( $tags, 'name' );
			}
		}

		return $tags;
	}

}
