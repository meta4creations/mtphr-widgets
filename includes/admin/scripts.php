<?php

/* --------------------------------------------------------- */
/* !Register the admin scripts - 2.3 */
/* --------------------------------------------------------- */

function mtphr_widgets_admin_scripts( $hook ) {

	global $typenow;
	
	// Load the fontastic font
	wp_enqueue_style( 'mtphr-widgets-font', MTPHR_WIDGETS_URL.'assets/fontastic/styles.css', false, filemtime(MTPHR_WIDGETS_DIR.'assets/fontastic/styles.css') );
  
	// Load scipts for the media uploader
	if(function_exists( 'wp_enqueue_media' )){
    wp_enqueue_media();
	} else {
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
	}

	// Load the global widgets jquery
	wp_enqueue_script( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'assets/js/script-admin.js', array('jquery'), filemtime(MTPHR_WIDGETS_DIR.'assets/js/script-admin.js') );
  wp_localize_script( 'mtphr-widgets-admin', 'mtphr_widgets_vars', array(
  		'security' => wp_create_nonce( 'mtphr_widgets' ),
		)
	);

	// Load the global widgets stylesheet
	wp_enqueue_style( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'assets/css/style-admin.css', false, filemtime(MTPHR_WIDGETS_DIR.'assets/css/style-admin.css') );
}
add_action( 'admin_enqueue_scripts', 'mtphr_widgets_admin_scripts' );