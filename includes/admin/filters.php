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
/* !Add skype protocal - 2.1.19 */
/* --------------------------------------------------------- */

function mtphr_widgets_allow_skype_protocol( $protocols ){
	$protocols[] = 'skype';
	return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'mtphr_widgets_allow_skype_protocol' );

