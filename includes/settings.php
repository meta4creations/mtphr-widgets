<?php

/* --------------------------------------------------------- */
/* !Get the settings - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings') ) {
function mtphr_widgets_twitter_settings() {
	$settings = get_option( 'mtphr_widgets_twitter_settings', array() );
	
	// Translate the settings
	$settings = mtphr_widgets_translate_settings( $settings );

	return wp_parse_args( $settings, mtphr_widgets_twitter_settings_defaults() );
}
}
if( !function_exists('mtphr_widgets_twitter_settings_defaults') ) {
function mtphr_widgets_twitter_settings_defaults() {
	$defaults = array(
		'redirect_uri' => get_admin_url().'?mtphr_widgets_twitter_authorize=true',
		'key' => '',
		'secret' => '',
		'token' => '',
		'token_secret' => '',
		'access_token' => '',
		'userid' => '',
		'username' => '',
		'fullname' => '',
		'profile_picture' => '',
		'cache_time' => 10
	);
	return $defaults;
}
}



/* --------------------------------------------------------- */
/* !Initializes the settings page - 2.1.9 */
/* --------------------------------------------------------- */

function mtphr_widgets_twitter_initialize_settings() {

	$settings = mtphr_widgets_twitter_settings();


	/* --------------------------------------------------------- */
	/* !Add the setting sections - 2.1.9 */
	/* --------------------------------------------------------- */

	add_settings_section( 'mtphr_widgets_twitter_settings_section', __( 'Twitter settings', 'mtphr-widgets' ).'<input type="submit" class="button button-small" value="'.__('Save Changes', 'mtphr-widgets').'">', false, 'mtphr_widgets_twitter_settings' );
	
	$reset_url = get_admin_url().'plugins.php?page=mtphr_widgets_twitter_settings&settings-updated=reset';
	$reset_auth = '<p><a id="mtphr-widgets-twitter-reset" class="button button-small" href="'.$reset_url.'">'.__('Reset Authorization', 'mtphr-widgets').'</a></p>';
	$reset_info = '<p><a id="mtphr-widgets-twitter-reset" class="button button-small" href="'.$reset_url.'">'.__('Reset Information', 'mtphr-widgets').'</a></p>';


	/* --------------------------------------------------------- */
	/* !Add the settings - 2.1.9 */
	/* --------------------------------------------------------- */

	if( $settings['access_token'] == '' && ($settings['key'] == '' || $settings['secret'] == '') ) {
		
		/* API Information */
		$title = mtphr_widgets_settings_label( __( 'API Information', 'mtphr-widgets' ), __('In order to connect to Twitter you must create a custom application with your account. Please follow the directions here to quickly get your application up and running!', 'mtphr-widgets') );
		add_settings_field( 'mtphr_widgets_twitter_settings_api_info', $title, 'mtphr_widgets_twitter_settings_api_info', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_section', array('settings' => $settings) );
	
	} elseif( $settings['access_token'] == '' ) {
	
		if( isset($_GET['oauth_token']) && isset($_GET['oauth_verifier']) ) {
			
			// Get the access token
			$url = 'https://api.twitter.com/oauth/access_token';	
			$args = array(
				'oauth_token' => $_GET['oauth_token'],
				'oauth_verifier' => $_GET['oauth_verifier'],
			);
			$twitter = mtphr_widgets_twitter_oauth( $url, $args );	
			if( $twitter['response']['code'] == '200' ) {
	
				parse_str( $twitter['body'], $response );
				
				// Update the token_secret
				$settings['access_token'] = $response['oauth_token'];
				$settings['token'] = $response['oauth_token'];
				$settings['token_secret'] = $response['oauth_token_secret'];
				$settings['userid'] = $response['user_id'];
				$settings['username'] = $response['screen_name'];
				
				update_option( 'mtphr_widgets_twitter_settings', $settings );

				//User Info
				$title = mtphr_widgets_settings_label( __( 'Twitter User Info', 'mtphr-widgets' ), sprintf(__('Congratulation! You are now connected to Twitter. %s', 'mtphr-widgets'), $reset_auth) );
				add_settings_field( 'mtphr_widgets_twitter_settings_user_info', $title, 'mtphr_widgets_twitter_settings_user_info', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_section', array('settings' => $settings) );
				
			} else {
				
				/* Error */
				$title = mtphr_widgets_settings_label( __( 'Twitter Error', 'mtphr-widgets' ), sprintf(__('Whoops! There seems to have been an error connecting to Twitter. %s', 'mtphr-widgets'), $reset_info) );
				add_settings_field( 'mtphr_widgets_twitter_settings_error', $title, 'mtphr_widgets_twitter_settings_error', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_section', array('settings' => $settings, 'error_code' => $twitter['response']['code'], 'error_message' => $twitter['response']['message']) );
				
			}

		} else {

			/* Authorization */
			$title = mtphr_widgets_settings_label( __( 'Authorization', 'mtphr-widgets' ), sprintf(__('You must authorize your account with Twitter to use Ditty Twitter Ticker %s', 'mtphr-widgets'), $reset_info) );
			add_settings_field( 'mtphr_widgets_twitter_settings_authorize', $title, 'mtphr_widgets_twitter_settings_authorize', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_section', array('settings' => $settings) );
		}

	} else {

		/* User Info */
		$title = mtphr_widgets_settings_label( __( 'Twitter User Info', 'mtphr-widgets' ), sprintf(__('Congratulation! You are now connected to Twitter. %s', 'mtphr-widgets'), $reset_auth) );
		add_settings_field( 'mtphr_widgets_twitter_settings_user_info', $title, 'mtphr_widgets_twitter_settings_user_info', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_section', array('settings' => $settings) );
	}
	
	/* Cache time */
	$title = mtphr_widgets_settings_label( __( 'Cache Time', 'mtphr-widgets' ), __('Set the amount of time your feeds should stay cached', 'mtphr-widgets') );
	add_settings_field( 'mtphr_widgets_twitter_settings_cache_time', $title, 'mtphr_widgets_twitter_settings_cache_time', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_section', array('settings' => $settings) );


	/* --------------------------------------------------------- */
	/* !Register the settings - 2.1.9 */
	/* --------------------------------------------------------- */

	if( false == get_option('mtphr_widgets_twitter_settings') ) {
		add_option( 'mtphr_widgets_twitter_settings' );
	}
	register_setting( 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings', 'mtphr_widgets_twitter_settings_sanitize' );

}
add_action( 'admin_init', 'mtphr_widgets_twitter_initialize_settings' );



/* --------------------------------------------------------- */
/* !API Information - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings_api_info') ) {
function mtphr_widgets_twitter_settings_api_info( $args ) {

	$settings = $args['settings'];
	
	echo '<div id="mtphr_widgets_twitter_settings_api_info">';
		echo '<table class="mtphr-widgets-instructions">';
			
			echo '<div id="mtphr_widgets_twitter_settings_api_info">';
		echo '<table class="mtphr-widgets-instructions">';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">1</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						$url = '<a href="https://apps.twitter.com" target="_blank"><strong>'.__('My applications', 'mtphr-widgets').'</strong></a>';
						echo sprintf(__( 'Go to your Twitter Apps page %s and log into your account', 'mtphr-widgets' ), $url);
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">2</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						$url = '<a class="button button-small" href="https://apps.twitter.com/app/new" target="_blank"><strong>'.__('Create New App', 'mtphr-widgets').'</strong></a>';
						echo sprintf(__( 'Select %s', 'mtphr-widgets' ), $url);
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">3</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Give your application an appropriate <strong>Name</strong> and <strong>Description</strong>', 'mtphr-widgets' );
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">4</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Copy and paste the following URL into the <strong>Website</strong> field', 'mtphr-widgets' ).'<br/>';
						echo '<pre><strong>'.home_url().'</strong></pre>';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">5</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Copy and paste the following URL into the <strong>Callback URL</strong> field', 'mtphr-widgets' ).'<br/>';
						echo '<pre><strong>'.$settings['redirect_uri'].'</strong></pre>';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">6</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Select the checkbox to agree to the <strong>Developer Agreement</strong>', 'mtphr-widgets' ).' ';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">7</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Select <strong>Create your Twitter Application</strong> to register your new application', 'mtphr-widgets' ).' ';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">8</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Once your application is created select the <strong>Keys and Access Tokens</strong> tab on your App page', 'mtphr-widgets' ).' ';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">9</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Copy and paste the <strong>Consumer Key (API Key)</strong> here', 'mtphr-widgets' ).' ';
						echo '<input style="width:auto;" type="text" name="mtphr_widgets_twitter_settings[key]" value="'.$settings['key'].'" placeholder="'.__('API key', 'mtphr-widgets').'" size="30" />';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">10</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Copy and paste the <strong>Consumer Secret (API Secret)</strong> here', 'mtphr-widgets' ).' ';
						echo '<input style="width:auto;" type="text" name="mtphr_widgets_twitter_settings[secret]" value="'.$settings['secret'].'" placeholder="'.__('API secret', 'mtphr-widgets').'" size="30" />';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			
			echo '<tr class="mtphr-widgets-instruction">';
				echo '<td class="mtphr-widgets-instruction-label">';
					echo '<span class="mtphr-widgets-instruction-number">11</span>';
				echo '</td>';
				echo '<td class="mtphr-widgets-instruction-info">';
					echo '<div class="mtphr-widgets-instruction-info-wrapper">';
						echo __( 'Select <strong>Save Changes</strong> below', 'mtphr-widgets' ).' ';
					echo '</div>';
				echo '</td>';
			echo '</tr>';

		echo '</table>';

	echo '</div>';
}
}


/* --------------------------------------------------------- */
/* !Authorize - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings_authorize') ) {
function mtphr_widgets_twitter_settings_authorize( $args ) {
	
	$settings = $args['settings'];
	
	echo '<div id="mtphr_widgets_twitter_settings_authorize" class="clearfix">';	
	
		$url = 'https://api.twitter.com/oauth/request_token';
		$callback = get_admin_url().'?mtphr_widgets_twitter_authorize=true';
		
		// Remove any tildes from the callback
		$callback = preg_replace( '%~%', '', $callback );
		
		$args = array(
			'oauth_callback' => urlencode( $callback )
		);
		$fields = array(
			'oauth_callback' => $callback
		);
		$twitter = mtphr_widgets_twitter_oauth( $url, $args, $fields );
		
		if( $twitter['response']['code'] == '200' ) {

			parse_str( $twitter['body'], $response );
			
			// Update the token_secret
			$settings['token'] = $response['oauth_token'];
			$settings['token_secret'] = $response['oauth_token_secret'];
			update_option( 'mtphr_widgets_twitter_settings', $settings );
			
			// Render the authorize button
			echo '<p><a id="mtphr-widgets-twitter-authorize" class="button button-primary" href="https://api.twitter.com/oauth/authorize?oauth_token='.$response['oauth_token'].'">'.__('Authorize Twitter', 'mtphr-widgets').'</a></p>';

		} else {
		
			echo '<p>'.__('There was an error with the Twitter API.', 'mtphr-widgets').'</p>';
		}
		
	echo '</div>';
}
}


/* --------------------------------------------------------- */
/* !Error - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings_error') ) {
function mtphr_widgets_twitter_settings_error( $args ) {
	
	$settings = $args['settings'];
	$error_code = $args['error_code'];
	$error_message = $args['error_message'];
	
	echo '<div id="mtphr_widgets_twitter_settings_error" class="clearfix">';	
	
		echo '<p><strong>'.__('Sorry, there was an issue authorizing Twitter.', 'mtphr-widgets').'</strong><br/>';
		echo sprintf(__('Error %s:', 'mtphr-widgets'), $error_code).' '.$error_message.'</p>';
		
	echo '</div>';
}
}


/* --------------------------------------------------------- */
/* !User Info - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings_user_info') ) {
function mtphr_widgets_twitter_settings_user_info( $args ) {

	$settings = $args['settings'];
	
	echo '<div id="mtphr_widgets_twitter_settings_user_info" class="clearfix">';	
		if( $settings['access_token'] != '' ) {
			echo '<div id="mtphr-widgets-twitter-credentials">';
				if( $settings['profile_picture'] == '' ) {		
					$userinfo = mtphr_widgets_twitter_userinfo( $settings['username'], $settings );
					if( $userinfo ) {
						$settings['fullname'] = $userinfo['name'];
						$settings['profile_picture'] = $userinfo['profile_image_url'];
						update_option( 'mtphr_widgets_twitter_settings', $settings );
					}
					echo '<img src="'.$settings['profile_picture'].'" />';
				} else {
					echo '<img src="'.$settings['profile_picture'].'" />';
				}
				echo '<p>'.$settings['username'].'</p>';
			echo '</div>';
		}
	echo '</div>';
}
}


/* --------------------------------------------------------- */
/* !Cache time - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings_cache_time') ) {
function mtphr_widgets_twitter_settings_cache_time( $args ) {

	$settings = $args['settings'];
	
	echo '<div id="mtphr_widgets_twitter_settings_cache_time" class="clearfix">';	
		echo '<label><input class="small-text" type="number" name="mtphr_widgets_twitter_settings[cache_time]" value="'.$settings['cache_time'].'" /> '.__('Minutes', 'mtphr-widgets').'</label>';
	echo '</div>';
}
}



/* --------------------------------------------------------- */
/* !Create a settings label - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_settings_label') ) {
function mtphr_widgets_settings_label( $title, $description = '' ) {

	$label = '<div class="mtphr-widgets-label-alt">';
		$label .= '<label>'.$title.'</label>';
		if( $description != '' ) {
			$label .= '<small>'.$description.'</small>';
		}
	$label .= '</div>';

	return $label;
}
}



/* --------------------------------------------------------- */
/* !Sanitize the setting fields - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_settings_sanitize') ) {
function mtphr_widgets_twitter_settings_sanitize( $fields ) {

	$settings = mtphr_widgets_twitter_settings();
	
	// Twitter
	$fields['key'] = isset( $fields['key'] ) ? sanitize_text_field($fields['key']) : $settings['key'];
	$fields['secret'] = isset( $fields['secret'] ) ? sanitize_text_field($fields['secret']) : $settings['secret'];
	$fields['cache_time'] = isset( $fields['cache_time'] ) ? intval($fields['cache_time']) : $settings['cache_time'];
	
	return wp_parse_args( $fields, get_option('mtphr_widgets_twitter_settings', array()) );

	return $fields;
}
}




/* --------------------------------------------------------- */
/* !Add a menu page to display options - 2.1.9 */
/* --------------------------------------------------------- */

function mtphr_widgets_twitter_settings_page() {

	add_plugins_page(
		__( 'Metaphor Widgets', 'mtphr-widgets' ),			// The value used to populate the browser's title bar when the menu page is active
		__( 'Metaphor Widgets', 'mtphr-widgets' ),			// The label of this submenu item displayed in the menu
		'administrator',																// What roles are able to access this submenu item
		'mtphr_widgets_twitter_settings',												// The ID used to represent this submenu item
		'mtphr_widgets_twitter_settings_display'								// The callback function used to render the options for this submenu item
	);
}
add_action( 'admin_menu', 'mtphr_widgets_twitter_settings_page' );

/* --------------------------------------------------------- */
/* !Render the settings page - 2.1.9 */
/* --------------------------------------------------------- */

function mtphr_widgets_twitter_settings_display() {
	$settings = mtphr_widgets_twitter_settings();
	?>
	<div class="wrap">

		<div id="icon-mtphr_widgets" class="icon32"></div>
		<h2><?php _e('Metaphor Widgets Settings', 'mtphr-widgets'); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'mtphr_widgets_twitter_settings' );
			do_settings_sections( 'mtphr_widgets_twitter_settings' );
			submit_button();
			?>
		</form>

	</div><!-- /.wrap -->
	<?php
}
