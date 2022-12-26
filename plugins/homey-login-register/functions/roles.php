<?php
/**
 * Role Management
 *
 * User: waqasriaz
 */

add_role(
    'homey_renter',
    esc_html__( 'Renter', 'homey-login-register' ),
    array(
        'read'         => true,  // true allows this capability
        'edit_posts'   => false,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

add_role(
    'homey_host',
    esc_html__( 'Host', 'homey-login-register' ),
    array(
        'read'                      => true,  // true allows this capability
        'edit_posts'                => true,
        'delete_posts'              => true, // Use false to explicitly deny
        'read_listing'             => true,
        'publish_posts'             => true,
        'edit_listing'             => true,
        'create_listings'         => true,
        'edit_listings'           => true,
        'delete_listings'       => true,
        'edit_published_listings'    => true,
        'publish_listings'        => true,
        'delete_published_listings'   => true,
        'delete_private_listings' => true,
        'read_testimonial'             => true,
        'edit_testimonial'             => true,
        'create_testimonials'         => true,
        'edit_testimonials'           => true,
        'edit_published_testimonials'    => true,
        'publish_testimonials'        => true,
        'delete_published_testimonials'   => true
    )
);

add_role(
    'homey_sales',
    esc_html__( 'Sales', 'homey-login-register' ),
    array(
        'read'                      => true,  // true allows this capability
        'edit_posts'                => true,
        'delete_posts'              => true, // Use false to explicitly deny
        'read_listing'             => true,
        'publish_posts'             => true,
        'edit_listing'             => true,
        'create_listings'         => true,
        'edit_listings'           => true,
        'delete_listings'       => true,
        'edit_published_listings'    => true,
        'publish_listings'        => true,
        'delete_published_listings'   => true,
        'delete_private_listings' => true,
        'read_testimonial'             => true,
        'edit_testimonial'             => true,
        'create_testimonials'         => true,
        'edit_testimonials'           => true,
        'edit_published_testimonials'    => true,
        'publish_testimonials'        => true,
        'delete_published_testimonials'   => true
    )
);