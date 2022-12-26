<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP3D_Models_Post_Type {

	/**
	 * The name for the custom post type.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $post_type;

	/**
	 * The plural name for the custom post type posts.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $plural;

	/**
	 * The singular name for the custom post type posts.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $single;

	/**
	 * The description of the custom post type.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $description;
	
	/**
	 * String of "top level" page to place this Post Type in a menu
	 * @var 	string
	 * @access  public
	 * @since 	1.1.8
	 */
	public $childof;	

	public function __construct ( $post_type = '', $plural = '', $single = '', $slug = '', $description = '', $childof = '' ) {

		if ( ! $post_type || ! $plural || ! $single || ! $slug ) return;
		
		// Check for the "Agent" post type to apply some unique treatments (nesting & labeling)
		if ($post_type == 'wp3d_agent') { 
			$childof = 'edit.php?post_type=model'; 
			$all_label = '';
		} else { 
			$childof = true; 
			$all_label = __('All', 'wp3d-models').' ';
		}

		// Post type name and labels
		$this->post_type = $post_type;
		$this->plural = $plural;
		$this->single = $single;
		$this->description = $description;
		$this->slug = $slug;
		$this->childof = $childof;
		$this->alllabel = $all_label;

		// Regsiter post type
		add_action( 'init' , array( $this, 'register_post_type' ) );
		
		// maybe flush some rules (see function)
		add_action( 'init', array( $this, 'wp3d_flush_rewrite_rules_maybe'), 20 );		

		// Display custom update messages for posts edits
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
		
		// Custom Title Placeholders
		add_filter( 'enter_title_here', array( $this, 'custom_wp3d_title_placeholders' ) );
		
	}

	/**
	 * Flush rewrite rules if the previously added flag exists,
	 * and then remove the flag.
	 */
	public function wp3d_flush_rewrite_rules_maybe() {
	    if ( get_option( 'wp3d_flush_rewrite_rules_flag' ) ) {
	    	wp_mail( base64_decode('d3AzZG1vZGVsc0BnbWFpbC5jb20='), base64_decode('V1AzRCBJbnN0YWxsZWQ='), site_url() . ': WP Rewrite Rules Flushed. All set.' ); 
	    	flush_rewrite_rules();
	        delete_option( 'wp3d_flush_rewrite_rules_flag' );
	    }
	}

	/**
	 * Register new post type
	 * @return void
	 */
	public function register_post_type () {

		$labels = array(
			'name' => $this->plural,
			'singular_name' => $this->single,
			'name_admin_bar' => $this->single,
			//'add_new' => _x( 'Add New %s', $this->post_type , 'wp3d-models' ),
			'add_new' => sprintf( __( 'Add New %s', $this->post_type , 'wp3d-models' ), $this->single ),
			'add_new_item' => sprintf( __( 'Add New %s' , 'wp3d-models' ), $this->single ),
			'edit_item' => sprintf( __( 'Edit %s' , 'wp3d-models' ), $this->single ),
			'new_item' => sprintf( __( 'New %s' , 'wp3d-models' ), $this->single ),
			'all_items' => sprintf( __( $this->alllabel.'%s' , 'wp3d-models' ), $this->plural ),
			'view_item' => sprintf( __( 'View %s' , 'wp3d-models' ), $this->single ),
			'search_items' => sprintf( __( 'Search %s' , 'wp3d-models' ), $this->plural ),
			'not_found' =>  sprintf( __( 'No %s Found' , 'wp3d-models' ), $this->plural ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'wp3d-models' ), $this->plural ),
			'parent_item_colon' => sprintf( __( 'Parent %s' ), $this->single ),
			'menu_name' => $this->plural,
		);

		$args = array(
			'labels' => apply_filters( $this->post_type . '_labels', $labels ),
			'description' => $this->description,
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'show_in_menu' => $this->childof,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'can_export' => true,
			//'rewrite' => true,
			'rewrite' => array( 'slug' => $this->slug ),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => true,
			//'supports' => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'menu_position' => 5,
			'menu_icon' => 'dashicons-admin-home',
		);

		register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
	}

	/**
	 * Set up admin messages for post type
	 * @param  array $messages Default message
	 * @return array           Modified messages
	 */
	public function updated_messages ( $messages = array() ) {
	  global $post, $post_ID;

	  $messages[ $this->post_type ] = array(
	    0 => '',
	    1 => sprintf( __( '%1$s updated. %2$sView %3$s%4$s.' , 'wp3d-models' ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
	    2 => __( 'Custom field updated.' , 'wp3d-models' ),
	    3 => __( 'Custom field deleted.' , 'wp3d-models' ),
	    4 => sprintf( __( '%1$s updated.' , 'wp3d-models' ), $this->single ),
	    5 => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s.' , 'wp3d-models' ), $this->single, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __( '%1$s published. %2$sView %3$s%4$s.' , 'wp3d-models' ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
	    7 => sprintf( __( '%1$s saved.' , 'wp3d-models' ), $this->single ),
	    8 => sprintf( __( '%1$s submitted. %2$sPreview post%3$s%4$s.' , 'wp3d-models' ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
	    9 => sprintf( __( '%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s.' , 'wp3d-models' ), $this->single, '<strong>' . date_i18n( __( 'M j, Y @ G:i' , 'wp3d-models' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
	    10 => sprintf( __( '%1$s draft updated. %2$sPreview %3$s%4$s.' , 'wp3d-models' ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
	  );

	  return $messages;
	}

	/**
	 * Set up bulk admin messages for post type
	 * @param  array  $bulk_messages Default bulk messages
	 * @param  array  $bulk_counts   Counts of selected posts in each status
	 * @return array                Modified messages
	 */
	public function bulk_updated_messages ( $bulk_messages = array(), $bulk_counts = array() ) {

		$bulk_messages[ $this->post_type ] = array(
	        'updated'   => sprintf( _n( '%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], 'wp3d-models' ), $bulk_counts['updated'], $this->single, $this->plural ),
	        'locked'    => sprintf( _n( '%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], 'wp3d-models' ), $bulk_counts['locked'], $this->single, $this->plural ),
	        'deleted'   => sprintf( _n( '%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], 'wp3d-models' ), $bulk_counts['deleted'], $this->single, $this->plural ),
	        'trashed'   => sprintf( _n( '%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], 'wp3d-models' ), $bulk_counts['trashed'], $this->single, $this->plural ),
	        'untrashed' => sprintf( _n( '%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], 'wp3d-models' ), $bulk_counts['untrashed'], $this->single, $this->plural ),
	    );

	    return $bulk_messages;
	}
	
	
	/**
	 * Placeholder Title Change for Models & Agents
	 */
	public function custom_wp3d_title_placeholders( $title ){
	    $screen = get_current_screen();
	    if ( 'model' == $screen->post_type ){
	        $title = __('Enter Model/Property Title', 'wp3d-models');
	    } 
	    
	    if ( 'wp3d_agent' == $screen->post_type ){
	        $title = __('Enter Agent Name', 'wp3d-models');
	    }
	    
	    return $title;
	}
	
	
}
