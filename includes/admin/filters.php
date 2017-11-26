<?php

/* --------------------------------------------------------- */
/* !Redirect the user to the Twitter tab - 2.1.9 */
/* --------------------------------------------------------- */

function mtphr_widgets_twitter_redirect() {

  if( is_admin() && isset($_GET['mtphr_widgets_twitter_authorize']) && isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) ) {
  	$location = get_admin_url().'plugins.php?page=mtphr_widgets_twitter_settings&oauth_token='.$_GET['oauth_token'].'&oauth_verifier='.$_GET['oauth_verifier'];
    wp_redirect( $location );
		exit;
  }
}
add_action( 'wp_loaded', 'mtphr_widgets_twitter_redirect' );



/* --------------------------------------------------------- */
/* !Reset the twitter options - 2.1.9 */
/* --------------------------------------------------------- */

function mtphr_widgets_twitter_reset() {

  if( is_admin() && isset($_GET['page']) && isset($_GET['settings-updated']) ) {
  	if( $_GET['page'] == 'mtphr_widgets_twitter_settings' && $_GET['settings-updated'] == 'reset' ) {
  		$settings = mtphr_widgets_twitter_settings();
  		$defaults = mtphr_widgets_twitter_settings_defaults();
  		$cache_time = $settings['cache_time'];
  		$defaults['cache_time'] = $cache_time;
	  	update_option( 'mtphr_widgets_twitter_settings', $defaults );
		}
  }
}
add_action( 'wp_loaded', 'mtphr_widgets_twitter_reset' );



/* --------------------------------------------------------- */
/* !Add the page icons modal - 2.3 */
/* --------------------------------------------------------- */

function mtphr_widgets_custom_icons() {

	if( mtphr_widgets_mtphr_shortcodes() ) {

		$content = '<div id="mtphr-widgets-icon-selects">';
			$content .= mtphr_shortcodes_icon_admin_display();
		$content .= '</div>';
		
		$args = array(
			'id' => 'mtphr-widgets-icon-modal',
			'title' => __('Select an icon', 'mtphr-widgets'),
			'button' => __('Use icon', 'mtphr-widgets')
		);			
		mtphr_shortcodes_modal( $content, $args );
	}
}
add_action( 'admin_footer', 'mtphr_widgets_custom_icons' );



/* --------------------------------------------------------- */
/* !Save extra icons - 2.3 */
/* --------------------------------------------------------- */

function mtphr_widgets_save_icon() {

	// Get access to the database
	global $wpdb;

	// Check the nonce
	check_ajax_referer( 'mtphr_widgets', 'security' );
	
	// Get variables
	$prefix = $_POST['prefix'];
	$id = $_POST['id'];
	
	$custom_icons = get_option( 'mtphr_widgets_custom_icons', array() );
	$custom_icons[$prefix.'__'.$id] = array(
		'prefix' => $prefix,
		'id' => $id
	);
	
	update_option( 'mtphr_widgets_custom_icons', $custom_icons );

	die(); // this is required to return a proper result
}
add_action( 'wp_ajax_mtphr_widgets_save_icon', 'mtphr_widgets_save_icon' );

