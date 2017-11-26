<?php

/**
 * Create a class for the widget
 *
 * @since 2.2.1
 */
class mtphr_social_widget extends WP_Widget {
	
	/** Constructor */
	function __construct() {
		parent::__construct(
			'mtphr-social',
			__('Metaphor Social Links', 'mtphr-widgets'),
			array(
				'classname' => 'mtphr-social-widget',
				'description' => __('Displays your social links.', 'mtphr-widgets')
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

	/** @see WP_Widget::update */	
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
		$instance['advanced'] = $new_instance['advanced'];
	
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Get Social', 'mtphr-widgets'),
			'sites' => '',
			'new_tab' => true,
			'advanced' => ''
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
		foreach( $allsites as $i=>$site ) {
			
			$active = isset($sites[$i]) ? 'active' : '';
			
			$prefix = 'metaphor-widgets-ico';
			$id = $i;
			$title = $site;
			$href = $i;
					
			if( is_array($site) ) {
				$prefix = $site['prefix'];
				$id = $title = $site['id'];
				$href = $site['prefix'].'__'.$site['id'];
			}
					
			$html .= '<a class="metaphor-widgets-social-icon '.$active.'" href="#'.$href.'" title="'.$title.'" data-name="'.$name.'" data-prefix="'.$prefix.'" data-id="'.$id.'"><i class="'.$prefix.' '.$prefix.'-'.$id.'"></i></a>';
		}
	$html .= '</div>';
	
	if( is_plugin_active('mtphr-shortcodes/mtphr-shortcodes.php') ) {
		$html .= '<a href="#mtphr-widgets-icon-modal" class="button button-primary mtphr-shortcodes-modal-link" data-name="'.$name.'" style="margin-bottom:15px;">'.__('Click here to add more icons!', 'mtphr-widgets').'</a>';
	} else {
		$url = get_bloginfo('wpurl').'/wp-admin/plugin-install.php?tab=plugin-information&plugin=mtphr-shortcodes&TB_iframe=true&width=640&height=500';
		$html .= '<div style="margin-bottom:15px;">'.sprintf(__('Install <a class="thickbox" href="%s"><strong>Metaphor Shortcodes</strong></a> to add more icons to this list!.','mtphr-members'), $url).'</div>';
	}
	
	$html .= '<table class="metaphor-widgets-social-sites">';
		if( is_array($sites) && count($sites) > 0 ) {
			foreach( $sites as $site=>$link ) {
				
				$display = true;
				$prefix = 'metaphor-widgets-ico';
				$id = $site;
				$title = $site;
				$href = $site;
				
				$split = explode( '__', $site );
				if( count($split) > 1 ) {
					$prefix = $split[0];
					$id = $title = $split[1];
					if( !mtphr_widgets_mtphr_shortcodes() ) {
						$display = false;
					}
				}
				
				if( $display ) {
					$html .= '<tr class="metaphor-widgets-social-site metaphor-widgets-social-'.$href.'">';
						$html .= '<td class="metaphor-widgets-social-site-icon"><a tabindex="-1" href="#'.$href.'"><i class="'.$prefix.' '.$prefix.'-'.$id.'"></i></a></td>';
						$html .= '<td><input type="text" name="'.$name.'['.$href.']" value="'.$link.'" /></td>';
					$html .= '</tr>';
				} else {
					$html .= '<input type="hidden" name="'.$name.'['.$href.']" value="'.$link.'" />';
				}
			}
		}
	$html .= '</table>';

	return $html;
}
}


/* --------------------------------------------------------- */
/* !Display the social links - 2.3 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_social_links_display') ) {
function metaphor_widgets_social_links_display( $sites, $new_tab ) {
	
	$html = '';
	$t = ( $new_tab ) ? ' target="_blank"' : '';

	// If there is at least one site
	if( is_array($sites) && count($sites) > 0 ) {
		foreach( $sites as $site=>$url ) {
			
			$display = true;
			$prefix = 'metaphor-widgets-ico';
			$id = $site;
			$title = $site;
			$href = $site;
				
			$split = explode( '__', $site );
			if( count($split) > 1 ) {
				$prefix = $split[0];
				$id = $title = $split[1];
				if( !mtphr_widgets_mtphr_shortcodes() ) {
					$display = false;
				}
			}
			
			$icon = apply_filters( 'mtphr_social_widget_site_icon', '<i class="'.$prefix.' '.$prefix.'-'.$id.'"></i>', $site, $prefix, $id );
			$html .= '<a class="mtphr-social-widget-site mtphr-social-widget-'.$site.'" href="'.esc_url($url).'"'.$t.'>'.$icon.'</a>';
		}
	}
	
	return $html;
}
}


/* --------------------------------------------------------- */
/* !Register the widget - 2.2 */
/* --------------------------------------------------------- */

function mtphr_social_widget_init() {
	register_widget( 'mtphr_social_widget' );
}
add_action( 'widgets_init', 'mtphr_social_widget_init' );