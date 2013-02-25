<?php

/* Register the widget - @since 1.0 */
add_action( 'widgets_init', 'mtphr_post_navigation_init' );

/**
 * Register the widget
 *
 * @since 1.0.0
 */
function mtphr_post_navigation_init() {
	register_widget( 'mtphr_post_navigation' );
}




/**
 * Create a class for the widget
 *
 * @since 2.0.0
 */
class mtphr_post_navigation extends WP_Widget {
	
/**
 * Widget setup
 *
 * @since 2.0.0
 */
function mtphr_post_navigation() {
	
	// Widget settings
	$widget_ops = array(
		'classname' => 'mtphr-post-navigation',
		'description' => __('Displays previous, next, and archive links for single posts.', 'mtphr-widgets')
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mtphr-post-navigation'
	);

	// Create the widget
	$this->WP_Widget( 'mtphr-post-navigation', __('Metaphor Post Navigation', 'mtphr-widgets'), $widget_ops, $control_ops );
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
	$home = apply_filters( 'mtphr_widgets_navigation_home', $instance['home'], $widget_id );
	$home_link = apply_filters( 'mtphr_widgets_navigation_home_link', $instance['home_link'], $widget_id );
	$previous = apply_filters( 'mtphr_widgets_navigation_previous', $instance['previous'], $widget_id );
	$next = apply_filters( 'mtphr_widgets_navigation_next', $instance['next'], $widget_id );
	
	// Before widget (defined by themes)
	echo $before_widget;
	
	// Title of widget (before and after defined by themes)
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}
	
	if( is_single() ) {
		
		// Setup the home link
		$obj = get_post_type_object( get_post_type() );
		$home = preg_replace('/{type}/s', $obj->labels->name, $home);
		$home_link = ( $home_link != '' ) ? esc_url($home_link) : get_post_type_archive_link( get_post_type() );
		
		// Get the previous post
		$prev_post = get_previous_post();
		if( empty( $prev_post ) ) {
			$p = get_posts( array(
		    'post_type' => get_post_type(),
		    'numberposts' => 1,
		    'order' => 'DESC'
			));
			$prev_post = $p[0];
		}
		
		// Get the next post
		$next_post = get_next_post();
		if( empty( $next_post ) ) {
			$p = get_posts( array(
		    'post_type' => get_post_type(),
		    'numberposts' => 1,
		    'order' => 'ASC'
			));
			$next_post = $p[0];
		}
		?>
		
		<nav>
			<ul>
				<?php if( $home != '' ) { ?><li class="mtphr-post-navigation-home"><a href="<?php echo $home_link; ?>"><?php echo $home; ?></a></li><?php } ?>
				<?php if( $previous != '' ) { ?><li class="mtphr-post-navigation-previous"><a href="<?php echo get_permalink($prev_post->ID); ?>"><?php echo $previous; ?></a></li><?php } ?>
				<?php if( $next != '' ) { ?><li class="mtphr-post-navigation-next"><a href="<?php echo get_permalink($next_post->ID); ?>"><?php echo $next; ?></a></li><?php } ?>
			</ul>
		</nav>
	
	<?php } ?>
	
	<?php
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
	$instance['home'] = sanitize_text_field( $new_instance['home'] );
	$instance['home_link'] = esc_url( $new_instance['home_link'] );
	$instance['previous'] = sanitize_text_field( $new_instance['previous'] );
	$instance['next'] = sanitize_text_field( $new_instance['next'] );

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
		'title' => '',
		'home' => '{type}'.__(' home', 'mtphr-widgets'),
		'home_link' => '',
		'previous' => __('Previous', 'mtphr-widgets'),
		'next' => __('Next', 'mtphr-widgets'),
		'advanced' => ''
	);
	
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	
  <!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>
	
	<!--Home Text: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'home' ); ?>"><?php _e( 'Home link text:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'home' ); ?>" name="<?php echo $this->get_field_name( 'home' ); ?>" value="<?php echo $instance['home']; ?>" style="width:97%;" />
	</p>
	
	<!--Home Link: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'home_link' ); ?>"><?php _e( 'Custom home link:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'home_link' ); ?>" name="<?php echo $this->get_field_name( 'home_link' ); ?>" value="<?php echo $instance['home_link']; ?>" style="width:97%;" />
	</p>
	
	<!-- Previous Text: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'previous' ); ?>"><?php _e( 'Previous link text:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'previous' ); ?>" name="<?php echo $this->get_field_name( 'previous' ); ?>" value="<?php echo $instance['previous']; ?>" style="width:97%;" />
	</p>
	
	<!-- Next Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'next' ); ?>"><?php _e( 'Next link text:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'next' ); ?>" name="<?php echo $this->get_field_name( 'next' ); ?>" value="<?php echo $instance['next']; ?>" style="width:97%;" />
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
		$shortcode = '[mtphr_post_navigation';
		$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
		$shortcode .= ( $instance['home'] != '' ) ? ' home="'.$instance['home'].'"' : '';
		$shortcode .= ( $instance['previous'] != '' ) ? ' previous="'.$instance['previous'].'"' : '';
		$shortcode .= ( $instance['next'] != '' ) ? ' next="'.$instance['next'].'"' : '';
		$shortcode .= ']';
		?>
		<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
	</span>
	<?php
}
}
