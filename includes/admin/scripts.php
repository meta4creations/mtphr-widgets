<?php

/* --------------------------------------------------------- */
/* !Register the admin scripts - 2.1.15 */
/* --------------------------------------------------------- */

function mtphr_widgets_admin_scripts( $hook ) {

	global $typenow;

	if ( $hook == 'widgets.php' ) {
	
		// Load the fontastic font
		wp_register_style( 'mtphr-widgets-font', MTPHR_WIDGETS_URL.'/assets/fontastic/styles.css', false, MTPHR_WIDGETS_VERSION );
	  wp_enqueue_style( 'mtphr-widgets-font' );

		// Load scipts for the media uploader
		if(function_exists( 'wp_enqueue_media' )){
	    wp_enqueue_media();
		} else {
	    wp_enqueue_style('thickbox');
	    wp_enqueue_script('media-upload');
	    wp_enqueue_script('thickbox');
		}

		// Load the global widgets jquery
		wp_register_script( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'/assets/js/script-admin.js', array('jquery'), MTPHR_WIDGETS_VERSION );
	  wp_enqueue_script( 'mtphr-widgets-admin' );
	}

	// Load the global widgets stylesheet
	wp_register_style( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'/assets/css/style-admin.css', false, MTPHR_WIDGETS_VERSION );
  wp_enqueue_style( 'mtphr-widgets-admin' );
}
add_action( 'admin_enqueue_scripts', 'mtphr_widgets_admin_scripts' );