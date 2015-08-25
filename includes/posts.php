<?php

/**
 * Create a class for the widget
 *
 * @since 2.2.1
 */
class mtphr_posts_widget extends WP_Widget {
	
	/** Constructor */
	function __construct() {
		parent::__construct(
			'mtphr-posts',
			__('Metaphor Recent Posts', 'mtphr-widgets'),
			array(
				'classname' => 'mtphr-posts-widget',
				'description' => __('Displays recent posts.', 'mtphr-widgets')
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
		$widget_limit = apply_filters( 'mtphr_widgets_post_limit', intval( $instance['widget_limit'] ), $widget_id );
		$excerpt_length = apply_filters( 'mtphr_widgets_post_excerpt_length', intval( $instance['excerpt_length'] ), $widget_id );
		$read_more = isset( $instance['read_more'] ) ? $instance['read_more'] : __('Read more', 'mtphr-widgets');
		$read_more = apply_filters( 'mtphr_widgets_post_read_more', sanitize_text_field($read_more), $widget_id );
		$author = apply_filters( 'mtphr_widgets_post_author', sanitize_text_field($instance['author']), $widget_id );
	
		$instance['category'] = isset($instance['category']) ? $instance['category'] : '';
		$category = apply_filters( 'mtphr_widgets_post_category', sanitize_text_field($instance['category']), $widget_id );
	
		if ( $widget_limit == 0 ) {
			$widget_limit = 3;
		}
	
		// Before widget (defined by themes)
		echo $before_widget;
	
		// Title of widget (before and after defined by themes)
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
	
		$args = array(
			'post_type'=> 'post',
			'posts_per_page' => $widget_limit,
			'author_name' => $author,
			'category_name' => $category
		);
		$args = apply_filters( 'mtphr_widgets_post_args', $args, $widget_id );
	
		// Save the original query & create a new one
		global $wp_query;
		$original_query = $wp_query;
		$wp_query = null;
		$wp_query = new WP_Query();
		$wp_query->query( $args );
		
		$output = '';
	
		if ( have_posts() ) :
	
		$output .= '<ul>';
	
		// Start the Loop
		while ( $wp_query->have_posts() ) : $wp_query->the_post();
	
			$output .= '<li>';
			$content = '<a class="mtphr-posts-widget-title" href="'.get_permalink().'">'.get_the_title().'</a> ';
			if( $excerpt_length > 0 ) {
				$content .= mtphr_widgets_post_excerpt( $excerpt_length );
			}
			if( $read_more != '' ) {
				$content .= '<span class="readmore-wrapper"><a class="readmore" href="'.get_permalink().'">'.$read_more.'</a></span>';
			}
			$output .= apply_filters( 'mtphr_widgets_posts_content', $content, $excerpt_length, $read_more );
			$output .= '</li>';
	
		endwhile;
	
		$output .= '</ul>';
	
		else :
		endif;
	
		$wp_query = null;
		$wp_query = $original_query;
		wp_reset_postdata();
	
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
		$instance['read_more'] = sanitize_text_field( $new_instance['read_more'] );
		$instance['author'] = $new_instance['author'];
		$instance['category'] = $new_instance['category'];
		$instance['advanced'] = $new_instance['advanced'];
	
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Blog Posts', 'mtphr-widgets'),
			'widget_limit' => 3,
			'excerpt_length' => 72,
			'read_more' => __('Read more', 'mtphr-widgets'),
			'author' => '',
			'category' => '',
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
			<label for="<?php echo $this->get_field_id( 'widget_limit' ); ?>"><?php _e( 'Number of Posts:', 'mtphr-widgets' ); ?></label><br/>
			<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'widget_limit' ); ?>" name="<?php echo $this->get_field_name( 'widget_limit' ); ?>" value="<?php echo $instance['widget_limit']; ?>" style="width:50px;" />
		</p>
	
		<!-- Excerpt Length: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length:', 'mtphr-widgets' ); ?></label><br/>
			<input class="widefat" type="number" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo $instance['excerpt_length']; ?>" style="width:50px;" />
		</p>
	
		<!--Read More: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'read_more' ); ?>"><?php _e( 'More Text :', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'read_more' ); ?>" name="<?php echo $this->get_field_name( 'read_more' ); ?>" value="<?php echo $instance['read_more']; ?>" style="width:97%;" />
		</p>
	
		<!-- Author: Select -->
		<table style="margin: 0 0 1em;">
			<tr><td>
				<label for="<?php echo $this->get_field_id( 'author' ); ?>"><?php _e( 'Author:', 'mtphr-widgets' ); ?></label><br/>
				<select name="<?php echo $this->get_field_name( 'author' ); ?>" id="<?php echo $this->get_field_id( 'author' ); ?>">
				<option value="">-----</option>
				<?php
				$authors = mtphr_widgets_author_array();
				foreach( $authors as $author ) {
					if( $instance['author'] == $author ) { ?>
						<option value="<?php echo $author; ?>" selected="selected"><?php echo $author; ?></option>
					<?php } else { ?>
						<option value="<?php echo $author; ?>"><?php echo $author; ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</td><td>
				<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'mtphr-widgets' ); ?></label><br/>
				<select name="<?php echo $this->get_field_name( 'category' ); ?>" id="<?php echo $this->get_field_id( 'category' ); ?>">
				<option value="">-----</option>
				<?php
				$categories = get_categories();
				foreach( $categories as $category ) {
					if( $instance['category'] == $category->slug ) { ?>
						<option value="<?php echo $category->slug; ?>" selected="selected"><?php echo $category->name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</td></tr>
		</table>
	
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
			$shortcode = '[mtphr_posts_widget';
			$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
			$shortcode .= ( $instance['widget_limit'] != '' ) ? ' limit="'.$instance['widget_limit'].'"' : '';
			$shortcode .= ( $instance['excerpt_length'] != '' ) ? ' excerpt_length="'.$instance['excerpt_length'].'"' : '';
			$shortcode .= ( $instance['read_more'] != '' ) ? ' read_more="'.$instance['read_more'].'"' : '';
			$shortcode .= ( $instance['author'] != '' ) ? ' author="'.$instance['author'].'"' : '';
			$shortcode .= ( $instance['category'] != '' ) ? ' category="'.$instance['category'].'"' : '';
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

function mtphr_posts_widget_init() {
	register_widget( 'mtphr_posts_widget' );
}
add_action( 'widgets_init', 'mtphr_posts_widget_init' );