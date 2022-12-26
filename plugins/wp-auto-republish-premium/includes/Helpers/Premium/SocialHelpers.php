<?php
/**
 * Social Helper functions.
 *
 * @since      1.2.2
 * @package    WP Auto Republish
 * @subpackage Wpar\Helpers
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Helpers\Premium;

defined( 'ABSPATH' ) || exit;

/**
 * Meta & Option class.
 */
trait SocialHelpers
{
	/**
	 * Save the Social Profile Data to db.
	 *
	 * @param  array   $args  Social Profile Data.
	 * @param  string  $type  Social account Type.
	 */
	protected function update_db( $args, $type = '' )
	{
		$data = unserialize( get_option( 'wpar_' . $type . '_accounts_db' ) );
		if( empty( $data ) ) $data = [];

		$data[$args['id']] = $args;

		update_option( 'wpar_' . $type . '_accounts_db', maybe_serialize( $data ) );
	}

	/**
	 * Expire social account if access token is expired.
	 *
	 * @param string  $account_id  Social Profile ID.
	 * @param string  $type        Social account Type.
	 */
	protected function expire_token( $account_id, $type = '' )
	{
        $data = unserialize( get_option( 'wpar_' . $type . '_accounts_db' ) );
		if ( isset( $data[$account_id] ) ) {
			$data[$account_id]['status'] = false;
			$data[$account_id]['valid'] = false;

			update_option( 'wpar_' . $type . '_accounts_db', maybe_serialize( $data ) );
		}
	}

	/**
	 * Save Twitter share logs as post meta.
	 * 
	 * @param int     $post_id WP Post ID.
	 * @param array   $args    Share Log data
	 * @param string  $type    Social account Type.
	 */
	protected function set_post_metadata( $post_id, $args, $type )
	{
		$share_logs = unserialize( $this->get_meta( $post_id, 'wpar_' . $type . '_share_logs' ) );
		if ( empty( $share_logs ) ) $share_logs = [];
	
		$share_logs[] = $args;

		$this->update_meta( $post_id, 'wpar_' . $type . '_share_logs', maybe_serialize( $share_logs ) );
	}

	/**
	 * Generate Hashtags ffor social share.
	 * 
	 * @param array $taxonomies  Taxonomies Array
	 * @param int   $post_id     WP Post ID.
	 * @return array
	 */
	protected function get_hashtags( $taxonomies, $post_id )
	{
		$post = get_post( $post_id );
		$terms = [];
        if ( ! empty( $taxonomies ) ) {
	    	foreach( $taxonomies as $taxonomy ) {
	    		$get_item = explode( '|', $taxonomy );
	    		$type = $get_item[0];
	    		$taxonomy_name = $get_item[1];
	    		$term_id = $get_item[2];
	    		if ( $post->post_type === $type && has_term( $term_id, $taxonomy_name, $post->ID ) ) {
					$terms[] = str_replace( [ ' ', '-' ], '_', get_term( $term_id, $taxonomy_name )->name );
	    		}
	    	}
		}
		
		return $terms;
	}

	/**
	 * Generate Social Share Template.
	 *
	 * @param string $template_structure  Template Structure from DB. 
	 * @param int    $post_id             WP Post ID.
	 * @param array  $hashtags            Post Hashtags
	 * @param string $source              Post Content Source.
	 * @param int    $limit               Content Limit.
	 * 
	 * @return array $template_structure
	 */
    protected function social_template( $template_structure, $post_id, $hashtags, $source, $limit )
    {
		$post = get_post( $post_id );
		$post_title = $post->post_title;
		$post_link = esc_url( get_permalink( $post_id ) );
		if ( $source == 'post_excerpt' && has_excerpt( $post->ID ) ) {
            $desc = wp_strip_all_tags( strip_shortcodes( $post->post_excerpt ) );
        } else {
            $desc = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
        }

		$template = str_replace( [ '%post_title%', '%post_url%', '%post_content%', '%hashtags%' ], '', $template_structure );
		$post_content_limit = intval( $limit ) - strlen( $template );

        if ( ! empty( $post_title ) ) {
            $post_content_limit = intval( $post_content_limit ) - strlen( $post_title );
            $template_structure = str_replace( '%post_title%', $post_title, $template_structure );
		}
		
        if ( ! empty( $post_link ) ) {
            $post_content_limit = intval( $post_content_limit ) - strlen( $post_link );
            $template_structure = str_replace( '%post_url%', $post_link, $template_structure );
		}
		
        if ( ! empty( $hashtags ) && is_array( $hashtags ) ) {
			shuffle( $hashtags );
			$hashtag = '#' . implode( ' #', $hashtags );
            $post_content_limit = intval( $post_content_limit ) - strlen( $hashtag );
            $template_structure = str_replace( '%hashtags%', $hashtag, $template_structure );
        } else {
            $template_structure = str_replace( '%hashtags%', '', $template_structure );
        }

        if ( ! empty( $desc ) ) {
            $post_content = substr( $desc, 0, $post_content_limit );
            $template_structure = str_replace( '%post_content%', $post_content , $template_structure );
        } else {
            $template_structure = str_replace( '%post_content%', '', $template_structure );
		}
		
		return $template_structure;
    }
}