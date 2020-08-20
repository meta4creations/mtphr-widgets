<?php

/**
 * Create a class for the widget
 *
 * @since 2.2.1
 */
class mtphr_collapse_widget extends WP_Widget {
	
	/** Constructor */
	function __construct() {
		parent::__construct(
			'mtphr-collapse',
			__('Metaphor Collapse', 'mtphr-widgets'),
			array(
				'classname' => 'mtphr-collapse-widget',
				'description' => __('Displays collapsible content.', 'mtphr-widgets')
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
					$active = ( isset($info['open']) && $info['open'] == 'on' ) ? ' active' : '';
					$style = ( isset($info['open']) && $info['open'] == 'on' ) ? ' style="display:block;"' : '';
					echo '<div class="mtphr-collapse-widget-block">';
						echo '<p class="mtphr-collapse-widget-heading'.$active.'">';
							$toggle = apply_filters( 'mtphr_collapse_widget_toggle', '<span class="mtphr-collapse-widget-toggle"><span class="mtphr-toggle-expand"><i class="metaphor-widgets-ico-plus-square"></i></span><span class="mtphr-toggle-collapse"><i class="metaphor-widgets-ico-minus-square"></i></span></span>' );
							echo '<a href="#">'.$toggle.sanitize_text_field($info['title']).'</a>';
					echo '</p>';
					echo '<p class="mtphr-collapse-widget-description"'.$style.'>'.make_clickable($info['description']).'</p></div>';
				}	
			}
		}
		
		// After widget (defined by themes)
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
	
		// Strip tags (if needed) and update the widget settings
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['collapse_info'] = $new_instance['collapse_info'];
		$instance['advanced'] = $new_instance['advanced'];
	
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Information', 'mtphr-widgets'),
			'collapse_info' => array(
				array(
					'title' => __('Add title here...', 'mtphr-widgets'),
					'description' => __('Add a description here...', 'mtphr-widgets')
				)
			),
			'advanced' => ''
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	  <!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
		</p>
		
		<?php echo metaphor_widgets_collapse_setup( $this->get_field_name('collapse_info'), $instance['collapse_info'] ); ?>
		
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


/* --------------------------------------------------------- */
/* !Render the collapse info setup - 2.1.15 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_collapse_setup') ) {
function metaphor_widgets_collapse_setup( $name, $data ) {
	
	$html = '';
	$html .= '<table class="mtphr-widgets-list mtphr-widgets-default-list">';
		$html .= '<tr>';
			$html .= '<th class="mtphr-widgets-list-handle"></th>';
			$html .= '<th>'.__('Title', 'mtphr-widgets').'</th>';
			$html .= '<th>'.__('Description', 'mtphr-widgets').'</th>';
			$html .= '<th colspan="3">'.__('Open', 'mtphr-widgets').'</th>';
		$html .= '</tr>';
		if( is_array($data) && count($data) > 0 ) {
			foreach( $data as $i=>$d ) {
				$html .= metaphor_widgets_collapse_row( $name, $d );
			}
		} else {
			$html .= metaphor_widgets_collapse_row( $name );
		}
	$html .= '</table>';
	
	return $html;
}
}


/* --------------------------------------------------------- */
/* !Render a collapse row - 2.1.15 */
/* --------------------------------------------------------- */

if( !function_exists('metaphor_widgets_collapse_row') ) {
function metaphor_widgets_collapse_row( $name, $data=false ) {
	$title = ( isset($data) && isset($data['title']) ) ? $data['title'] : '';
	$description = ( isset($data) && isset($data['description']) ) ? $data['description'] : '';
	$open = ( isset($data) && isset($data['open']) && $data['open'] == 'on' ) ? $data['open'] : '';
	
	$html = '';
	$html .= '<tr class="mtphr-widgets-list-item">';
		$html .= '<td class="mtphr-widgets-list-handle"><span><i class="metaphor-widgets-ico-down-up-scale-1"></i></span></td>';
		$html .= '<td class="mtphr-widgets-collapse-title">';
			$html .= '<textarea name="'.$name.'[title]" data-name="'.$name.'" data-key="title" rows="1">'.htmlentities($title).'</textarea>';
		$html .= '</td>';
		$html .= '<td class="mtphr-widgets-collapse-description">';
			$html .= '<textarea name="'.$name.'[description]" data-name="'.$name.'" data-key="description" rows="1">'.htmlentities($description).'</textarea>';
		$html .= '</td>';
		$html .= '<td class="mtphr-widgets-collapse-open">';
			$html .= '<input type="checkbox" name="'.$name.'[open]" data-name="'.$name.'" data-key="open" value="on" '.checked($open, 'on', false).' />';
		$html .= '</td>';
		$html .= '<td class="mtphr-widgets-list-delete"><a href="#"><i class="metaphor-widgets-ico-minus-alt"></i></a></td>';
		$html .= '<td class="mtphr-widgets-list-add"><a href="#"><i class="metaphor-widgets-ico-plus-alt"></i></a></td>';
	$html .= '</tr>';
	
	return $html;
}
}


/* --------------------------------------------------------- */
/* !Register the widget - 2.2 */
/* --------------------------------------------------------- */

function mtphr_collapse_widget_init() {
	register_widget( 'mtphr_collapse_widget' );
}
add_action( 'widgets_init', 'mtphr_collapse_widget_init' );