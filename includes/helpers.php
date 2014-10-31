<?php

/* --------------------------------------------------------- */
/* !Get the feed - 1.3.2 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_get_feed') ) {
function mtphr_widgets_twitter_get_feed( $data, $settings ) {

	$settings = mtphr_widgets_twitter_settings();
	
	// Get the access token

	if( $data['type'] == 'user_timeline' ) {
		$response = mtphr_widgets_twitter_user_timeline( $data['handle'], $settings );
	} elseif( $data['type'] == 'search' ) {
		$response = mtphr_widgets_twitter_search( $data['handle'], $settings );
	} elseif( $data['type'] == 'list' ) {
		$response = mtphr_widgets_twitter_list( $data['handle'], $settings );
	}
	
	if( $response ) {
		return json_encode($response);
	}
}
}


/* --------------------------------------------------------- */
/* !Get a user timeline - 2.1.13 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_user_timeline') ) {
function mtphr_widgets_twitter_user_timeline( $handle, $settings=false ) {

	$settings = $settings ? $settings : mtphr_widgets_twitter_settings();

	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$args = $fields = array(
		'screen_name' => urlencode( $handle ),
		'oauth_token' => $settings['access_token'],
	);
	$fields['screen_name'] = $handle;
	$twitter = mtphr_widgets_twitter_oauth( $url, $args, $fields );
	
	if( is_wp_error($twitter) ) {
   
   $error_string = $twitter->get_error_message();
   return '<div id="message" class="error"><p>' . $error_string . '</p></div>';
   
	} elseif( $twitter['response']['code'] == '200' ) {
		return $twitter['body'];
		
	} else {
		return '<div id="message" class="error"><p>'.sprintf(__('Error: %s', 'mtphr-widgets'), $twitter['body']).'</p></div>';	
	}
}
}


/* --------------------------------------------------------- */
/* !Get a search - 1.3.2 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_search') ) {
function mtphr_widgets_twitter_search( $handle, $settings=false ) {

	$settings = $settings ? $settings : mtphr_widgets_twitter_settings();

	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$args = $fields = array(
		'q' => urlencode( $handle ),
		'oauth_token' => $settings['access_token'],
	);
	$fields['q'] = $handle;
	$twitter = mtphr_widgets_twitter_oauth( $url, $args, $fields );
	if( $twitter['response']['code'] == '200' ) {
		$response = json_decode($twitter['body'], true);
		return $response;
	}
}
}


/* --------------------------------------------------------- */
/* !Get a list - 1.3.2 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_list') ) {
function mtphr_widgets_twitter_list( $handle, $settings=false ) {

	$settings = $settings ? $settings : mtphr_widgets_twitter_settings();

	$url = 'https://api.twitter.com/1.1/lists/statuses.json';
	$args = $fields = array(
		'slug' => urlencode( $handle ),
		'owner_screen_name' => $settings['username'],
		'owner_id' => $settings['userid'],
	  'count' => 200,
	  'include_rts' => true,
		'oauth_token' => $settings['access_token'],
	);
	$fields['slug'] = $handle;
	$twitter = mtphr_widgets_twitter_oauth( $url, $args, $fields );
	if( $twitter['response']['code'] == '200' ) {
		$response = json_decode($twitter['body'], true);
		return $response;
	}
}
}


/* --------------------------------------------------------- */
/* !Get a users info - 1.3.2 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_userinfo') ) {
function mtphr_widgets_twitter_userinfo( $username, $settings=false ) {

	$settings = $settings ? $settings : mtphr_widgets_twitter_settings();

	$url = 'https://api.twitter.com/1.1/users/show.json';
	$args = $fields = array(
		'screen_name' => urlencode( $username ),
		'oauth_token' => $settings['access_token'],
	);
	$fields['screen_name'] = $username;
	$twitter = mtphr_widgets_twitter_oauth( $url, $args, $fields );
	if( $twitter['response']['code'] == '200' ) {
		$response = json_decode($twitter['body'], true);
		return $response;
	}
}
}



/* --------------------------------------------------------- */
/* !Twitter oauth - 1.3.2 */
/* --------------------------------------------------------- */

if( !function_exists('mtphr_widgets_twitter_oauth') ) {
function mtphr_widgets_twitter_oauth( $url, $args, $fields=false ) {

	$settings = mtphr_widgets_twitter_settings();
	$hash_key = $settings['secret'].'&'.$settings['token_secret'];
	$nonce = md5(microtime(true));
	$timestamp = time();
	
	// Add the to args and alphabetize
	$args['oauth_consumer_key'] = $settings['key'];
	$args['oauth_nonce'] = $nonce;
	$args['oauth_timestamp'] = $timestamp;
	$args['oauth_signature_method'] = 'HMAC-SHA1';
	$args['oauth_version'] = '1.0';
	ksort( $args );
	
	// Create the base string
	$base = '';
	foreach( $args as $key=>$value ) {
		$base .= $key.'='.$value.'&';
	}
	$base = rtrim( $base, '&' );

	// Create the signature
	$signature = 'GET&'.urlencode($url).'&'.urlencode($base);	
	$oauth_signature = base64_encode( hash_hmac('sha1',$signature,$hash_key,true) );
	
	// Add the fields and alphabetize
	if( !$fields ) {
		$fields = $args;
	} else {
		$fields['oauth_consumer_key'] = $settings['key'];
		$fields['oauth_nonce'] = $nonce;
		$fields['oauth_timestamp'] = $timestamp;
		$fields['oauth_signature_method'] = 'HMAC-SHA1';
		$fields['oauth_version'] = '1.0';
	}
	$fields['oauth_signature'] = $oauth_signature;
	ksort( $fields );
	
	// Create the fields string
	$fields_string = '';
	foreach( $fields as $key=>$value ) {
		$fields_string .= $key.'='.urlencode($value).'&';
	}
	$fields_string = rtrim( $fields_string, '&' );
	
	$args = apply_filters( 'mtphr_widgets_twitter_remote_get_args', array() );
	return wp_remote_get( $url.'?'.$fields_string, $args );
}
}