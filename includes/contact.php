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
 * @since 2.1.9
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
				echo '<td class="mtphr-contact-widget-title">'.nl2br(wp_kses_post($info['title'])).'</td>';
				echo '<td>'.make_clickable(nl2br(wp_kses_post($info['description']))).'</td>';
			} else {
				echo '<td colspan="2">'.make_clickable(nl2br(wp_kses_post($info['description']))).'</td>';
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
 * @since 2.1.9
 */
function update( $new_instance, $old_instance ) {

	$instance = $old_instance;

	// Strip tags (if needed) and update the widget settings
	$instance['title'] = sanitize_text_field( $new_instance['title'] );
	$instance['contact_info'] = $new_instance['contact_info'];
	$instance['advanced'] = $new_instance['advanced'];

	return $instance;
}

/**
 * Widget settings
 *
 * @since 2.1.9
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
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>
	
	<?php echo metaphor_widgets_contact_setup( $this->get_field_name('contact_info'), $instance['contact_info'] ); ?>

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



/* --------------------------------------------------------- */
/* !Render the contact info setup - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_contact_setup') ) {
function metaphor_widgets_contact_setup( $name, $data ) {
	
	$html = '';
	$html .= '<table class="mtphr-widgets-list mtphr-widgets-default-list">';
		$html .= '<tr>';
			$html .= '<th class="mtphr-widgets-list-handle"></th>';
			$html .= '<th>'.__('Title', 'mtphr-widgets').'</th>';
			$html .= '<th>'.__('Description', 'mtphr-widgets').'</th>';
			$html .= '<th class="mtphr-widgets-list-delete"></th>';
			$html .= '<th class="mtphr-widgets-list-add"></th>';
		$html .= '</tr>';
		if( is_array($data) && count($data) > 0 ) {
			foreach( $data as $i=>$d ) {
				$html .= metaphor_widgets_contact_row( $name, $d );
			}
		} else {
			$html .= metaphor_widgets_contact_row( $name );
		}
	$html .= '</table>';
	
	return $html;
}
}


/* --------------------------------------------------------- */
/* !Render a contact row - 2.1.9 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_contact_row') ) {
function metaphor_widgets_contact_row( $name, $data=false ) {
	
	$title = ( isset($data) && isset($data['title']) ) ? $data['title'] : '';
	$description = ( isset($data) && isset($data['description']) ) ? $data['description'] : '';
	
	$html = '';
	$html .= '<tr class="mtphr-widgets-list-item">';
		$html .= '<td class="mtphr-widgets-list-handle"><span></span></td>';
		$html .= '<td class="mtphr-widgets-contact-title">';
			$html .= '<input type="text" name="'.$name.'[title]" data-prefix="'.$name.'" data-key="title" value="'.$title.'" size="8" />';
		$html .= '</td>';
		$html .= '<td class="mtphr-widgets-contact-description">';
			$html .= '<textarea name="'.$name.'[description]" data-prefix="'.$name.'" data-key="description" rows="1">'.$description.'</textarea>';
		$html .= '</td>';
		$html .= '<td class="mtphr-widgets-list-delete"><a href="#"></a></td>';
		$html .= '<td class="mtphr-widgets-list-add"><a href="#"></a></td>';
	$html .= '</tr>';
	
	return $html;
}
}
