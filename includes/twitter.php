<?php

/* Register the widget - @since 1.0 */
add_action( 'widgets_init', 'mtphr_twitter_widget_init' );

/**
 * Register the widget
 *
 * @since 1.0
 */
function mtphr_twitter_widget_init() {
	register_widget( 'mtphr_twitter_widget' );
}




/**
 * Create a class for the widget
 *
 * @since 1.0
 */
class mtphr_twitter_widget extends WP_Widget {

/**
 * Widget setup
 *
 * @since 1.0
 */
function mtphr_twitter_widget() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'mtphr-twitter-widget',
		'description' => __('Displays a users latest twitter comments.', 'mtphr-widgets')
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mtphr-twitter'
	);

	// Create the widget
	$this->WP_Widget( 'mtphr-twitter', __('Metaphor Twitter Feed', 'mtphr-widgets'), $widget_ops, $control_ops );
}

/**
 * Display the widget
 *
 * @since 2.0.0
 */
function widget( $args, $instance ) {

	extract( $args );

	// User-selected settings
	$title = $instance['title'];
	$title = apply_filters( 'widget_title', $title );

	$widget_id = ( isset($args['widget_id']) ) ? $args['widget_id'] : -1;
	$twitter_name = apply_filters( 'mtphr_widgets_twitter_name', sanitize_text_field( $instance['twitter_name'] ), $widget_id );
	$widget_limit = apply_filters( 'mtphr_widgets_twitter_limit', intval( $instance['widget_limit'] ), $widget_id );
	$twitter_image = apply_filters( 'mtphr_widgets_twitter_image', isset( $instance['widget_image'] ), $widget_id );
	$twitter_avatar = apply_filters( 'mtphr_widgets_twitter_avatar', isset( $instance['widget_avatar'] ), $widget_id );

	if ( $widget_limit == '' || $widget_limit == 0 ) {
		$widget_limit = 3;
	}

	// Before widget (defined by themes)
	echo $before_widget;

	// Title of widget (before and after defined by themes)
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}

	// Display the twitter feed
	mtphr_twitter_widget_feed( $twitter_name, $widget_limit, $twitter_image, $twitter_avatar );

	// After widget (defined by themes)
	echo $after_widget;
}

/**
 * Update the widget
 *
 * @since 2.0.0
 */
function update( $new_instance, $old_instance ) {

	$instance = $old_instance;

	// Strip tags (if needed) and update the widget settings
	$instance['title'] = sanitize_text_field( $new_instance['title'] );
	$instance['twitter_name'] = sanitize_text_field( strip_tags($new_instance['twitter_name']) );
	$instance['widget_limit'] = intval( $new_instance['widget_limit'] );
	$instance['widget_image'] = $new_instance['widget_image'];
	$instance['widget_avatar'] = $new_instance['widget_avatar'];
	$instance['advanced'] = $new_instance['advanced'];

	return $instance;
}

/**
 * Widget settings
 *
 * @since 2.0.0
 */
function form( $instance ) {

	// Set up some default widget settings
	$defaults = array(
		'title' => __('Recent Tweets', 'mtphr-widgets'),
		'twitter_name' => '',
		'widget_limit' => 3,
		'widget_image' => '',
		'widget_avatar' => '',
		'advanced' => ''
	);

	$instance = wp_parse_args( (array) $instance, $defaults ); ?>

  <!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>

  <!-- Twitter Username: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'twitter_name' ); ?>"><?php _e( 'Twitter Username:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'twitter_name' ); ?>" name="<?php echo $this->get_field_name( 'twitter_name' ); ?>" value="<?php echo $instance['twitter_name']; ?>" style="width:97%;" />
	</p>

  <!-- Widget Limit: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'widget_limit' ); ?>"><?php _e( 'Number of Tweets:', 'mtphr-widgets' ); ?></label><br/>
		<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'widget_limit' ); ?>" name="<?php echo $this->get_field_name( 'widget_limit' ); ?>" value="<?php echo $instance['widget_limit']; ?>" style="width:50px;" />
	</p>

	<!-- Display Widget Image: Checkbox -->
	<p>
		<input class="checkbox" type="checkbox" <?php checked( $instance['widget_image'], 'on' ); ?> id="<?php echo $this->get_field_id( 'widget_image' ); ?>" name="<?php echo $this->get_field_name( 'widget_image' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'widget_image' ); ?>"><?php _e( 'Show Icon?', 'mtphr-widgets' ); ?></label>
		&nbsp;
		<!-- Use Avatar: Checkbox -->
		<input class="checkbox" type="checkbox" <?php checked( $instance['widget_avatar'], 'on' ); ?> id="<?php echo $this->get_field_id( 'widget_avatar' ); ?>" name="<?php echo $this->get_field_name( 'widget_avatar' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'widget_avatar' ); ?>"><?php _e( 'Use Avatar?', 'mtphr-widgets' ); ?></label>
	</p>

	<!-- Advanced: Checkbox -->
	<p class="mtphr-widget-advanced">
		<input class="checkbox" type="checkbox" <?php checked( $instance['advanced'], 'on' ); ?> id="<?php echo $this->get_field_id( 'advanced' ); ?>" name="<?php echo $this->get_field_name( 'advanced' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'advanced' ); ?>"><?php _e( 'Show Advanced Info', 'mtphr-widgets' ); ?></label>
	</p>

	<!-- Widget ID: Text -->
	<p class="mtphr-widget-id">
		<label for="<?php echo $this->get_field_id( 'widget_id' ); ?>"><?php _e( 'Widget ID:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'widget_id' ); ?>" name="<?php echo $this->get_field_name( 'widget_id' ); ?>" value="<?php echo substr( $this->get_field_id(''), 0, -1 ); ?>" style="width:97%;" disabled />
	</p>

	<!-- Shortcode -->
	<span class="mtphr-widget-shortcode">
		<label><?php _e( 'Shortcode:', 'mtphr-widgets' ); ?></label>
		<?php
		$shortcode = '[mtphr_twitter_widget';
		$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
		$shortcode .= ( $instance['twitter_name'] != '' ) ? ' twitter_name="'.$instance['twitter_name'].'"' : '';
		$shortcode .= ( $instance['widget_limit'] != '' ) ? ' limit="'.$instance['widget_limit'].'"' : '';
		$shortcode .= ( $instance['widget_image'] != '' ) ? ' image="'.$instance['widget_image'].'"' : '';
		$shortcode .= ( $instance['widget_avatar'] != '' ) ? ' avatar="'.$instance['widget_avatar'].'"' : '';
		$shortcode .= ']';
		?>
		<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
	</span>

	<?php
}
}




/**
 * Display the feed
 *
 * @since 2.0.7
 */
function mtphr_twitter_widget_feed( $twitter_name, $widget_limit, $twitter_image, $twitter_avatar ) {

	if ( $twitter_name != "" ) {

		// Create variables for the cache file and cache time
		$cachefile = MTPHR_WIDGETS_DIR.'assets/cache/' . $twitter_name . '-twitter-cache';
		$cachetime = 600;

		// if the file exists & the time it was created is less then cache time
		if ( (file_exists($cachefile)) && ( time() - $cachetime < filemtime($cachefile) ) ) {

			// Get the cache file contents & display the feed
			$twitter_feed = file_get_contents( $cachefile );
			mtphr_display_twitter_widget_feed( $twitter_feed, $widget_limit, $twitter_image, $twitter_avatar );

		} else {

			// Turn on output buffering
			ob_start();

			// Save the feed
			$twitter_feed = mtphr_get_twitter_widget_feed( $twitter_name );

			// If errors, use old file
			if ( !$twitter_feed ) {

				if ( (file_exists($cachefile)) ) {

					// Get the cached file
					$twitter_feed = file_get_contents( $cachefile );

					// Resave the feed to reset the cache time
					$fp = fopen( $cachefile, 'w' );
					fwrite( $fp, $feed );
					fclose( $fp );

					mtphr_display_twitter_widget_feed( $twitter_feed, $widget_limit, $twitter_image, $twitter_avatar );
				}

			} else {

				// Create or open the cache file
				$fp = fopen( $cachefile, 'w' );

				// Write the twitter feed to the cache file
				fwrite( $fp, $twitter_feed );

				// Close the file
				fclose( $fp );

				// Display the twitter feed
				mtphr_display_twitter_widget_feed( $twitter_feed, $widget_limit, $twitter_image, $twitter_avatar );
			}

			// End and close the output buffer
			ob_end_flush();
		}
	}
}

/**
 * Use curl to get the feed
 *
 * @since 2.0.7
 */
function mtphr_get_twitter_widget_feed( $twitter_name ) {

	$access = get_option('mtphr_widgets_twitter_access', array());

	if( isset($access['oauth_token']) ) {

		$tmhOAuth = new tmhOAuth(array(
		  'consumer_key'    => 'KEEIyPyhpjNBrnYCjwDoNg',
		  'consumer_secret' => '2jRa8Z5jWUnN8cDaiawTa6SPXZzWLkQJNWmL2z7ohc',
		  'user_token'      => $access['oauth_token'],
		  'user_secret'     => $access['oauth_token_secret'],
		));

		$args = array(
			'screen_name' => $twitter_name,
		  'count' => 200,
		  'include_rts' => true
		);
		$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), $args);
		$response = $tmhOAuth->response;

		if ($code == 200) {
			return $tmhOAuth->response['response'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * Display the feed on th site
 *
 * @since 2.0.9
 */
function mtphr_display_twitter_widget_feed( $twitter_feed, $widget_limit, $twitter_image, $twitter_avatar ) {

	// Store the data in a variable
	$output = "<ul>";

	// If the feed is not empty
	if( !empty($twitter_feed) ) {

		// Save the data as json data
		$json_data = json_decode( $twitter_feed, true );

		// Create a limit variable
		$limit = sizeof( $json_data );
		if ( $widget_limit < $limit ) {
			$limit = $widget_limit;
		}

		if ( isset($json_data['error']) ) {
			$output .= '<li>'.$json_data['error'].'</li>';
		} elseif ( isset($json_data['errors']) ) {
			foreach( $json_data['errors'] as $error ) {
				$output .= '<li>'.__('Error','mtphr-widgets').' '.$error['code'].': '.$error['message'].'</li>';
			}
		} else {

			// Loop through the tweets
			for ( $i=0; $i < $limit; $i++ ) {

				$twitter_name = $json_data[$i]['user']['screen_name'];
				$twitter_user_avatar = $json_data[$i]['user']['profile_image_url'];
				$twitter_text = $json_data[$i]['text'];
				$twitter_date = $json_data[$i]['created_at'];
				$twitter_id = $json_data[$i]['id_str'];


				if( $twitter_image || $twitter_avatar ) {
					$output .= '<li class="show-image">';
					$output .= '<span class="mtphr-twitter-widget-image">';
					if( $twitter_avatar ) {
						$output .= '<img src="'.$twitter_user_avatar.'" alt="'.$twitter_name.'" />';
					}
					$output .= '</span>';
				} else {
					$output .= '<li>';
				}
				$output .= '"'.mtphr_widgets_twitter_links( $twitter_text ).'"<span class="mtphr-twitter-widget-date">'.human_time_diff( strtotime($twitter_date), current_time('timestamp') ).' '.__('ago','mtphr-widgets').'</span>';
				$output .= '</li>';
			}
		}
	} else {
		$output .= '<li><p>'.__('Sorry, but there was a problem connecting to the API.','mtphr-widgets').'</p></li>';
	}
	$output .= '</ul>';

	echo $output;
}