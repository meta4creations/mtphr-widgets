<?php

/* --------------------------------------------------------- */
/* !Register scripts - 2.3 */
/* --------------------------------------------------------- */

function mtphr_widgets_scripts(){

	// Load the fontastic font
	wp_enqueue_style( 'mtphr-widgets-font', MTPHR_WIDGETS_URL.'assets/fontastic/styles.css', false, filemtime(MTPHR_WIDGETS_DIR.'assets/fontastic/styles.css') );

  // Load the global widgets stylesheet
	wp_enqueue_style( 'mtphr-widgets', MTPHR_WIDGETS_URL.'assets/css/style.css', false, filemtime(MTPHR_WIDGETS_DIR.'assets/css/style.css') );

  wp_register_script( 'jquery-easing', MTPHR_WIDGETS_URL.'assets/js/jquery.easing.1.3.js', array('jquery'), '1.3', true );

  // Load the global widgets js
	wp_enqueue_script( 'mtphr-widgets', MTPHR_WIDGETS_URL.'assets/js/script.js', array('jquery','jquery-easing'), filemtime(MTPHR_WIDGETS_DIR.'assets/js/script.js'), true );
  
  // Load mtphr tabs scripts
	wp_enqueue_style( 'mtphr-tabs', MTPHR_WIDGETS_URL.'assets/mtphr-tabs/mtphr-tabs.css', false, filemtime(MTPHR_WIDGETS_DIR.'assets/mtphr-tabs/mtphr-tabs.css') );
  wp_register_script( 'mtphr-tabs', MTPHR_WIDGETS_URL.'assets/mtphr-tabs/mtphr-tabs.js', false, filemtime(MTPHR_WIDGETS_DIR.'assets/mtphr-tabs/mtphr-tabs.js') );
}
add_action( 'wp_enqueue_scripts', 'mtphr_widgets_scripts' );



/* --------------------------------------------------------- */
/* !Setup the class scripts - 2.1.10 */
/* --------------------------------------------------------- */

function mtphr_widgets_footer_scripts() {

	if( is_active_widget( false, false, 'mtphr-tabbed-posts' ) ) {
		wp_print_scripts( 'mtphr-tabs' );
		?>
		<script>
			jQuery( window ).load( function() {
				jQuery('.mtphr-tabs').mtphr_tabs();
			});
		</script>
		<?php
	}
}
add_action( 'wp_footer', 'mtphr_widgets_footer_scripts' );


