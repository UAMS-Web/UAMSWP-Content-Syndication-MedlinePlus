<?php
/*
|--------------------------------------------------------------------------
| DEFINE THE CUSTOM POST TYPE
|--------------------------------------------------------------------------
*/
 
/**
 * Setup testimonial Custom Post Type
 *
 * @since 1.0
*/
 
function uamswp_medline_setup_post_types() {
 
    // Custom Post Type Labels
    $labels = array(
        'name' => esc_html__( 'Health Library', 'uamswp_medline' ),
        'singular_name' => esc_html__( 'Health Topic', 'uamswp_medline' ),
        'add_new' => esc_html__( 'Add New', 'uamswp_medline' ),
        'add_new_item' => esc_html__( 'Add New Health Topic', 'uamswp_medline' ),
        'edit_item' => esc_html__( 'Edit Health Topic', 'uamswp_medline' ),
        'new_item' => esc_html__( 'New Health Topic', 'uamswp_medline' ),
        'view_item' => esc_html__( 'View Health Topic', 'uamswp_medline' ),
        'search_items' => esc_html__( 'Search Health Topics', 'uamswp_medline' ),
        'not_found' => esc_html__( 'No health topic found', 'uamswp_medline' ),
        'not_found_in_trash' => esc_html__( 'No health topic found in trash', 'uamswp_medline' ),
        'parent_item_colon' => ''
    );
 
    // Supports
    $supports = array( 'title', 'editor' );
 
    // Custom Post Type Supports
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array( 'slug' => 'health-library', 'with_front' => true ),
        'capability_type' => 'page',
        'hierarchical' => true,
        'menu_position' => 25,
        'supports' => $supports,
        'has_archive' => true,
        'menu_icon' => 'dashicons-book-alt', //plugin_dir_url( __FILE__ ) . '/includes/images/testimonials_icon.png', // you can set your own icon here
    );
 
    // Finally register the "health-library" custom post type
    register_post_type( 'health-library' , $args );
}
 
add_action( 'init', 'uamswp_medline_setup_post_types' );

/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/
 
add_filter( 'template_include', 'uamswp_medline_template_chooser');
 
/*
|--------------------------------------------------------------------------
| PLUGIN FUNCTIONS
|--------------------------------------------------------------------------
*/
 
/**
 * Returns template file
 *
 * @since 1.0
 */
 
function uamswp_medline_template_chooser( $template ) {
 
    // Post ID
    $post_id = get_the_ID();
 
    // For all other CPT
    if ( get_post_type( $post_id ) != 'health-library' ) {
        return $template;
    }
 
    // Else use custom template
    if ( is_single() ) {
        return uamswp_medline_get_template_hierarchy( 'single' );
    }

    if ( is_archive() ) {
        return uamswp_medline_get_template_hierarchy( 'archive' );
    }
 
}

/**
 * Get the custom template if is set
 *
 * @since 1.0
 */
 
function uamswp_medline_get_template_hierarchy( $template ) {
 
    // Get the template slug
    $template_slug = rtrim( $template, '.php' );
    $template = $template_slug . '.php';
 
    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'plugin_template/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = dirname( __FILE__ ) . '/templates/' . $template;
    }
 
    return apply_filters( 'uamswp_repl_template_' . $template, $file );
}
 
/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/
 
add_filter( 'template_include', 'uamswp_medline_template_chooser' );