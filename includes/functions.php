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
 * @since 2.0.0
 */
function mtphr_widgets_localization() {
  load_plugin_textdomain( 'mtphr-widgets', false, MTPHR_WIDGETS_DIR.'languages/' );
}
 



/**
 * Convert twitter links
 *
 * @since 2.0.0
 */
function mtphr_widgets_twitter_links( $string ) {

	$string = make_clickable( $string );
	$string = preg_replace("/[@]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/\\1\" target=\"_blank\">\\0</a>", $string ); 
	$string = preg_replace("/[#]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">\\0</a>", $string );

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




/**
 * Get an array of social links
 *
 * @since 2.0.0
 */
function mtphr_widgets_social_sites() {
	
	$social_sites = array(
		'twitter' => 'Twitter',
		'facebook' => 'Facebook',
		'linkedin' => 'LinkedIn',
		'googleplus' => 'Google+',
		'flickr' => 'Flickr',
		'tridadvisor' => 'TripAdvisor',
		'reddit' => 'reddit',
		'posterous' => 'Posterous',
		'plurk' => 'Plurk',
		'ebay' => 'eBay',
		'netvibes' => 'Netvibes',
		'picasa' => 'Picasa',
		'digg' => 'Digg',
		'newsvine' => 'Newsvine',
		'rss' => 'RSS',
		'stumbleupon' => 'StumbleUpon',
		'aim' => 'AIM',
		'youtube' => 'YouTube',
		'lastfm' => 'Last.fm',
		'myspace' => 'Myspace',
		'msn' => 'MSN',
		'paypal' => 'PayPal',
		'windows' => 'Windows',
		'wordpress' => 'WordPress',
		'yahoo' => 'Yahoo!',
		'dribble' => 'Dribble',
		'apple' => 'Apple',
		'bebo' => 'Bebo',
		'cargo' => 'Cargo',
		'ember' => 'Ember',
		'evernote' => 'Evernote',
		'googletalk' => 'Google Talk',
		'skype' => 'Skype',
		'feedburner' => 'Feedburner',
		'tumblr' => 'Tumblr',
		'android' => 'Android',
		'bing' => 'Bing',
		'metacafe' => 'Metacafe',
		'orkut' => 'Orkut',
		'delicious' => 'Delicious',
		'amazon' => 'Amazon',
		'grooveshark' => 'Grooveshark',
		'deviantart' => 'deviantART',
		'behance' => 'Behance',
		'vimeo' => 'Vimeo',
		'mobileme' => 'MobileMe',
		'magnolia' => 'Magnolia',
		'mixx' => 'Mixx',
		'blogger' => 'Blogger',
		'yahoobuzz' => 'Yahoo! Buzz'
	);
	
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


