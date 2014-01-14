<?php

/* Register the widget - @since 2.0.9 */
add_action( 'widgets_init', 'mtphr_tabbed_posts_widget_init' );

/**
 * Register the widget
 *
 * @since 2.0.9
 */
function mtphr_tabbed_posts_widget_init() {
	register_widget( 'mtphr_tabbed_posts_widget' );
}




/**
 * Create a class for the widget
 *
 * @since 2.0.9
 */
class mtphr_tabbed_posts_widget extends WP_Widget {

/**
 * Widget setup
 *
 * @since 2.0.9
 */
function mtphr_tabbed_posts_widget() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'mtphr-tabbed-posts-widget',
		'description' => __('Displays recent posts.', 'mtphr-widgets')
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mtphr-tabbed-posts'
	);

	// Create the widget
	$this->WP_Widget( 'mtphr-tabbed-posts', __('Metaphor Tabbed Posts', 'mtphr-widgets'), $widget_ops, $control_ops );
}

/**
 * Display the widget
 *
 * @since 2.0.9
 */
function widget( $args, $instance ) {

	extract( $args );

	// User-selected settings
	$title = $instance['title'];
	$title = apply_filters( 'widget_title', $title );

	$widget_id = ( isset($args['widget_id']) ) ? $args['widget_id'] : -1;
	$widget_limit = apply_filters( 'mtphr_widgets_post_limit', intval( $instance['widget_limit'] ), $widget_id );
	$excerpt_length = apply_filters( 'mtphr_widgets_post_excerpt_length', intval( $instance['excerpt_length'] ), $widget_id );

	if ( $widget_limit == 0 ) {
		$widget_limit = 3;
	}
	if ( $excerpt_length == 0 ) {
		$excerpt_length = 60;
	}

	// Before widget (defined by themes)
	echo $before_widget;

	// Title of widget (before and after defined by themes)
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}

	global $wp_query;
	$original_query = $wp_query;
	$popular_posts = '';
	$recent_posts = '';

	// Get the popular posts
	$args = array(
		'post_type'=> 'post',
		'posts_per_page' => $widget_limit,
		'orderby' => 'comment_count'
	);
	$wp_query = null;
	$wp_query = new WP_Query();
	$wp_query->query( $args );

	if ( have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post();
		$popular_posts .= '<li>';
		$popular_posts .= '<a class="mtphr-tabbed-posts-widget-title" href="'.get_permalink().'">'.get_the_title().'</a> ';
		$popular_posts .= '<small class="mtphr-tabbed-posts-widget-date">'.get_the_time( get_option('date_format') ).'</small>';
		$popular_posts .= mtphr_widgets_post_excerpt( $excerpt_length );
		$popular_posts .= '</li>';
	endwhile;
	else :
	endif;

	wp_reset_postdata();

	// Get the recent posts
	$args = array(
		'post_type'=> 'post',
		'posts_per_page' => $widget_limit,
		'orderby' => 'date'
	);
	$wp_query = null;
	$wp_query = new WP_Query();
	$wp_query->query( $args );

	if ( have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post();
		$recent_posts .= '<li>';
		$recent_posts .= '<a class="mtphr-tabbed-posts-widget-title" href="'.get_permalink().'">'.get_the_title().'</a> ';
		$recent_posts .= '<span class="mtphr-tabbed-posts-widget-date">'.get_the_time( get_option('date_format') ).'</span>';
		$recent_posts .= mtphr_widgets_post_excerpt( $excerpt_length );
		$recent_posts .= '</li>';
	endwhile;
	else :
	endif;

	$wp_query = null;
	$wp_query = $original_query;
	wp_reset_postdata();

	?>
	<table>
		<tr>
			<td class="mtphr-tabbed-posts-link"><a href="#0" rel="nofollow"><?php _e('Popular', 'mtphr-widgets'); ?></a></td>
			<td class="mtphr-tabbed-posts-link"><a href="#1" rel="nofollow"><?php _e('Recent', 'mtphr-widgets'); ?></a></td>
		</tr>
		<tr>
			<td class="mtphr-tabbed-posts-content-container" colspan="2">
				<div class="mtphr-tabbed-posts-content">
					<div class="mtphr-tabbed-posts-content-wrapper">
						<ul><?php echo $popular_posts; ?></ul>
					</div>
				</div>
				<div class="mtphr-tabbed-posts-content">
					<div class="mtphr-tabbed-posts-content-wrapper">
						<ul><?php echo $recent_posts; ?></ul>
					</div>
				</div>
			</td>
		</tr>
	</table>

	<?php
	// After widget (defined by themes)
	echo $after_widget;
}

/**
 * Update the widget
 *
 * @since 2.0.9
 */
function update( $new_instance, $old_instance ) {

	$instance = $old_instance;

	// Strip tags (if needed) and update the widget settings
	$instance['title'] = sanitize_text_field( $new_instance['title'] );
	$instance['widget_limit'] = intval( $new_instance['widget_limit'] );
	$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
	$instance['date_format'] = sanitize_text_field( $new_instance['date_format'] );
	$instance['advanced'] = $new_instance['advanced'];

	return $instance;
}

/**
 * Widget settings
 *
 * @since 2.0.9
 */
function form( $instance ) {

	// Set up some default widget settings
	$defaults = array(
		'title' => __('Blog Posts', 'mtphr-widgets'),
		'widget_limit' => 3,
		'excerpt_length' => 60,
		'date_format' => get_option('date_format'),
		'advanced' => ''
	);

	$instance = wp_parse_args( (array) $instance, $defaults ); ?>

  <!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>

  <!-- Widget Limit: Number Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'widget_limit' ); ?>"><?php _e( 'Number of Posts:', 'mtphr-widgets' ); ?></label><br/>
		<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'widget_limit' ); ?>" name="<?php echo $this->get_field_name( 'widget_limit' ); ?>" value="<?php echo $instance['widget_limit']; ?>" style="width:50px;" />
	</p>

	<!-- Excerpt Length: Number Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length:', 'mtphr-widgets' ); ?></label><br/>
		<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo $instance['excerpt_length']; ?>" style="width:50px;" />
	</p>

	<!-- Date Formate: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'date_format' ); ?>"><?php _e( 'Date Format:', 'mtphr-widgets' ); ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'date_format' ); ?>" name="<?php echo $this->get_field_name( 'date_format' ); ?>" value="<?php echo $instance['date_format']; ?>" style="width:97%;" />
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
		$shortcode = '[mtphr_tabbed_posts_widget';
		$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
		$shortcode .= ( $instance['widget_limit'] != '' ) ? ' limit="'.$instance['widget_limit'].'"' : '';
		$shortcode .= ( $instance['excerpt_length'] != '' ) ? ' excerpt_length="'.$instance['excerpt_length'].'"' : '';
		$shortcode .= ( $instance['date_format'] != '' ) ? ' date_format="'.$instance['date_format'].'"' : '';
		$shortcode .= ']';
		?>
		<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
	</span>

	<?php
}
}

