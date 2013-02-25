<?php
/**
 * Put all the Metaboxer admin function here fields here
 *
 * @package  XXX
 * @author   Metaphor Creations
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 */



/**
 * Create a field container and switch.
 *
 * @since 1.0.0
 */
function mtphr_widgets_metaboxer_container( $field, $context ) {

	global $post;

	$default = isset( $field['default'] ) ? $field['default'] : '';
	$value = ( get_post_meta( $post->ID, $field['id'], true ) != '' ) ? get_post_meta( $post->ID, $field['id'], true ) : $default;
	$display = isset( $field['display'] ) ? $field['display'] : '';
	?>
	<tr class="mtphr-widgets-metaboxer-field mtphr-widgets-metaboxer-field-<?php echo $field['type']; ?> mtphr-widgets-metaboxer<?php echo $field['id']; ?><?php if( isset($field['class']) ) { echo ' '.$field['class']; } ?> clearfix">	
		
		<?php
		$content_class = 'mtphr-widgets-metaboxer-field-content mtphr-widgets-metaboxer-field-content-full mtphr-widgets-metaboxer-'.$field['type'].' clearfix';
		$content_span = ' colspan="2"';
		$label = false;
		
		if ( isset($field['name']) || isset($field['description']) ) {
		
			$content_class = 'mtphr-widgets-metaboxer-field-content mtphr-widgets-metaboxer-'.$field['type'].' clearfix';
			$content_span = '';
			$label = true;
			?>

			<?php if( $context == 'side' || $display == 'vertical' ) { ?><td><table><tr><?php } ?>
			
			<td class="mtphr-widgets-metaboxer-label">
				<?php if( isset($field['name']) ) { ?><label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label><?php } ?>
				<?php if( isset($field['description']) ) { ?><small><?php echo $field['description']; ?></small><?php } ?>
			</td>
			
			<?php if( $context == 'side' || $display == 'vertical' ) { echo '</tr>'; } ?>

			<?php
		}
		?>
		
		<?php if( $label ) { if( $context == 'side' || $display == 'vertical' ) { echo '<tr>'; } } ?>
		
		<td<?php echo $content_span; ?> class="<?php echo $content_class; ?>" id="<?php echo $post->ID; ?>">
			<?php
			// Call the function to display the field
			if ( function_exists('mtphr_widgets_metaboxer_'.$field['type']) ) {
				call_user_func( 'mtphr_widgets_metaboxer_'.$field['type'], $field, $value );
			}
			?>
		</td>
		
		<?php if( $label ) { if( $context == 'side' || $display == 'vertical' ) { echo '</tr></table></td>'; } } ?>
		
	</tr>
	<?php
}




/**
 * Append fields
 *
 * @since 1.0.0
 */
function mtphr_widgets_metaboxer_append_field( $field ) {

	// Add appended fields
	if( isset($field['append']) ) {
		
		$fields = $field['append'];
		$settings = ( isset($field['option'] ) ) ? $field['option'] : false;

		if( is_array($fields) ) {
		
			foreach( $fields as $id => $field ) {
				
				// Get the value
				if( $settings) {
					$options = get_option( $settings );
					$value = isset( $options[$id] ) ? $options[$id] : get_option( $id );	
				} else {
					global $post;
					$value = get_post_meta( $post->ID, $id, true );
				}
				
				// Set the default if no value
				if( $value == '' && isset($field['default']) ) {
					$value = $field['default'];
				}
	
				if( isset($field['type']) ) {
		
					if( $settings ) {
						$field['id'] = $settings.'['.$id.']';
						$field['option'] = $settings;
					} else {
						$field['id'] = $id;
					}
	
					// Call the function to display the field
					if ( function_exists('mtphr_widgets_metaboxer_'.$field['type']) ) {
						echo '<div class="mtphr-widgets-metaboxer-appended mtphr-widgets-metaboxer'.$field['id'].'">';
						call_user_func( 'mtphr_widgets_metaboxer_'.$field['type'], $field, $value );
						echo '</div>';
					}
				}
			}
		}
	}
}



/**
 * Renders an text field.
 *
 * @since 1.0.1
 */
function mtphr_widgets_metaboxer_text( $field, $value='' ) {
	$size = ( isset($field['size']) ) ? $field['size'] : 40;
	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';
	$text_align = ( isset($field['text_align']) ) ? ' style="text-align:'.$field['text_align'].'"' : '' ;
	$output = $before.'<input name="'.$field['id'].'" id="'.$field['id'].'" type="text" value="'.$value.'" size="'.$size.'"'.$text_align.'>'.$after;
	echo $output;
	
	// Add appended fields
	mtphr_widgets_metaboxer_append_field($field);
}



/**
 * Renders a textarea.
 *
 * @since 1.0.0
 */
function mtphr_widgets_metaboxer_textarea( $field, $value='' ) {
	$rows = ( isset($field['rows']) ) ? $field['rows'] : 5;
	$cols = ( isset($field['cols']) ) ? $field['cols'] : 40;
	$output = '<textarea name="'.$field['id'].'" id="'.$field['id'].'" rows="'.$rows.'" cols="'.$cols.'">'.$value.'</textarea>';
	echo $output;
	
	// Add appended fields
	mtphr_widgets_metaboxer_append_field($field);
}



/**
 * Renders a select field.
 *
 * @since 1.0.0
 */
function mtphr_widgets_metaboxer_select( $field, $value='' ) {

	$before = ( isset($field['before']) ) ? '<span>'.$field['before'].' </span>' : '';
	$after = ( isset($field['after']) ) ? '<span> '.$field['after'].'</span>' : '';
	
	$output = $before.'<select name="'.$field['id'].'" id="'.$field['id'].'">';
	
  if( $field['options'] ) {
  
  	$key_val = isset( $field['key_val'] ) ? true : false;
  	
	  foreach ( $field['options'] as $key => $option ) {
	  	if( is_numeric($key) && !$key_val ) {
				$name = ( is_array( $option ) ) ? $option['name'] : $option;
				$val = ( is_array( $option ) ) ? $option['value'] : $option;
			} else {
				$name = $option;
				$val = $key;
			}
			$selected = ( $val == $value ) ? 'selected="selected"' : '';
			$output .= '<option value="'.$val.'" '.$selected.'>'.stripslashes( $name ).'</option>';
		}
	}
  $output .= '</select>'.$after;

	echo $output;
	
	// Add appended fields
	mtphr_widgets_metaboxer_append_field($field);
}



/**
 * Renders a list.
 *
 * @since 1.0.2
 */
function mtphr_widgets_metaboxer_list( $field, $value='' ) {
		
	$output = '<table>';	
	
	$headers = false;
	$header_str = '';
	foreach( $field['structure'] as $id => $str ) {
	
		$header_str .= '<th>';
		if( isset($str['header']) ) {
			$headers = true;
			$header_str .= $str['header'];
		}
		$header_str .= '</th>';
	}
	if( $headers ) {
		$output .= '<tr><td class="mtphr-widgets-metaboxer-list-item-handle"></td>'.$header_str.'</tr>';
	}
	
	$buttons = '<td class="mtphr-widgets-metaboxer-list-item-delete"><a href="#">Delete</a></td><td class="mtphr-widgets-metaboxer-list-item-add"><a href="#">Add</a></td>';
	if( is_array($value) ) {
		foreach( $value as $i=>$v ) {
			$structure = mtphr_widgets_metaboxer_list_structure( $i, $field, $v );
			$output .= '<tr class="mtphr-widgets-metaboxer-list-item"><td class="mtphr-widgets-metaboxer-list-item-handle"><span></span></td>'.$structure.$buttons.'</tr>';
		}
	}
	
	// If nothing is being output make sure one field is showing
	if( $value == '' || count($value) == 0 ) {
		$structure = mtphr_widgets_metaboxer_list_structure( 0, $field );
		$output .= '<tr class="mtphr-widgets-metaboxer-list-item"><td class="mtphr-widgets-metaboxer-list-item-handle"><span></span></td>'.$structure.$buttons.'</tr>';
	}
	
	$output .= '</table>';
	
	echo $output;
	
	// Add appended fields
	mtphr_widgets_metaboxer_append_field($field);
}

// Add the list structure
function mtphr_widgets_metaboxer_list_structure( $pos, $fields, $m_value='' ) {

	$main_id = $fields['id'];
	
	// Add appended fields
	if( isset($fields['structure']) ) {
		
		$fields = $fields['structure'];
		$settings = ( isset($fields['option'] ) ) ? $fields['option'] : false;

		if( is_array($fields) ) {
		
			ob_start();
			
			foreach( $fields as $id => $field ) {
				
				// Get the value
				$value = isset($m_value[$id]) ? $m_value[$id] : '';
				
				// Get the width
				$width = isset($field['width']) ? ' style="width:'.$field['width'].'"' : '';
	
				if( isset($field['type']) ) {
		
					$field['id'] = $main_id.'['.$pos.']['.$id.']';
	
					// Call the function to display the field
					if ( function_exists('mtphr_widgets_metaboxer_'.$field['type']) ) {

						echo '<td'.$width.' class="mtphr-widgets-metaboxer-list-structure-item mtphr-widgets-metaboxer'.$main_id.'-'.$id.'" base="'.$main_id.'" field="'.$id.'">';
						call_user_func( 'mtphr_widgets_metaboxer_'.$field['type'], $field, $value );
						echo '</td>';
					}
				}
			}
			
			return ob_get_clean();
		}
	}
}



