<?php

/**
 * Create a class for the widget
 *
 * @since 2.2.1
 */
class mtphr_comments_widget extends WP_Widget {
	
	/** Constructor */
	function __construct() {
		parent::__construct(
			'mtphr-comments',
			__('Metaphor Recent Comments', 'mtphr-widgets'),
			array(
				'classname' => 'mtphr-comments-widget',
				'description' => __('Displays recent comments.', 'mtphr-widgets')
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
		$widget_limit = apply_filters( 'mtphr_widgets_comment_limit', intval( $instance['widget_limit'] ), $widget_id );
		$excerpt_length = apply_filters( 'mtphr_widgets_comment_excerpt_length', intval( $instance['excerpt_length'] ), $widget_id );
		$widget_avatar = apply_filters( 'mtphr_widgets_comment_avatar', isset( $instance['widget_avatar'] ), $widget_id );
		
		if ( $widget_limit == 0 ) {
			$widget_limit = 3;
		}
		if ( $excerpt_length == 0 ) {
			$excerpt_length = 72;
		}
		
		// Before widget (defined by themes)
		echo $before_widget;
		
		// Title of widget (before and after defined by themes)
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}	
		
		$args = array(
			'number' => $widget_limit
		);
		$comments = get_comments($args);
		
		if ( is_array($comments) ) :
		
		$output = '<ul>';
		
		// Start the Loop
		foreach ( $comments as $comment ) {
	
			$output .= '<li>';
			if( $widget_avatar ) {
				$output .= '<span class="mtphr-comments-avatar">'.get_avatar( $comment->comment_author_email ).'</span>';
			}
			$output .= '<span class="mtphr-comments-author">'.$comment->comment_author.'</span>';
	
			$difference = human_time_diff( strtotime($comment->comment_date), current_time('timestamp') );
		  $output .= '<span class="mtphr-comments-date">'.sprintf( __('%s ago', 'mtphr-widgets'), $difference).'</span>';
		  
		  $excerpt = mtphr_widgets_comment_excerpt( $comment->comment_content, $excerpt_length );
		  $output .= '<a class="mtphr-comments-excerpt" href="'.get_comment_link($comment).'">"'.$excerpt.'"</a>';
			$output .= '</li>';
			
		}
		
		$output .= '</ul>';
	
		endif;
	
		echo $output;
	
		// After widget (defined by themes)
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
	
		// Strip tags (if needed) and update the widget settings
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['widget_limit'] = intval( $new_instance['widget_limit'] );
		$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
		$instance['widget_avatar'] = $new_instance['widget_avatar'];
		$instance['advanced'] = $new_instance['advanced'];
	
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Latest Comments', 'mtphr-widgets'),
			'widget_limit' => 3,
			'excerpt_length' => 72,
			'widget_avatar' => '',
			'advanced' => ''
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	  <!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
		</p>
		
	  <!-- Widget Limit: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_limit' ); ?>"><?php _e( 'Number of Comments:', 'mtphr-widgets' ); ?></label><br/>
			<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'widget_limit' ); ?>" name="<?php echo $this->get_field_name( 'widget_limit' ); ?>" value="<?php echo $instance['widget_limit']; ?>" style="width:50px;" />
		</p>
		
		<!-- Excerpt Length: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length:', 'mtphr-widgets' ); ?></label><br/>
			<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo $instance['excerpt_length']; ?>" style="width:50px;" />
		</p>
		
		<!-- Use Avatar: Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['widget_avatar'], 'on' ); ?> id="<?php echo $this->get_field_id( 'widget_avatar' ); ?>" name="<?php echo $this->get_field_name( 'widget_avatar' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'widget_avatar' ); ?>"><?php _e( 'Show Avatar?', 'mtphr-widgets' ); ?></label>
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
			$shortcode = '[mtphr_comments_widget';
			$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
			$shortcode .= ( $instance['widget_limit'] != '' ) ? ' limit="'.$instance['widget_limit'].'"' : '';
			$shortcode .= ( $instance['excerpt_length'] != '' ) ? ' excerpt_length="'.$instance['excerpt_length'].'"' : '';
			$shortcode .= ( $instance['widget_avatar'] != '' ) ? ' avatar="'.$instance['widget_avatar'].'"' : '';
			$shortcode .= ']';
			?>
			<pre class="mtphr-widgets-code"><p><?php echo $shortcode; ?></p></pre>
		</span>
		
		<?php
	}
}


/* --------------------------------------------------------- */
/* !Register the widget - 2.2 */
/* --------------------------------------------------------- */

function mtphr_comments_widget_init() {
	register_widget( 'mtphr_comments_widget' );
}
add_action( 'widgets_init', 'mtphr_comments_widget_init' );