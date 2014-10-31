<?php

/* --------------------------------------------------------- */
/* !Register the widget - 1.0.0 */
/* --------------------------------------------------------- */

function mtphr_social_widget_init() {
	register_widget( 'mtphr_social_widget' );
}
add_action( 'widgets_init', 'mtphr_social_widget_init' );


 
/* --------------------------------------------------------- */
/* !Create a class for the widget - 1.0.0 */
/* --------------------------------------------------------- */

class mtphr_social_widget extends WP_Widget {


	/* --------------------------------------------------------- */
	/* !Widget setup - 2.0.0 */
	/* --------------------------------------------------------- */

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

 
	/* --------------------------------------------------------- */
	/* !Display the widget - 2.1.8 */
	/* --------------------------------------------------------- */
	
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
		$instance = mtphr_widgets_social_update_2_1_8( $instance );
		$sites = apply_filters( 'mtphr_widgets_social_sites', $instance['sites'], $widget_id );
		$new_tab = apply_filters( 'mtphr_widgets_social_new_tab', $instance['new_tab'], $widget_id );
	
		// Before widget (defined by themes)
		echo $before_widget;
	
		// Title of widget (before and after defined by themes)
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		// Display the social links
		echo '<div class="mtphr-social-widget-links clearfix">';
			echo metaphor_widgets_social_links_display( $sites, $new_tab );
		echo '</div>';
	
		// After widget (defined by themes)
		echo $after_widget;
	}


	/* --------------------------------------------------------- */
	/* !Update the widget - 2.1.8 */
	/* --------------------------------------------------------- */
	
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
	
		// Strip tags (if needed) and update the widget settings
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
	
		// Loop through the sites and esc_urls
		$sites = array();
		foreach( $new_instance['sites'] as $site=>$url ) {
			$sites[$site] = esc_url( $url );
		}
		$instance['sites'] = $sites;
		$instance['new_tab'] = $new_instance['new_tab'];
	
		return $instance;
	}

	
	/* --------------------------------------------------------- */
	/* !Widget settings - 2.1.8 */
	/* --------------------------------------------------------- */
	
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Get Social', 'mtphr-widgets'),
			'sites' => '',
			'new_tab' => true
		);
	
		$instance = wp_parse_args( (array) $instance, $defaults );
		$instance = mtphr_widgets_social_update( $instance );
		$instance = mtphr_widgets_social_update_2_1_8( $instance );
		?>
		
	  <!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
		</p>
		
		<!-- New window: Checkbox -->
		<p>
			<?php echo metaphor_widgets_social_target( $this->get_field_name( 'new_tab' ), $instance['new_tab'] ); ?>
		</p>
	
		<?php echo metaphor_widgets_social_setup( $this->get_field_name('sites'), $instance['sites'] ); ?>

		<!-- Advanced: Checkbox -->
		<p class="mtphr-widget-advanced">
			<label><input class="checkbox" type="checkbox" /> <?php _e( 'Show Advanced Info', 'mtphr-widgets' ); ?></label>
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
			$shortcode = '[mtphr_social_widget';
			$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
			$shortcode .= ( $instance['new_tab'] == false ) ? ' new_tab="false"' : '';
			if( is_array($instance['sites']) && count($instance['sites']) > 0 ) {
				$shortcode .= ' sites="';
				$sites = '';
				foreach( $instance['sites'] as $site=>$link ) {
					$sites .= $site.'***'.$link.':::';
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


/* --------------------------------------------------------- */
/* !Render the social site target - 2.1.8 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_social_target') ) {
function metaphor_widgets_social_target( $name, $value ) {

	$html = '';
	$html .= '<label><input class="checkbox" type="checkbox" '.checked($value, 'on', false).' name="'.$name.'" /> '.__( 'Open links in a new window/tab', 'mtphr-widgets' ).'</label>';
	
	return $html;
}
}


/* --------------------------------------------------------- */
/* !Render the social site setup - 2.1.8 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_social_setup') ) {
function metaphor_widgets_social_setup( $name, $sites ) {
	
	$allsites = mtphr_widgets_social_sites();
	
	$html = '';
	$html .= '<div class="metaphor-widgets-social-icon-container clearfix">';
		foreach( $allsites as $i=>$sitename ) {
			$active = isset($sites[$i]) ? 'active' : '';
			$html .= '<a class="metaphor-widgets-social-icon '.$active.'" href="#'.$i.'" title="'.$sitename.'" data-prefix="'.$name.'"><i class="metaphor-widgets-ico-'.$i.'"></i></a>';
		}
	$html .= '</div>';
	
	$html .= '<table class="metaphor-widgets-social-sites">';
		if( is_array($sites) && count($sites) > 0 ) {
			foreach( $sites as $site=>$link ) {
				$html .= '<tr class="metaphor-widgets-social-site metaphor-widgets-social-'.$site.'">';
					$html .= '<td class="metaphor-widgets-social-site-icon"><a tabindex="-1" href="#'.$site.'"><i class="metaphor-widgets-ico-'.$site.'"></i></a></td>';
					$html .= '<td><input type="text" name="'.$name.'['.$site.']" value="'.$link.'" /></td>';
				$html .= '</tr>';
			}
		}
	$html .= '</table>';
	
	return $html;
}
}


/* --------------------------------------------------------- */
/* !Display the social links - 2.1.8 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_social_links_display') ) {
function metaphor_widgets_social_links_display( $sites, $new_tab ) {
	
	$html = '';
	$t = ( $new_tab ) ? ' target="_blank"' : '';

	// If there is at least one site
	if( is_array($sites) && count($sites) > 0 ) {
		foreach( $sites as $site=>$url ) {
			$html .= '<a class="mtphr-social-widget-site mtphr-social-widget-'.$site.'" href="'.esc_url($url).'"'.$t.'><i class="metaphor-widgets-ico-'.$site.'"></i></a>';
		}
	}
	
	return $html;
}
}
