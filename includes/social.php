<?php

/* Register the widget - @since 1.0 */
add_action( 'widgets_init', 'mtphr_social_widget_init' );

/**
 * Register the widget
 *
 * @since 1.0
 */
function mtphr_social_widget_init() {
	register_widget( 'mtphr_social_widget' );
}




/**
 * Create a class for the widget
 *
 * @since 1.0
 */
class mtphr_social_widget extends WP_Widget {

/**
 * Widget setup
 *
 * @since 2.0.0
 */
function mtphr_social_widget() {

	// Widget settings
	$widget_ops = array(
		'classname' => 'mtphr-social-widget',
		'description' => __('Displays your social links.', 'mtphr-widgets')
	);

	// Widget control settings
	$control_ops = array(
		'id_base' => 'mtphr-social',
		'width' => 400
	);

	// Create the widget
	$this->WP_Widget( 'mtphr-social', __('Metaphor Social Links', 'mtphr-widgets'), $widget_ops, $control_ops );
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
	if( !isset($instance['sites']) ) {
		$instance = mtphr_widgets_social_update($instance);
		$instance['new_tab'] = false;
	}
	$sites = apply_filters( 'mtphr_widgets_social_sites', $instance['sites'], $widget_id );
	$new_tab = apply_filters( 'mtphr_widgets_social_new_tab', $instance['new_tab'], $widget_id );

	// Before widget (defined by themes)
	echo $before_widget;

	// Title of widget (before and after defined by themes)
	if ( $title ) {
		echo $before_title . $title . $after_title;
	}

	// If there is at least one site
	if( isset($sites[0]) ) {

		$t = ( $new_tab ) ? ' target="_blank"' : '';
		echo '<div class="mtphr-social-widget-links clearfix">';

		foreach( $sites as $site ) {
			echo '<a class="mtphr-social-widget-site" href="'.esc_url($site['link']).'"'.$t.'><i class="mtphr-socon-'.$site['site'].'"></i></a>';
		}

		echo '</div>';
	}

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

	// Loop through the sites and esc_urls
	$sites = array();
	foreach( $new_instance['sites'] as $site ) {
		$group = array(
			'site' => $site['site'],
			'link' => esc_url( $site['link'] )
		);
		$sites[] = $group;
	}
	$instance['sites'] = $sites;
	$instance['new_tab'] = $new_instance['new_tab'];
	$instance['advanced'] = $new_instance['advanced'];

	// No longer supported
	$instance['twitter_name'] = sanitize_text_field( strip_tags($new_instance['twitter_name']) );
	$instance['twitter_link'] = esc_url( $new_instance['twitter_link'] );
	$instance['facebook_link'] = esc_url( $new_instance['facebook_link'] );
	$instance['google_link'] = esc_url( $new_instance['google_link'] );
	$instance['linkedin_link'] = esc_url( $new_instance['linkedin_link'] );

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
		'title' => __('Get Social', 'mtphr-widgets'),
		'sites' => '',
		'new_tab' => true,
		'advanced' => ''
	);

	$instance = wp_parse_args( (array) $instance, $defaults );
	$instance = mtphr_widgets_social_update( $instance ); ?>

  <!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
	</p>

	<?php
	// Create the link structure
	$link_structure = array(
		'site' => array(
			'header' => __('Website', 'mtphr-widgets'),
			'width' => '10%',
			'type' => 'select',
			'options' => mtphr_widgets_social_sites()
		),
		'link' => array(
			'header' => __('Link', 'mtphr-widgets'),
			'type' => 'text'
		)
	);

	// Set the widget fields
	$fields = array(
		'sites' => array(
			'id' => $this->get_field_name( 'sites' ),
			'type' => 'list',
			'structure' =>  $link_structure,
			'widget_value' => $instance['sites']
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

	<!-- New window: Checkbox -->
	<p>
		<input class="checkbox" type="checkbox" <?php checked( $instance['new_tab'], 'on' ); ?> id="<?php echo $this->get_field_id( 'new_tab' ); ?>" name="<?php echo $this->get_field_name( 'new_tab' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'new_tab' ); ?>"><?php _e( 'Open links in a new window/tab', 'mtphr-widgets' ); ?></label>
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
		$shortcode = '[mtphr_social_widget';
		$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
		$shortcode .= ( $instance['new_tab'] == false ) ? ' new_tab="false"' : '';
		if( isset($instance['sites'][0]) ) {
			$shortcode .= ' sites="';
			$sites = '';
			foreach( $instance['sites'] as $site ) {
				$sites .= $site['site'].'***'.esc_url($site['link']).':::';
			}
			$sites = substr( $sites, 0, -3 );
			$shortcode .= $sites.'"';
		}
		$shortcode .= ']';
		?>
		<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
	</span>
	<?php
}
}
