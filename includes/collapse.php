<?php

/* Register the widget - @since 2.0.3 */
add_action( 'widgets_init', 'mtphr_collapse_widget_init' );

/**
 * Register the widget
 *
 * @since 2.0.3
 */
function mtphr_collapse_widget_init() {
	register_widget( 'mtphr_collapse_widget' );
}




/**
 * Create a class for the widget
 *
 * @since 2.0.3
 */
class mtphr_collapse_widget extends WP_Widget {
	
/**
 * Widget setup
 *
 * @since 2.0.3
 */
function mtphr_collapse_widget() {
	
	// Widget settings
	$widget_ops = array(
		'classname' => 'mtphr-collapse-widget',
		'description' => __('Displays collapsible content.', 'mtphr-widgets')
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mtphr-collapse',
		'width' => 400
	);

	// Create the widget
	$this->WP_Widget( 'mtphr-collapse', __('Metaphor Collapse', 'mtphr-widgets'), $widget_ops, $control_ops );
}

/**
 * Display the widget
 *
 * @since 2.0.3
 */
function widget( $args, $instance ) {
	
	extract( $args );

	// User-selected settings	
	$title = $instance['title'];
	$title = apply_filters( 'widget_title', $title );
	
	$widget_id = ( isset($args['widget_id']) ) ? $args['widget_id'] : -1;
	$collapse_info = apply_filters( 'mtphr_widgets_collapse_info', $instance['collapse_info'], $widget_id );
	
	// Before widget (defined by themes)
	echo $before_widget;
	
	// Title of widget (before and after defined by themes)
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}
	
	if( isset($collapse_info[0]) ) {
		foreach( $collapse_info as $info ) {
			if( $info['title'] != '' ) {
				?>
					<div class="mtphr-collapse-widget-block">
					<p class="mtphr-collapse-widget-heading"><a href="#"><span class="mtphr-collapse-widget-toggle"></span><?php echo sanitize_text_field($info['title']); ?></a></p>
					<p class="mtphr-collapse-widget-description"><?php echo make_clickable(nl2br(wp_kses_post($info['description']))); ?></p></div>
				<?php
			}	
		}
	}
	
	// After widget (defined by themes)
	echo $after_widget;
}

/**
 * Update the widget
 *
 * @since 2.0.3
 */
function update( $new_instance, $old_instance ) {
	
	$instance = $old_instance;

	// Strip tags (if needed) and update the widget settings
	$instance['title'] = sanitize_text_field( $new_instance['title'] );
	$instance['collapse_info'] = $new_instance['collapse_info'];
	$instance['advanced'] = $new_instance['advanced'];

	return $instance;
}

/**
 * Widget settings
 *
 * @since 2.0.3
 */
function form( $instance ) {

	// Set up some default widget settings
	$defaults = array(
		'title' => __('Information', 'mtphr-widgets'),
		'collapse_info' => array(
			array(
				'title' => __('Add a heading here...', 'mtphr-widgets'),
				'description' => __('Add a description here...', 'mtphr-widgets')
			)
		),
		'advanced' => ''
	);
	
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	
  <!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>

	<?php
	// Create the collapse structure	
	$collapse_structure = array(
		'title' => array(
			'header' => __('Heading', 'mtphr-widgets'),
			'width' => '30%',
			'type' => 'text'
		),
		'description' => array(
			'header' => __('Description', 'mtphr-widgets'),
			'type' => 'textarea',
			'rows' => 1
		)
	);
	
	// Set the widget fields
	$fields = array(
		'collapse_info' => array(
			'id' => $this->get_field_name( 'collapse_info' ),
			'type' => 'list',
			'structure' =>  $collapse_structure,
			'widget_value' => $instance['collapse_info']
		)
	);
	
	foreach( $fields as $name => $field ) {
	
		echo '<span class="mtphr-widgets-metaboxer-field mtphr-widgets-metaboxer-widget-field mtphr-widgets-metaboxer-'.$field['type'].' mtphr-widgets-metaboxer-'.$name.'">';
		if( isset($field['name']) ) {
			echo '<label>'.$field['name'].'</label>';
		}
		// Call the function to display the field
		if ( function_exists('mtphr_widgets_metaboxer_'.$field['type']) ) {
			call_user_func( 'mtphr_widgets_metaboxer_'.$field['type'], $field, $field['widget_value'] );
		}
		echo '</span>';
	}
	?>
	
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
		$shortcode = '[mtphr_collapse_widget';
		$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
		$shortcode .= ']';
		if( isset($instance['collapse_info'][0]) ) {
			$all_info = '';
			foreach( $instance['collapse_info'] as $info ) {
				$all_info .= sanitize_text_field($info['title']).'***'.esc_attr(nl2br($info['description'])).':::';
			}
			$all_info = substr( $all_info, 0, -3 );
			$shortcode .= $all_info.'"';
		}
		$shortcode .= '[/mtphr_collapse_widget]';
		?>
		<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
	</span>
  	
	<?php
}
}
