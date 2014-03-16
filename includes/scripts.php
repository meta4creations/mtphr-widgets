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

	//if ( $hook == 'widgets.php' ) {
	
		// Load the fontastic font
		wp_register_style( 'mtphr-widgets-font', MTPHR_WIDGETS_URL.'/assets/fontastic/styles.css', false, MTPHR_WIDGETS_VERSION );
	  wp_enqueue_style( 'mtphr-widgets-font' );

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
	//}

	// Load the global widgets stylesheet
	wp_register_style( 'mtphr-widgets-admin', MTPHR_WIDGETS_URL.'/assets/css/style-admin.css', false, MTPHR_WIDGETS_VERSION );
  wp_enqueue_style( 'mtphr-widgets-admin' );
}





add_action( 'wp_enqueue_scripts', 'mtphr_widgets_scripts' );
/**
 * Register scripts
 *
 * @since 2.1.2
 */
function mtphr_widgets_scripts(){

	// Load the fontastic font
	wp_register_style( 'mtphr-widgets-font', MTPHR_WIDGETS_URL.'/assets/fontastic/styles.css', false, MTPHR_WIDGETS_VERSION );
  wp_enqueue_style( 'mtphr-widgets-font' );

  // Load the global widgets stylesheet
	wp_register_style( 'mtphr-widgets', MTPHR_WIDGETS_URL.'/assets/css/style.css', false, MTPHR_WIDGETS_VERSION );
  wp_enqueue_style( 'mtphr-widgets' );

  wp_register_script( 'jquery-easing', MTPHR_WIDGETS_URL.'/assets/js/jquery.easing.1.3.js', array('jquery'), MTPHR_WIDGETS_VERSION, true );

  // Load the global widgets js
	wp_register_script( 'mtphr-widgets', MTPHR_WIDGETS_URL.'/assets/js/script.js', array('jquery','jquery-easing'), MTPHR_WIDGETS_VERSION, true );
  wp_enqueue_script( 'mtphr-widgets' );

  // Register jQuery classes
  wp_register_script( 'mtphr-tabbed-posts', MTPHR_WIDGETS_URL.'/assets/js/mtphr-tabbed-posts.js', array('jquery', 'jquery-easing'), MTPHR_WIDGETS_VERSION, true );
}




add_action( 'wp_footer', 'mtphr_widgets_footer_scripts' );
/**
 * Add the class scripts
 *
 * @since 2.0.9
 */
function mtphr_widgets_footer_scripts() {

	if( is_active_widget( false, false, 'mtphr-tabbed-posts' ) ) {
		wp_print_scripts( 'mtphr-tabbed-posts' );
		?>
		<script>
			jQuery( window ).load( function() {
				jQuery('.mtphr-tabbed-posts-widget').mtphr_tabbed_posts();
			});
		</script>
		<?php
	}
}


