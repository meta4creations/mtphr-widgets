<?php

/* Register the widget - @since 2.0.0 */
add_action( 'widgets_init', 'mtphr_contact_widget_init' );

/**
 * Register the widget
 *
 * @since 2.0.0
 */
function mtphr_contact_widget_init() {
	register_widget( 'mtphr_contact_widget' );
}




/**
 * Create a class for the widget
 *
 * @since 2.0.0
 */
class mtphr_contact_widget extends WP_Widget {

/**
 * Widget setup
 *
 * @since 2.0.0
 */
function mtphr_contact_widget() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'mtphr-contact-widget',
		'description' => __('Displays contact information.', 'mtphr-widgets')
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mtphr-contact',
		'width' => 400
	);

	// Create the widget
	$this->WP_Widget( 'mtphr-contact', __('Metaphor Contact', 'mtphr-widgets'), $widget_ops, $control_ops );
}

/**
 * Display the widget
 *
 * @since 2.1.2
 */
function widget( $args, $instance ) {

	extract( $args );

	// User-selected settings
	$title = $instance['title'];
	$title = apply_filters( 'widget_title', $title );

	$widget_id = ( isset($args['widget_id']) ) ? $args['widget_id'] : -1;

	// Populate with old info
	if( !isset($instance['contact_info']) ) {
		$instance = mtphr_widgets_contact_update($instance);
	}
	$contact_info = apply_filters( 'mtphr_widgets_contact_info', $instance['contact_info'], $widget_id );

	// Before widget (defined by themes)
	echo $before_widget;

	// Title of widget (before and after defined by themes)
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}

	echo '<table>';

	if( is_array($contact_info) ) {
		foreach( $contact_info as $info ) {

			echo '<tr class="mtphr-contact-widget-info">';
			if( $info['title'] != '' ) {
				echo '<td class="mtphr-contact-widget-title"><p>'.nl2br(wp_kses_post($info['title'])).'</p></td>';
				echo '<td><p>'.make_clickable(nl2br(wp_kses_post($info['description']))).'</p></td>';
			} else {
				echo '<td colspan="2"><p>'.make_clickable(nl2br(wp_kses_post($info['description']))).'</p></td>';
			}
			echo '</tr>';

		}
	}

	echo '</table>';

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
	$instance['contact_info'] = $new_instance['contact_info'];
	$instance['advanced'] = $new_instance['advanced'];

	// No longer supported
	$instance['email'] = sanitize_email( $new_instance['email'] );
	$instance['telephone'] = sanitize_text_field( $new_instance['telephone'] );
	$instance['fax'] = sanitize_text_field( $new_instance['fax'] );
	$instance['address'] = wp_kses_post( $new_instance['address'] );

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
		'title' => __('Contact', 'mtphr-widgets'),
		'contact_info' => array(
			'email' => array(
				'title' => __('Email', 'mtphr-widgets'),
				'description' => '',
			),
			'telephone' => array(
				'title' => __('Tel', 'mtphr-widgets'),
				'description' => '',
			),
			'fax' => array(
				'title' => __('Fax', 'mtphr-widgets'),
				'description' => '',
			),
			'address' => array(
				'title' => __('', 'mtphr-widgets'),
				'description' => __('Add your address here...', 'mtphr-widgets'),
			)
		),
		'advanced' => ''
	);

	$instance = wp_parse_args( (array) $instance, $defaults );
	$instance = mtphr_widgets_contact_update( $instance ); ?>

  <!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>

	<?php
	// Create the contact structure
	$contact_structure = array(
		'title' => array(
			'header' => __('Title', 'mtphr-widgets'),
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
		'contact_info' => array(
			'id' => $this->get_field_name( 'contact_info' ),
			'type' => 'list',
			'structure' =>  $contact_structure,
			'widget_value' => $instance['contact_info']
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
		$shortcode = '[mtphr_contact_widget';
		$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
		$shortcode .= ']';
		if( isset($instance['contact_info'][0]) ) {
			$all_info = '';
			foreach( $instance['contact_info'] as $info ) {
				$all_info .= esc_attr(nl2br($info['title'])).'***'.esc_attr(nl2br($info['description'])).':::';
			}
			$all_info = substr( $all_info, 0, -3 );
			$shortcode .= $all_info;
		}
		$shortcode .= '[/mtphr_contact_widget]';
		?>
		<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
	</span>

	<?php
}
}
