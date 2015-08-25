<?php

/**
 * Create a class for the widget
 *
 * @since 2.2.1
 */
class mtphr_twitter_widget extends WP_Widget {
	
	/** Constructor */
	function __construct() {
		parent::__construct(
			'mtphr-twitter',
			__('Metaphor Twitter Feed', 'mtphr-widgets'),
			array(
				'classname' => 'mtphr-twitter-widget',
				'description' => __('Displays a users latest twitter comments.', 'mtphr-widgets')
			) 
		);
	}
		
	/** @see WP_Widget::widget */
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
		$new_window = ( !isset($instance['new_window_default']) ) ? 'on' : isset( $instance['new_window'] );
	
		if ( $widget_limit == '' || $widget_limit == 0 ) {
			$widget_limit = 3;
		}
	
		// Before widget (defined by themes)
		echo $before_widget;
	
		// Title of widget (before and after defined by themes)
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
	
		if( !mtphr_widgets_check_twitter_access() ) {
			echo __( 'Twitter feed must be authorized.', 'mtphr-widgets' );
		} else {
			// Display the twitter feed
			mtphr_twitter_widget_feed( $twitter_name, $widget_limit, $twitter_image, $twitter_avatar, $new_window );
		}
	
		// After widget (defined by themes)
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
	
		// Strip tags (if needed) and update the widget settings
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['twitter_name'] = sanitize_text_field( strip_tags($new_instance['twitter_name']) );
		$instance['widget_limit'] = intval( $new_instance['widget_limit'] );
		$instance['widget_image'] = $new_instance['widget_image'];
		$instance['widget_avatar'] = $new_instance['widget_avatar'];
		$instance['new_window'] = $new_instance['new_window'];
		$instance['new_window_default'] = $new_instance['new_window_default'];
		$instance['advanced'] = $new_instance['advanced'];
	
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Recent Tweets', 'mtphr-widgets'),
			'twitter_name' => '',
			'widget_limit' => 3,
			'widget_image' => '',
			'widget_avatar' => '',
			'new_window' => '',
			'new_window_default' => '',
			'advanced' => ''
		);
	
		$instance = wp_parse_args( (array) $instance, $defaults );
		$instance['new_window'] = ( $instance['new_window_default'] == '' ) ? 'on' : $instance['new_window'];
	
		if( !mtphr_widgets_check_twitter_access() ) {
			$link = admin_url().'plugins.php?page=mtphr_widgets_twitter_settings'; ?>
	
	    <div>
	       <p><?php _e('You must authorize <strong>Metaphor Widgets</strong> access through Twitter before you can display any feeds.', 'mtphr-widgets'); ?><br/><br/><?php printf( __('<a href="%s"><strong>Click here</strong></a> for instructions on creating an app and granting acces to <strong>Metaphor Widgets</strong>.', 'mtphr-widgets'), $link ); ?></p>
	    </div>
	
	  <?php } else { ?>
	
		  <!-- Widget Title: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
			</p>
	
		  <!-- Twitter Username: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'twitter_name' ); ?>"><?php _e( 'Twitter Username:', 'mtphr-widgets' ); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'twitter_name' ); ?>" name="<?php echo $this->get_field_name( 'twitter_name' ); ?>" value="<?php echo $instance['twitter_name']; ?>" style="width:97%;" />
			</p>
	
		  <!-- Widget Limit: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'widget_limit' ); ?>"><?php _e( 'Number of Tweets:', 'mtphr-widgets' ); ?></label><br/>
				<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'widget_limit' ); ?>" name="<?php echo $this->get_field_name( 'widget_limit' ); ?>" value="<?php echo $instance['widget_limit']; ?>" style="width:50px;" />
	
				<label style="margin-left:10px;"><input class="checkbox" type="checkbox" value="on" <?php checked( $instance['new_window'], 'on' ); ?> name="<?php echo $this->get_field_name( 'new_window' ); ?>" /> <?php _e('Open links in window/tab', 'mtphr-widgets'); ?></label>
				<input type="hidden" value="on" name="<?php echo $this->get_field_name( 'new_window_default' ); ?>" />
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
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'widget_id' ); ?>" name="<?php echo $this->get_field_name( 'widget_id' ); ?>" value="<?php echo substr( $this->get_field_id(''), 0, -1 ); ?>" style="width:97%;" disabled />
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
	
		<?php } // End else statement
	}
}




/**
 * Display the feed
 *
 * @since 2.1.15
 */
function mtphr_twitter_widget_feed( $twitter_name, $widget_limit, $twitter_image, $twitter_avatar, $new_window ) {

	if ( $twitter_name != "" ) {

		$twitter_feed = get_transient('mtphr_twitter_'.$twitter_name);
		
		if( false === $twitter_feed ) {
			
			$settings = mtphr_widgets_twitter_settings();
			$twitter_feed = mtphr_widgets_twitter_user_timeline( $twitter_name );

			set_transient('mtphr_twitter_'.$twitter_name, $twitter_feed, MINUTE_IN_SECONDS * $settings['cache_time']);
		}
		
		// Display the twitter feed
		mtphr_display_twitter_widget_feed( $twitter_feed, $widget_limit, $twitter_image, $twitter_avatar, $new_window );
	}
}

/**
 * Display the feed on th site
 *
 * @since 2.1.8
 */
function mtphr_display_twitter_widget_feed( $twitter_feed, $widget_limit, $twitter_image, $twitter_avatar, $new_window ) {

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
					} else {
						$output .= '<i class="metaphor-widgets-ico-twitter"></i>';
					}
					$output .= '</span>';
				} else {
					$output .= '<li>';
				}
				$output .= '"'.mtphr_widgets_twitter_links( $twitter_text, $new_window ).'"<span class="mtphr-twitter-widget-date">'.human_time_diff( strtotime($twitter_date), current_time('timestamp', 1) ).' '.__('ago','mtphr-widgets').'</span>';
				$output .= '</li>';
			}
		}
	} else {
		$output .= '<li><p>'.__('Sorry, but there was a problem connecting to the API.','mtphr-widgets').'</p></li>';
	}
	$output .= '</ul>';

	echo $output;
}


/* --------------------------------------------------------- */
/* !Register the widget - 2.2 */
/* --------------------------------------------------------- */

function mtphr_twitter_widget_init() {
	register_widget( 'mtphr_twitter_widget' );
}
add_action( 'widgets_init', 'mtphr_twitter_widget_init' );