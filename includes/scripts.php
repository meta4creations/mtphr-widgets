<?php
/**
 * Load CSS & jQuery Scripts
 *
 * @package Metaphor Widgets
 */




add_action( 'admin_enqueue_scripts', 'mtphr_widgets_admin_scripts' );
/**
 * Load the admin scripts
 *
 * @since 2.0.0
 */
function mtphr_widgets_admin_scripts( $hook ) {

	global $typenow;

	if ( $hook == 'widgets.php' ) {
	
		// Load the style sheet
		wp_register_style( 'mtphr-widgets-metaboxer', MTPHR_WIDGETS_URL.'/includes/metaboxer/metaboxer.css', false, MTPHR_WIDGETS_VERSION );
		wp_enqueue_style( 'mtphr-widgets-metaboxer' );
		
		// Load scipts for the media uploader
		if(function_exists( 'wp_enqueue_media' )){
	    wp_enqueue_media();
		} else {
	    wp_enqueue_style('thickbox');
	    wp_enqueue_script('media-upload');
	    wp_enqueue_script('thickbox');
		}
	
		// Load the jQuery
		wp_register_script( 'mtphr-widgets-metaboxer', MTPHR_WIDGETS_URL.'/includes/metaboxer/metaboxer.js', array('jquery'), MTPHR_WIDGETS_VERSION, true );
		wp_enqueue_script( 'mtphr-widgets-metaboxer' );
		
		// Load the global widgets jquery
		wp_register_script( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'/assets/js/script-admin.js', array('jquery'), MTPHR_WIDGETS_VERSION );
	  wp_enqueue_script( 'mtphr-widgets-admin' );	
	}
	
	// Load the global widgets stylesheet
	wp_register_style( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'/assets/css/style-admin.css', false, MTPHR_WIDGETS_VERSION );
  wp_enqueue_style( 'mtphr-widgets-admin' );
}





add_action( 'wp_enqueue_scripts', 'mtphr_widgets_scripts' );
/**
 * Register scripts
 *
 * @since 2.0.0
 */
function mtphr_widgets_scripts(){

	// Load the global widgets stylesheet
	wp_register_style( 'mtphr-widgets', MTPHR_WIDGETS_URL.'/assets/css/style.css', false, MTPHR_WIDGETS_VERSION );
  wp_enqueue_style( 'mtphr-widgets' );
}



