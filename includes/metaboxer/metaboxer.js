jQuery(document).ready( function($) {


	

/**
 * Add list functionality.
 *
 * @since 1.0.0
 */	
$('.mtphr-widgets-metaboxer-list').each( function(index) {

	// Set the field order
	mtphr_widgets_metaboxer_lists_set_order( $(this) );
	
	// Add sorting to the items
	mtphr_widgets_metaboxer_lists_set_sortable( $(this) );
});

// Add sorting to the items
function mtphr_widgets_metaboxer_lists_set_sortable( $list ) {

	$list.sortable( {
		handle: '.mtphr-widgets-metaboxer-list-item-handle',
		items: '.mtphr-widgets-metaboxer-list-item',
		axis: 'y',
		helper: function(e, tr) {
	    var $originals = tr.children();
	    var $helper = tr.clone();
	    $helper.children().each(function(index) {
	      // Set helper cell sizes to match the original sizes
	      $(this).width($originals.eq(index).width())
	    });
	    return $helper;
	  },
	  update: function( event, ui ) {
			
			// Set the field order
			mtphr_widgets_metaboxer_lists_set_order( $(this) );
		}
	});	
}

// Set the list item order
function mtphr_widgets_metaboxer_lists_set_order( $list ) {

	// Set the order of the items
	$list.find('.mtphr-widgets-metaboxer-list-item').each( function(i) {
		
		$(this).find('.mtphr-widgets-metaboxer-list-structure-item').each( function(e) {
			
			var base = $(this).attr('base');
			var field = $(this).attr('field');
			$(this).find('input,textarea,select').attr('name', base+'['+i+']['+field+']');
		});
	});

	// Hide the delete if only one element
	if( $list.find('.mtphr-widgets-metaboxer-list-item').length == 1 ) {
		
		$list.find('.mtphr-widgets-metaboxer-list-item-handle,.mtphr-widgets-metaboxer-list-item-delete').hide();
	}
}

// Add item click
$('.mtphr-widgets-metaboxer-list-item-add').live( 'click', function(e) {
	e.preventDefault();

	// Create a new item with blank content
	var $parent = $(this).parents('.mtphr-widgets-metaboxer-list-item');
	var $new = $parent.clone(true).hide();
	$new.find('input,textarea,select').removeAttr('value').removeAttr('checked').removeAttr('selected');
	$parent.after($new);
	$new.fadeIn();
	
	// Set the field order
	mtphr_widgets_metaboxer_lists_set_order( $(this).parents('.mtphr-widgets-metaboxer-list') );
	
	// Show the handles
	$(this).parents('.mtphr-widgets-metaboxer-list').find('.mtphr-widgets-metaboxer-list-item-handle,.mtphr-widgets-metaboxer-list-item-delete').show();
	
	// Set the focus to the new input
	var inputs = $new.find('input,textarea,select');
	$(inputs[0]).focus();
});

// Delete item click
$('.mtphr-widgets-metaboxer-list-item-delete').live( 'click', function(e) {
	e.preventDefault();
	
	// Fade out the item
	$(this).parents('.mtphr-widgets-metaboxer-list-item').fadeOut( function() {
		
		// Get the list
		var $list = $(this).parents('.mtphr-widgets-metaboxer-list');
		
		// Remove the item
		$(this).remove();
		
		// Set the field order
		mtphr_widgets_metaboxer_lists_set_order( $list );
	});
});

// List handle hover
$('.mtphr-widgets-metaboxer-list-item-handle').live( 'hover', function() {
	mtphr_widgets_metaboxer_lists_set_sortable( $(this).parents('.mtphr-widgets-metaboxer-list') );
});

// Listen for the contact widget save button
$('.widget-control-save').click( function() {

	var $widget = $(this).parents('.widget');
	if( $widget.find('.mtphr-widget-advanced').length > 0 ) {
		
		var $spinner = $(this).siblings('.spinner');
	
		// Wait for the spinner to disappear and run jquery
		var mtphr_widgets_check = setInterval( function() {	
			if( $spinner.not(':visible') ) {
				clearInterval( mtphr_widgets_check );
				
				// Hide the delete if only one element
				setTimeout( function() {
					var $list = $widget.find('.mtphr-widgets-metaboxer-list');
					if( $list.find('.mtphr-widgets-metaboxer-list-item').length == 1 ) {
						$list.find('.mtphr-widgets-metaboxer-list-item-handle,.mtphr-widgets-metaboxer-list-item-delete').hide();
					}
				}, 100);	
			}
		}, 100);
	}
});



});