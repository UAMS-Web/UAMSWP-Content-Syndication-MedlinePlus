<?php

namespace UAMS\Medline_Syndicate;

add_action( 'plugins_loaded', 'UAMS\Medline_Syndicate\bootstrap' );

/**
 * Loads the UAMSWP Content Syndicate base.
 *
 * @since 1.0.0
 */
function bootstrap() {
	include_once dirname( __FILE__ ) . '/class-uams-syndication-cpt-medline.php';
	add_action( 'init', 'UAMS\Medline_Syndicate\activate_shortcodes' );
	add_action( 'save_post_post', 'UAMS\Medline_Syndicate\clear_local_content_cache' );
	add_action( 'save_post_page', 'UAMS\Medline_Syndicate\clear_local_content_cache' );
}

/**
 * Activates the shortcodes built in with UAMSWP Content Syndicate.
 *
 * @since 1.0.0
 */
function activate_shortcodes() {	
	include_once dirname( __FILE__ ) . '/class-uams-syndication-shortcode-medline.php';
}

/**
 * Clear the last changed cache for local results whenever
 * a post is saved.
 *
 * @since 1.4.0
 */
function clear_local_content_cache() {
	wp_cache_set( 'last_changed', microtime(), 'uamswp-medline' );
}