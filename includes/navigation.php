<?php

/**
 * Create a class for the widget
 *
 * @since 2.2.1
 */
class mtphr_post_navigation extends WP_Widget {
	
	/** Constructor */
	function __construct() {
		parent::__construct(
			'mtphr-post-navigation',
			__('Metaphor Post Navigation', 'mtphr-widgets'),
			array(
				'classname' => 'mtphr-post-navigation',
				'description' => __('Displays previous, next, and archive links for single posts.', 'mtphr-widgets')
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
		$home = apply_filters( 'mtphr_widgets_navigation_home', $instance['home'], $widget_id );
		$home_link = apply_filters( 'mtphr_widgets_navigation_home_link', $instance['home_link'], $widget_id );
		$previous = apply_filters( 'mtphr_widgets_navigation_previous', $instance['previous'], $widget_id );
		$next = apply_filters( 'mtphr_widgets_navigation_next', $instance['next'], $widget_id );
	
		$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
		$orderby = apply_filters( 'mtphr_widgets_navigation_orderby', $orderby, $widget_id );
		$order = apply_filters( 'mtphr_widgets_navigation_order', $order, $widget_id );
	
		$taxonomy = isset( $instance['tax'] ) ? $instance['tax'] : '';
		$operator = isset( $instance['operator'] ) ? $instance['operator'] : 'IN';
		$terms = isset( $instance['terms'] ) ? $instance['terms'] : '';
		$taxonomy = apply_filters( 'mtphr_widgets_navigation_taxonomy', $taxonomy, $widget_id );
		$operator = apply_filters( 'mtphr_widgets_navigation_operator', $operator, $widget_id );
		$terms = apply_filters( 'mtphr_widgets_navigation_terms', $terms, $widget_id );
	
		// Before widget (defined by themes)
		echo $before_widget;
	
		// Title of widget (before and after defined by themes)
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
	
		if( is_single() ) {
	
			// Setup the home link
			$obj = get_post_type_object( get_post_type() );
			$home = preg_replace('/{type}/s', $obj->labels->name, $home);
			$home_link = ( $home_link != '' ) ? esc_url($home_link) : get_post_type_archive_link( get_post_type() );
	
			$query_args = array(
		    'post_type' => get_post_type(),
		    'numberposts' => -1,
		    'orderby' => $orderby,
		    'order' => $order
			);
	
			// Check for query args
			$taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : $taxonomy;
			$terms = isset($_GET['terms']) ? $_GET['terms'] : $terms;
	
			if( $taxonomy && $terms ) {
				$tax_query = array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => explode(',', $terms),
					'operator' => $operator
				);
				$query_args['tax_query'] = array( $tax_query );
			}
	
			$p_objs = get_posts( $query_args );
			$p_ids = array();
			foreach( $p_objs as $p ) {
				$p_ids[] = $p->ID;
			}
	
			// Get the current position
			$current = array_search( get_the_id(), $p_ids );
			$prev_post = (($current-1) < 0) ? (count($p_ids)-1) : $current-1;
			$next_post = (($current+1) == count($p_ids)) ? 0 : $current+1;
			$prev_permalink = ( $taxonomy && $terms ) ? esc_url( add_query_arg(array('taxonomy' => $taxonomy, 'terms' => $terms), get_permalink($p_ids[$prev_post])) ) : esc_url( remove_query_arg(array('taxonomy', 'terms'), get_permalink($p_ids[$prev_post])) );
			$next_permalink = ( $taxonomy && $terms ) ? esc_url( add_query_arg(array('taxonomy' => $taxonomy, 'terms' => $terms), get_permalink($p_ids[$next_post])) ) : esc_url( remove_query_arg(array('taxonomy', 'terms'), get_permalink($p_ids[$next_post])) );
			?>
	
			<nav>
				<ul>
					<?php if( $home != '' ) { ?><li class="mtphr-post-navigation-home"><a href="<?php echo $home_link; ?>"><?php echo $home; ?></a></li><?php } ?>
					<?php if( $previous != '' ) { ?><li class="mtphr-post-navigation-previous"><a href="<?php echo $prev_permalink; ?>"><?php echo $previous; ?></a></li><?php } ?>
					<?php if( $next != '' ) { ?><li class="mtphr-post-navigation-next"><a href="<?php echo $next_permalink; ?>"><?php echo $next; ?></a></li><?php } ?>
				</ul>
			</nav>
	
		<?php } ?>
	
		<?php
		// After widget (defined by themes)
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
	
		// Strip tags (if needed) and update the widget settings
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['home'] = sanitize_text_field( $new_instance['home'] );
		$instance['home_link'] = esc_url( $new_instance['home_link'] );
		$instance['previous'] = sanitize_text_field( $new_instance['previous'] );
		$instance['next'] = sanitize_text_field( $new_instance['next'] );
		$instance['orderby'] = $new_instance['orderby'];
		$instance['order'] = $new_instance['order'];
		$instance['tax'] = $new_instance['tax'];
		$instance['operator'] = $new_instance['operator'];
		$instance['terms'] = sanitize_text_field($new_instance['terms']);
		$instance['advanced'] = $new_instance['advanced'];
	
		return $instance;
	}
	
	/** @see WP_Widget::form */
	function form( $instance ) {
	
		// Set up some default widget settings
		$defaults = array(
			'title' => '',
			'home' => '{type}'.__(' home', 'mtphr-widgets'),
			'home_link' => '',
			'previous' => __('Previous', 'mtphr-widgets'),
			'next' => __('Next', 'mtphr-widgets'),
			'orderby' => 'date',
			'order' => 'DESC',
			'tax' => '',
			'operator' => 'IN',
			'terms' => '',
			'advanced' => ''
		);
	
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	
	  <!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:97%;" />
		</p>
	
		<!--Home Text: Text Input -->
		<p style="float:left;width:125px;margin-right:10px;">
			<label for="<?php echo $this->get_field_id( 'home' ); ?>"><?php _e( 'Home link text:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'home' ); ?>" name="<?php echo $this->get_field_name( 'home' ); ?>" value="<?php echo $instance['home']; ?>" style="width:97%;" />
		</p>
	
		<!-- Previous Text: Text Input -->
		<p style="float:left;width:125px;margin-right:10px;">
			<label for="<?php echo $this->get_field_id( 'previous' ); ?>"><?php _e( 'Previous link text:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'previous' ); ?>" name="<?php echo $this->get_field_name( 'previous' ); ?>" value="<?php echo $instance['previous']; ?>" style="width:97%;" />
		</p>
	
		<!-- Next Title: Text Input -->
		<p style="float:left;width:125px;margin-right:10px;">
			<label for="<?php echo $this->get_field_id( 'next' ); ?>"><?php _e( 'Next link text:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'next' ); ?>" name="<?php echo $this->get_field_name( 'next' ); ?>" value="<?php echo $instance['next']; ?>" style="width:97%;" />
		</p>
	
		<!--Home Link: Text Input -->
		<p style="clear:both;">
			<label for="<?php echo $this->get_field_id( 'home_link' ); ?>"><?php _e( 'Custom home link:', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'home_link' ); ?>" name="<?php echo $this->get_field_name( 'home_link' ); ?>" value="<?php echo $instance['home_link']; ?>" style="width:97%;" />
		</p>
	
		<!-- Order: Select -->
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Query order:', 'mtphr-widgets' ); ?></label><br/>
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
			<?php
			$order_array = array( 'ID', 'author', 'title', 'name', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order' );
			foreach( $order_array as $o ) {
				$selected = ( $instance['orderby'] == $o ) ? ' selected="selected"' : '';
				echo '<option value="'.$o.'"'.$selected.'>'.$o.'</option>';
			}
			?>
			</select>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
			<?php
			$order_array = array( 'ASC', 'DESC' );
			foreach( $order_array as $o ) {
				$selected = ( $instance['order'] == $o ) ? ' selected="selected"' : '';
				echo '<option value="'.$o.'"'.$selected.'>'.$o.'</option>';
			}
			?>
			</select>
		</p>
	
		<!-- Taxonomy: Select -->
		<p>
			<label for="<?php echo $this->get_field_id( 'tax' ); ?>"><?php _e( 'Taxonomy:', 'mtphr-widgets' ); ?></label><br/>
			<select style="width:150px;" id="<?php echo $this->get_field_id( 'tax' ); ?>" name="<?php echo $this->get_field_name( 'tax' ); ?>">
				<option value="">-----</option>
				<?php
				$args = array( 'public' => true );
				$taxonomies = get_taxonomies( $args,'names' );
				foreach ( $taxonomies as $tax ) {
					$selected = ( $instance['tax'] == $tax ) ? ' selected="selected"' : '';
				  echo '<option '.$selected.'>'.$tax.'</option>';
				}
				?>
			</select>
			<select style="width:69px;" id="<?php echo $this->get_field_id( 'operator' ); ?>" name="<?php echo $this->get_field_name( 'operator' ); ?>">
				<option <?php selected($instance['operator'], 'IN'); ?>>IN</option>
				<option <?php selected($instance['operator'], 'NOT IN'); ?>>NOT IN</option>
				<option <?php selected($instance['operator'], 'AND'); ?>>AND</option>
			</select>
		</p>
	
		<!-- Terms: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'terms' ); ?>"><?php _e( 'Terms: <small>Use slugs separated by commas (,)</small>', 'mtphr-widgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'terms' ); ?>" name="<?php echo $this->get_field_name( 'terms' ); ?>" value="<?php echo $instance['terms']; ?>" style="width:97%;" />
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
			$shortcode = '[mtphr_post_navigation';
			$shortcode .= ( $instance['title'] != '' ) ? ' title="'.$instance['title'].'"' : '';
			$shortcode .= ( $instance['home'] != '' ) ? ' home="'.$instance['home'].'"' : '';
			$shortcode .= ( $instance['home_link'] != '' ) ? ' home_link="'.$instance['home_link'].'"' : '';
			$shortcode .= ( $instance['previous'] != '' ) ? ' previous="'.$instance['previous'].'"' : '';
			$shortcode .= ( $instance['next'] != '' ) ? ' next="'.$instance['next'].'"' : '';
			$shortcode .= ( $instance['orderby'] != '' ) ? ' orderby="'.$instance['orderby'].'"' : '';
			$shortcode .= ( $instance['order'] != '' ) ? ' order="'.$instance['order'].'"' : '';
			$shortcode .= ( $instance['tax'] != '' ) ? ' tax="'.$instance['tax'].'"' : '';
			$shortcode .= ( $instance['operator'] != '' ) ? ' operator="'.$instance['operator'].'"' : '';
			$shortcode .= ( $instance['terms'] != '' ) ? ' terms="'.$instance['terms'].'"' : '';
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

function mtphr_post_navigation_init() {
	register_widget( 'mtphr_post_navigation' );
}
add_action( 'widgets_init', 'mtphr_post_navigation_init' );