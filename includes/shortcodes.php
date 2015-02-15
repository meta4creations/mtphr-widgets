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
 * @since 2.1.18
 */
function mtphr_contact_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => ''
	), $atts ) );

	// Split up the contact info
	$contact_groups = explode( ':::', $content );

	// Loop through the info and create a formatted array
	$info_array = array();
	foreach( $contact_groups as $group ) {

		// Split the site name and url
		$info_assets = explode( '***', $group );

		$info_title = isset( $info_assets[0] ) ? sanitize_text_field($info_assets[0]) : '';
		$info_description = isset( $info_assets[1] ) ? wp_kses_post(html_entity_decode($info_assets[1])) : '';

		$info = array(
			'title' => $info_title,
			'description' => $info_description
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
	$widget = new mtphr_contact_widget(); 
	$widget->widget( $args, $instance ); 
	return ob_get_clean();
}




add_shortcode( 'mtphr_collapse_widget', 'mtphr_collapse_widget_display' );
/**
 * Display the contact widget shortcode
 *
 * @since 2.1.18
 */
function mtphr_collapse_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => ''
	), $atts ) );

	// Split up the contact info
	$collapse_groups = explode( ':::', $content );

	// Loop through the info and create a formatted array
	$info_array = array();
	foreach( $collapse_groups as $group ) {

		// Split the site name and url
		$info_assets = explode( '***', $group );

		$info_header = isset( $info_assets[0] ) ? sanitize_text_field($info_assets[0]) : '';
		$info_description = isset( $info_assets[1] ) ? wp_kses_post(html_entity_decode($info_assets[1])) : '';

		$info = array(
			'title' => $info_header,
			'description' => $info_description
		);
		$info_array[] = $info;
	}

	$instance = array(
		'title' => sanitize_text_field($title),
		'collapse_info' => $info_array
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-collapse-widget">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	$widget = new mtphr_collapse_widget(); 
	$widget->widget( $args, $instance ); 
	return ob_get_clean();
}




add_shortcode( 'mtphr_social_widget', 'mtphr_social_widget_display' );
/**
 * Display the social widget shortcode
 *
 * @since 2.1.18
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
		$sites_array[sanitize_text_field($group_assets[0])] = esc_url($group_assets[1]);
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
	$widget = new mtphr_social_widget(); 
	$widget->widget( $args, $instance ); 
	return ob_get_clean();
}




add_shortcode( 'mtphr_twitter_widget', 'mtphr_twitter_widget_display' );
/**
 * Display the contact widget shortcode
 *
 * @since 2.1.18
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
	$widget = new mtphr_twitter_widget(); 
	$widget->widget( $args, $instance );
	return ob_get_clean();
}




add_shortcode( 'mtphr_posts_widget', 'mtphr_posts_widget_display' );
/**
 * Display the posts widget shortcode
 *
 * @since 2.1.18
 */
function mtphr_posts_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'limit' => 3,
		'excerpt_length' => 72,
		'read_more' => __('Read more', 'mtphr-widgets'),
		'author' => '',
		'category' => ''
	), $atts ) );

	$instance = array(
		'title' => sanitize_text_field($title),
		'widget_limit' => intval($limit),
		'excerpt_length' => intval($excerpt_length),
		'read_more' => sanitize_text_field($read_more),
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
	$widget = new mtphr_posts_widget(); 
	$widget->widget( $args, $instance );
	return ob_get_clean();
}




add_shortcode( 'mtphr_comments_widget', 'mtphr_comments_widget_display' );
/**
 * Display the posts widget shortcode
 *
 * @since 2.1.18
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
	$widget = new mtphr_comments_widget(); 
	$widget->widget( $args, $instance );
	return ob_get_clean();
}




add_shortcode( 'mtphr_post_navigation_widget', 'mtphr_post_navigation_widget_display' );
/**
 * Display the post navigation shortcode
 *
 * @since 2.1.18
 */
function mtphr_post_navigation_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'home' => '{type}'.__(' home', 'mtphr-widgets'),
		'home_link' => '',
		'previous' => __('Previous', 'mtphr-widgets'),
		'next' => __('Next', 'mtphr-widgets'),
		'orderby' => 'date',
		'order' => 'DESC',
		'tax' => '',
		'operator' => 'IN',
		'terms' => ''
	), $atts ) );

	$instance = array(
		'title' => sanitize_text_field($title),
		'home' => sanitize_text_field($home),
		'home_link' => esc_url($home_link),
		'previous' => sanitize_text_field($previous),
		'next' => sanitize_text_field($next),
		'orderby' => $orderby,
		'order' => $order,
		'tax' => $tax,
		'operator' => $operator,
		'terms' => sanitize_text_field($terms)
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-post-navigation">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	$widget = new mtphr_post_navigation(); 
	$widget->widget( $args, $instance );
	return ob_get_clean();
}




add_shortcode( 'mtphr_tabbed_posts_widget', 'mtphr_tabbed_posts_widget_display' );
/**
 * Display the tabbed posts shortcode
 *
 * @since 2.1.18
 */
function mtphr_tabbed_posts_widget_display( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'title' => __('Blog Posts', 'mtphr-widgets'),
		'widget_limit' => 3,
		'excerpt_length' => 60,
		'date_format' => get_option('date_format')
	), $atts ) );

	$instance = array(
		'title' => sanitize_text_field($title),
		'widget_limit' => intval($widget_limit),
		'excerpt_length' => intval($excerpt_length),
		'date_format' => sanitize_text_field($date_format)
	);
	$args = array(
		'before_widget' => '<aside class="widget mtphr-tabbed-posts">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	ob_start();
	$widget = new mtphr_tabbed_posts_widget(); 
	$widget->widget( $args, $instance );
	return ob_get_clean();
}




