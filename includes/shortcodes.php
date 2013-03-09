<?php
/**
 * Create shortcodes for the widgets
 *
 * @package Metaphor Widgets Pack
 */




add_shortcode( 'mtphr_contact_widget', 'mtphr_contact_widget_display' );
/**
 * Display the contact widget shortcode
 *
 * @since 2.0.1
 */
function mtphr_contact_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'contact_info' => ''
	), $atts ) );
	
	// Split up the contact info
	$contact_groups = explode( ':::', $contact_info );
	
	// Loop through the info and create a formatted array
	$info_array = array();
	foreach( $contact_groups as $group ) {
	
		$title = isset( $info_assets[0] ) ? sanitize_text_field($info_assets[0]) : '';
		$description = isset( $info_assets[1] ) ? wp_kses_post(html_entity_decode($info_assets[1])) : '';
		
		// Split the site name and url
		$info_assets = explode( '***', $group );
		$info = array(
			'title' => $title,
			'description' => $description
		);
		$info_array[] = $info;
	}
	
	$instance = array(
		'title' => sanitize_text_field($title),
		'contact_info' => $info_array
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-contact-widget">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	the_widget( 'mtphr_contact_widget', $instance, $args );
	return ob_get_clean();
}




add_shortcode( 'mtphr_social_widget', 'mtphr_social_widget_display' );
/**
 * Display the social widget shortcode
 *
 * @since 2.0.0
 */
function mtphr_social_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'new_tab' => true,
		'sites' => '',
	), $atts ) );
	
	// Split up the sites
	$site_groups = explode( ':::', $sites );
	
	// Loop through the sites and create a formatted array
	$sites_array = array();
	foreach( $site_groups as $group ) {
		
		// Split the site name and url
		$group_assets = explode( '***', $group );
		$site = array(
			'site' => sanitize_text_field($group_assets[0]),
			'link' => esc_url($group_assets[1])
		);
		$sites_array[] = $site;
	}
	
	$instance = array(
		'title' => sanitize_text_field( $title ),
		'new_tab' => intval($new_tab),
		'sites' => $sites_array
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-social-widget">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	ob_start();
	the_widget( 'mtphr_social_widget', $instance, $args );
	return ob_get_clean();
}




add_shortcode( 'mtphr_twitter_widget', 'mtphr_twitter_widget_display' );
/**
 * Display the contact widget shortcode
 *
 * @since 2.0.0
 */
function mtphr_twitter_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'twitter_name' => '',
		'limit' => 3,
		'image' => 'off',
		'avatar' => 'off'
	), $atts ) );
	
	$instance = array(
		'title' => sanitize_text_field($title),
		'twitter_name' => sanitize_text_field($twitter_name),
		'widget_limit' => intval($limit)
	);
	if( $image == 'on' ) {
		$instance['widget_image'] = true;
	}
	if( $avatar == 'on' ) {
		$instance['widget_avatar'] = true;
	}
	$args = array(
		'before_widget' => '<aside class="widget mtphr-twitter-widget">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	the_widget( 'mtphr_twitter_widget', $instance, $args );
	return ob_get_clean();
}




add_shortcode( 'mtphr_posts_widget', 'mtphr_posts_widget_display' );
/**
 * Display the posts widget shortcode
 *
 * @since 2.0.0
 */
function mtphr_posts_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'limit' => 3,
		'excerpt_length' => 72,
		'author' => '',
		'category' => ''
	), $atts ) );
	
	$instance = array(
		'title' => sanitize_text_field($title),
		'widget_limit' => intval($limit),
		'excerpt_length' => intval($excerpt_length),
		'author' => sanitize_text_field($author),
		'category' => sanitize_text_field($category)
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-posts-widget">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	the_widget( 'mtphr_posts_widget', $instance, $args );
	return ob_get_clean();
}




add_shortcode( 'mtphr_comments_widget', 'mtphr_comments_widget_display' );
/**
 * Display the posts widget shortcode
 *
 * @since 2.0.0
 */
function mtphr_comments_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'limit' => 3,
		'excerpt_length' => 72,
		'avatar' => false
	), $atts ) );
	
	$instance = array(
		'title' => sanitize_text_field($title),
		'widget_limit' => intval($limit),
		'excerpt_length' => intval($excerpt_length)
	);
	if( $avatar == 'on' ) {
		$instance['widget_avatar'] = true;
	}
	$args = array(
		'before_widget' => '<aside class="widget mtphr-comments-widget">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	the_widget( 'mtphr_comments_widget', $instance, $args );
	return ob_get_clean();
}




add_shortcode( 'mtphr_post_navigation', 'mtphr_post_navigation_display' );
/**
 * Display the post navigation shortcode
 *
 * @since 2.0.0
 */
function mtphr_post_navigation_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'home' => '{type}'.__(' home', 'mtphr-widgets'),
		'home_link' => '',
		'previous' => __('Previous', 'mtphr-widgets'),
		'next' => __('Next', 'mtphr-widgets')
	), $atts ) );
	
	$instance = array(
		'title' => sanitize_text_field($title),
		'home' => sanitize_text_field($home),
		'home_link' => esc_url($home_link),
		'previous' => sanitize_text_field($previous),
		'next' => sanitize_text_field($next)
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-post-navigation">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	the_widget( 'mtphr_post_navigation', $instance, $args );
	return ob_get_clean();
}




