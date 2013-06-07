<?php
/**
 * The global settings
 *
 * @package Metaphor Widgets
 */




add_action( 'admin_menu', 'mtphr_widgets_settings_menu', 9 );
/**
 * Create the settings page
 *
 * @since 2.0.7
 */
function mtphr_widgets_settings_menu() {

	add_plugins_page(
		__( 'Metaphor Widgets', 'mtphr-widgets' ),			// The value used to populate the browser's title bar when the menu page is active
		__( 'Metaphor Widgets', 'mtphr-widgets' ),			// The label of this submenu item displayed in the menu
		'administrator',																// What roles are able to access this submenu item
		'mtphr_widgets_settings',												// The ID used to represent this submenu item
		'mtphr_widgets_settings_display'								// The callback function used to render the options for this submenu item
	);
}




add_action( 'admin_init', 'mtphr_widgets_initialize_settings' );
/**
 * Setup the custom options for the settings page
 *
 * @since 2.0.7
 */
function mtphr_widgets_initialize_settings() {

	/**
	 * General options sections
	 */
	$settings = get_option('mtphr_widgets_general_settings', array());
	$oauth = get_option('mtphr_widgets_twitter_oath', array());
	$access = get_option('mtphr_widgets_twitter_access', array());

	$pin = isset($settings['pin']) ? $settings['pin'] : '';

	$token = isset($oauth['oauth_token']) ? $oauth['oauth_token'] : '';
	$token_secret = isset($oauth['oauth_token_secret']) ? $oauth['oauth_token_secret'] : '';

	$display = isset($access['oauth_token']) ? true : false;
	$error = false;

	if( !$display ) {
		if( $pin != '' && $token != '' ) {
			$display = mtphr_widgets_twitter_tokens();
			$error = !$display;
		}
	}

	$fields = array();

	if( $display ) {

		$fields['token'] = array(
			'title' => '<strong>'.__( 'Access granted!', 'mtphr-widgets' ).'</strong>',
			'type' => 'access_tokens'
		);

		$fields['reset'] = array(
			'title' => '',
			'type' => 'twitter_reset'
		);

	} elseif( $error ) {

		$fields['error'] = array(
			'title' => __( 'Access error', 'mtphr-widgets' ),
			'type' => 'error',
		);

		$fields['reset'] = array(
			'title' => '',
			'type' => 'twitter_reset'
		);

	} else {

		$fields['authorize'] = array(
			'title' => __( 'Generate pin from Twitter', 'mtphr-widgets' ),
			'type' => 'authorize',
		);

		$fields['pin'] = array(
			'title' => __( 'Your pin', 'mtphr-widgets' ),
			'type' => 'text',
			'rows' => 20
		);
	}

	/*
$fields['test'] = array(
		'title' => __( 'Test Info', 'mtphr-widgets' ),
		'type' => 'twitter_test'
	);
*/

	if( false == get_option('mtphr_widgets_general_settings') ) {
		add_option( 'mtphr_widgets_general_settings' );
	}

	/* Register the general options */
	add_settings_section(
		'mtphr_widgets_general_settings_section',				// ID used to identify this section and with which to register options
		'',																					// Title to be displayed on the administration page
		'mtphr_widgets_general_settings_callback',			// Callback used to render the description of the section
		'mtphr_widgets_general_settings'								// Page on which to add this section of options
	);

	if( is_array($fields) ) {
		foreach( $fields as $id => $setting ) {
			$setting['option'] = 'mtphr_widgets_general_settings';
			$setting['option_id'] = $id;
			$setting['id'] = 'mtphr_widgets_general_settings['.$id.']';
			add_settings_field( $setting['id'], $setting['title'], 'mtphr_widgets_settings_callback', 'mtphr_widgets_general_settings', 'mtphr_widgets_general_settings_section', $setting);
		}
	}

	// Register the fields with WordPress
	register_setting( 'mtphr_widgets_general_settings', 'mtphr_widgets_general_settings' );
}




/**
 * Render the theme options page
 *
 * @since 2.0.7
 */
function mtphr_widgets_settings_display( $active_tab = null ) {
	?>
	<!-- Create a header in the default WordPress 'wrap' container -->
	<div class="wrap">

		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'Metaphor Widgets Settings', 'mtphr-widgets' ); ?></h2>
		<?php settings_errors(); ?>

		<ul style="margin-bottom:20px;" class="subsubsub">
		</ul>

		<br class="clear" />

		<form method="post" action="options.php">
			<?php
			settings_fields( 'mtphr_widgets_general_settings' );
			do_settings_sections( 'mtphr_widgets_general_settings' );
			submit_button();
			?>
		</form>

	</div><!-- /.wrap -->
	<?php
}




/**
 * General options section callback
 *
 * @since 2.0.9
 */
function mtphr_widgets_general_settings_callback() {
	?>
	<div style="margin-bottom: 20px;">
		<h4 style="margin-top:0;"><?php _e( 'Generate a pin from Twitter to grant access to Metaphor Widgets', 'mtphr-widgets' ); ?></h4>
	</div>
	<?php
}




/**
 * The custom field callback.
 *
 * @since 2.0.7
 */
function mtphr_widgets_settings_callback( $args ) {

	// First, we read the options collection
	if( isset($args['option']) ) {
		$options = get_option( $args['option'] );
		$value = isset( $options[$args['option_id']] ) ? $options[$args['option_id']] : '';
	} else {
		$value = get_option( $args['id'] );
	}
	if( $value == '' && isset($args['default']) ) {
		$value = $args['default'];
	}
	if( isset($args['type']) ) {

		echo '<div class="mtphr-widgets-metaboxer-field mtphr-widgets-metaboxer-'.$args['type'].'">';

		// Call the function to display the field
		if ( function_exists('mtphr_widgets_metaboxer_'.$args['type']) ) {
			call_user_func( 'mtphr_widgets_metaboxer_'.$args['type'], $args, $value );
		}

		echo '<div>';
	}

	// Add a descriptions
	if( isset($args['description']) ) {
		echo '<span class="description"><small>'.$args['description'].'</small></span>';
	}
}




/**
 * Authorize the app and get temp tokens
 *
 * @since 2.0.7
 */
function mtphr_widgets_metaboxer_authorize( $field, $value='' ) {

	// Get the settings
	$oauth = get_option('mtphr_widgets_twitter_oath', array());

	$tmhOAuth = new tmhOAuth(array(
	  'consumer_key'    => 'KEEIyPyhpjNBrnYCjwDoNg',
	  'consumer_secret' => '2jRa8Z5jWUnN8cDaiawTa6SPXZzWLkQJNWmL2z7ohc',
	));

	if( !isset($oauth['oauth_token']) ) {

		$callback = 'oob';
	  $params = array(
	    'oauth_callback' => $callback
	  );

	  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);

	  if ($code == 200) {

	  	// Update the settings with oauth
	  	$response = $tmhOAuth->extract_params($tmhOAuth->response['response']);
	  	update_option( 'mtphr_widgets_twitter_oath', $response );

	    $authurl = $tmhOAuth->url("oauth/authorize", '') .  "?oauth_token={$response['oauth_token']}";
	    echo '<a target="_blank" href="'. $authurl . '">' . $authurl . '</a>';

	  } else {
	    _e('<p><strong>There was an error connecting to Twitter.</strong><br/>Please reset the settings and try again in a couple minutes.</p>', 'mtphr-widgets');
	    mtphr_widgets_metaboxer_twitter_reset();
	  }
  } else {
    echo '<a style="float:left;" class="mtphr-widgets-twitter-reset button" href="#">'.__('Regenerate Link','mtphr-widgets').'</a><span style="float:left;margin-top:4px;" class="spinner"></span>';
  }
}




/**
 * Get the access tokens
 *
 * @since 2.0.7
 */
function mtphr_widgets_twitter_tokens() {

	// Get the settings
	$settings = get_option('mtphr_widgets_general_settings', array());
	$oauth = get_option('mtphr_widgets_twitter_oath', array());

	$tmhOAuth = new tmhOAuth(array(
	  'consumer_key'    => 'UBWUlsY6vzrajWcS4bw',
	  'consumer_secret' => 'YZAcJkFO1SPziucrztbruuJ53emjFaGXpBADlKaJFs8',
	));

  if( isset($oauth['oauth_token']) && isset($settings['pin']) ) {

	  $tmhOAuth->config['user_token']  = $oauth['oauth_token'];
	  $tmhOAuth->config['user_secret'] = $oauth['oauth_token_secret'];

	  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
	    'oauth_verifier' => $settings['pin']
	  ));

	  if ($code == 200) {

		  $access = $tmhOAuth->extract_params($tmhOAuth->response['response']);
	    update_option( 'mtphr_widgets_twitter_access', $access );

	    // Delete the cached files
	    mtphr_widgets_twitter_delete_cache();
	    return true;

	  } else {
	  	return false;
	  }
  } else {
  	return false;
  }
}




/**
 * Display access data
 *
 * @since 2.0.7
 */
function mtphr_widgets_metaboxer_access_tokens( $field, $value='' ) {

	$access = get_option('mtphr_widgets_twitter_access', array());

	echo '<p>'.__('Screen name:', 'mtphr-widgets').' <strong>'.$access['screen_name'].'</strong></p>';
	echo '<p>'.__('User ID:', 'mtphr-widgets').' <strong>'.$access['user_id'].'</strong></p>';
	echo '<p>'.__('Access token:', 'mtphr-widgets').' <strong>'.$access['oauth_token'].'</strong></p>';
	echo '<p>'.__('Access token secret:', 'mtphr-widgets').' <strong>'.$access['oauth_token_secret'].'</strong></p>';
}




/**
 * Display an error notice
 *
 * @since 2.0.7
 */
function mtphr_widgets_metaboxer_error( $field=false, $value='' ) {
	_e('<strong>There was an error accessing your account.</strong><br/>Please reset the settings and try again in a couple minutes.', 'mtphr-widgets');
}




/**
 * Create a reset link
 *
 * @since 2.0.7
 */
function mtphr_widgets_metaboxer_twitter_reset( $field=false, $value='' ) {
	echo '<a style="float:left;" class="mtphr-widgets-twitter-reset button" href="#">Reset Settings</a><span style="float:left;margin-top:4px;" class="spinner"></span>';
}




/**
 * Print out settings for testing
 *
 * @since 2.0.7
 */
function mtphr_widgets_metaboxer_twitter_test( $field, $value='' ) {

	// Get the settings
	echo '<strong>mtphr_widgets_general_settings</strong><br/>';
	$settings = get_option('mtphr_widgets_general_settings', array());
	print_r( $settings );

	echo '<p>&nbsp;</p><strong>mtphr_widgets_twitter_oath</strong><br/>';
	$oauth = get_option('mtphr_widgets_twitter_oath', array());
	print_r( $oauth );

	echo '<p>&nbsp;</p><strong>mtphr_widgets_twitter_access</strong><br/>';
	$access = get_option('mtphr_widgets_twitter_access', array());
	print_r( $access );
}




add_action('admin_footer', 'mtphr_widgets_twitter_reset');
/**
 * Add reset jQuery
 *
 * @since 2.0.7
 */
function mtphr_widgets_twitter_reset() {
	?>
	<script>
	jQuery( document ).ready( function($) {
		$('.mtphr-widgets-twitter-reset').click( function(e) {
			e.preventDefault();

			var $spinner = $(this).next();
			$spinner.show();

			// Create the display
			var data = {
				action: 'mtphr_widgets_twitter_ajax_reset',
				security: '<?php echo wp_create_nonce('mtphr-widgets'); ?>'
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post( ajaxurl, data, function( response ) {
				if( response ) {
					location.reload();
				}
			});
			delete_option( 'mtphr_widgets_general_settings' );
		});
	});
	</script>
	<?
}

add_action( 'wp_ajax_mtphr_widgets_twitter_ajax_reset', 'mtphr_widgets_twitter_ajax_reset' );
/**
 * Ajax function used to reset settings
 *
 * @since 2.0.7
 */
function mtphr_widgets_twitter_ajax_reset() {

	// Get access to the database
	global $wpdb;

	// Check the nonce
	check_ajax_referer( 'mtphr-widgets', 'security' );

	delete_option( 'mtphr_widgets_twitter_oath' );
	delete_option( 'mtphr_widgets_twitter_access' );
	delete_option( 'mtphr_widgets_general_settings' );

	return true;

	die(); // this is required to return a proper result
}
