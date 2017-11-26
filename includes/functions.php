<?php
/**
 * General functions
 *
 * @package Metaphor Widgets
 */




add_action( 'plugins_loaded', 'mtphr_widgets_localization' );
/**
 * Setup localization
 *
 * @since 2.0.9
 */
function mtphr_widgets_localization() {
  load_plugin_textdomain( 'mtphr-widgets', false, 'mtphr-widgets/languages/' );
}




/**
 * Convert twitter links
 *
 * @since 2.1.8
 */
function mtphr_widgets_twitter_links( $string, $window='' ) {

	$string = make_clickable( $string );
	$string = preg_replace( "/[@]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/\\1\">\\0</a>", $string );
	$string = preg_replace( "/[#]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/search?q=%23\\1\">\\0</a>", $string );
	
	if( $window != '' ) {
		$string = preg_replace( "|<a (.+?)>|i", "<a $1 target=\"_blank\">", $string );
	}

  return $string;
}




/**
 * Return an array of authors
 *
 * @since 2.0.0
 */
function mtphr_widgets_author_array( $add=false, $args=false ) {
	$defaults = array(
	  'exclude_admin' => false,
	  'show_fullname' => false,
	  'hide_empty'    => false,
	  'echo'          => false,
	  'html'          => false
	);
	$args = wp_parse_args( $args, $defaults );
	$authors = wp_list_authors( $args );
	$authors_array = explode( ', ', $authors );
	if( $add ) {
		array_unshift( $authors_array, $add );
	}
	return $authors_array;
}




/**
 * Set a maximum excerpt length
 *
 * @since 2.0.0
 */
function mtphr_widgets_post_excerpt( $charlength ) {
	$excerpt = get_the_excerpt();
	$charlength++;

	$output = '';
	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$output .= mb_substr( $subex, 0, $excut );
		} else {
			$output .= $subex;
		}
		$output .= '&hellip;';
	} else {
		$output .= $excerpt;
	}
	return $output;
}




/**
 * Set a maximum excerpt length
 *
 * @since 2.0.0
 */
function mtphr_widgets_comment_excerpt( $excerpt, $charlength ) {

	$charlength++;

	$output = '';
	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$output .= mb_substr( $subex, 0, $excut );
		} else {
			$output .= $subex;
		}
		$output .= '&hellip;';
	} else {
		$output .= $excerpt;
	}
	return $output;
}


/* --------------------------------------------------------- */
/* !Check if Metaphor Shortcodes is active 2.3 */
/* --------------------------------------------------------- */

function mtphr_widgets_mtphr_shortcodes() {
	if( function_exists('mtphr_shortcodes_modal') && function_exists('mtphr_shortcodes_icon_admin_display') ) {
		return true;
	}
}



/**
 * Get an array of social links
 *
 * @since 2.1.14
 */
function mtphr_widgets_social_sites() {

	$social_sites = array(
		'aim' => 'AIM',
		'amazon' => 'Amazon',
		'android' => 'Android',
		'apple' => 'Apple',
		'bebo' => 'Bebo',
		'behance' => 'Behance',
		'bing' => 'Bing',
		'blogger' => 'Blogger',
		'cargo' => 'Cargo',
		'delicious' => 'Delicious',
		'deviantart' => 'deviantART',
		'digg' => 'Digg',
		'dribble' => 'Dribble',
		'ebay' => 'eBay',
		'ember' => 'Ember',
		'envato' => 'Envato',
		'evernote' => 'Evernote',
		'facebook' => 'Facebook',
		'feedburner' => 'Feedburner',
		'flickr' => 'Flickr',
		'googleplus' => 'Google+',
		'googletalk' => 'Google Talk',
		'grooveshark' => 'Grooveshark',
		'houzz' => 'Houzz',
		'instagram' => 'Instagram',
		'lastfm' => 'Last.fm',
		'linkedin' => 'LinkedIn',
		'magnolia' => 'Magnolia',
		'metacafe' => 'Metacafe',
		'mixx' => 'Mixx',
		'mobileme' => 'MobileMe',
		'msn' => 'MSN',
		'myspace' => 'Myspace',
		'netvibes' => 'Netvibes',
		'newsvine' => 'Newsvine',
		'orkut' => 'Orkut',
		'paypal' => 'PayPal',
		'picasa' => 'Picasa',
		'pinterest' => 'Pinterest',
		'plurk' => 'Plurk',
		'posterous' => 'Posterous',
		'reddit' => 'reddit',
		'rss' => 'RSS',
		'skype' => 'Skype',
		'spotify' => 'Spotify',
		'stumbleupon' => 'StumbleUpon',
		'tripadvisor' => 'TripAdvisor',
		'tumblr' => 'Tumblr',
		'twitter' => 'Twitter',
		'vimeo' => 'Vimeo',
		'windows' => 'Windows',
		'wordpress' => 'WordPress',
		'yahoo' => 'Yahoo!',
		'yahoobuzz' => 'Yahoo! Buzz',
		'youtube' => 'YouTube'
	);
	
	if( mtphr_widgets_mtphr_shortcodes() ) {
		$custom_icons = get_option( 'mtphr_widgets_custom_icons', array() );
		$social_sites = array_merge( $social_sites, $custom_icons );
	}

	return $social_sites;
}




/**
 * Move existing links prior to 1.5.0 to the new list
 * Set existing links to blank
 *
 * @since 2.0.0
 */
function mtphr_widgets_social_update( $instance ) {

	if ( isset($instance['twitter_link']) && $instance['twitter_link'] != '' ) {
		$instance['sites'][] = array(
			'site' => 'twitter',
			'link' => $instance['twitter_link']
		);
		$instance['twitter_link'] = '';
	}
	if ( isset($instance['facebook_link']) && $instance['facebook_link'] != '' ) {
		$instance['sites'][] = array(
			'site' => 'facebook',
			'link' => $instance['facebook_link']
		);
		$instance['facebook_link'] = '';
	}
	if ( isset($instance['google_link']) && $instance['google_link'] != '' ) {
		$instance['sites'][] = array(
			'site' => 'googleplus',
			'link' => $instance['google_link']
		);
		$instance['google_link'] = '';
	}
	if ( isset($instance['linkedin_link']) && $instance['linkedin_link'] != '' ) {
		$instance['sites'][] = array(
			'site' => 'linkedin',
			'link' => $instance['linkedin_link']
		);
		$instance['linkedin_link'] = '';
	}

	return $instance;
}



/**
 * Update the social sites to 2.1.8
 *
 * @since 2.1.8
 */
function mtphr_widgets_social_update_2_1_8( $instance ) {

	if( is_array($instance['sites']) && count($instance['sites']) > 0 && isset($instance['sites'][0]['site'])  ) {
	
		$updated = array();
		foreach( $instance['sites'] as $site ) {
			if( isset($site['site']) ) {
				$updated[$site['site']] = $site['link'];
			}
		}
		$instance['sites'] = $updated;
	}

	return $instance;
}




/**
 * Move existing contaact info prior to 1.5.0 to the new list
 * Set existing contact info to blank
 *
 * @since 2.0.0
 */
function mtphr_widgets_contact_update( $instance ) {

	if ( isset($instance['email']) && $instance['email'] != '' ) {
		$instance['contact_info']['email'] = array(
			'title' => __('Email', 'mtphr-widgets'),
			'description' => $instance['email']
		);
		$instance['email'] = '';
	}
	if ( isset($instance['telephone']) && $instance['telephone'] != '' ) {
		$instance['contact_info']['telephone'] = array(
			'title' => __('Tel', 'mtphr-widgets'),
			'description' => $instance['telephone']
		);
		$instance['telephone'] = '';
	}
	if ( isset($instance['fax']) && $instance['fax'] != '' ) {
		$instance['contact_info']['fax'] = array(
			'title' => __('Fax', 'mtphr-widgets'),
			'description' => $instance['fax']
		);
		$instance['fax'] = '';
	}
	if ( isset($instance['address']) && $instance['address'] != '' ) {
		$instance['contact_info']['address'] = array(
			'title' => '',
			'description' => $instance['address']
		);
		$instance['address'] = '';
	}

	return $instance;
}




/**
 * Create the css background positions
 *
 * @since 2.0.0
 */
/*
function mtphr_widgets_social_site_css() {
	$s = mtphr_widgets_social_sites();
	$left = 0;
	$top = 0;
	foreach( $s as $i=>$val ) { ?>
		#site-footer  .mtphr-social-widget-<?php echo $i; ?> {
			background-position: <?php echo intval(-43*$left); ?>px <?php echo (intval(-43*$top)); ?>px;
		}<br/>
		<?php
		$left = intval($left+1);
		if( $left == 10 ) {
			$left = 0;
			$top = intval($top+1);
		}
	}
}
*/




/**
 * Check for Twitter access
 *
 * @since 2.1.9
 */
if( !function_exists('mtphr_widgets_check_twitter_access') ) {
function mtphr_widgets_check_twitter_access() {

	$settings = mtphr_widgets_twitter_settings();
	if( $settings['access_token'] == '' ) {
		return false;
	} else {
		return true;
	}
}
}




/**
 * Delete the cached feeds
 *
 * @since 2.0.7
 */
function mtphr_widgets_twitter_delete_cache() {

	$directory = MTPHR_WIDGETS_DIR.'assets/cache/';
	$files = scandir($directory);
	$files = array_slice( $files, 2 );
	if( is_array($files) && count($files) > 0 ) {
		foreach ( $files as $file ) {
			unlink($directory.$file);
		}
	}
}