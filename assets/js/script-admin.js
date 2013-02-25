jQuery( document ).ready( function($) {
	
	
	
	
	/**
	 * Contact Widget advanced settings
	 *
	 * @since 1.0.0
	 */
	// Set the initial state of the advanced fields
	$('.mtphr-widget-advanced').each( function(index) {
		mtphr_widgets_advanced_fields( $(this) );
	});
	
	// Listen for the advanced fields toggle
	$('.mtphr-widget-advanced').live( 'click', function(e) {
		mtphr_widgets_advanced_fields( $(this) );		
	});
	
	// Show or hide the advanced fields
	function mtphr_widgets_advanced_fields( $advanced ) {

		if( $advanced.children('input[type="checkbox"]').is(':checked') ) {
			$advanced.siblings('.mtphr-widget-id').show();
			$advanced.siblings('.mtphr-widget-shortcode').show();
		} else {
			$advanced.siblings('.mtphr-widget-id').hide();
			$advanced.siblings('.mtphr-widget-shortcode').hide();
		}	
	}
	
	// Listen for the save button
	$('.widget-control-save').click( function() {
		
		var $widget = $(this).parents('.widget');
		if( $widget.find('.mtphr-widget-advanced').length > 0 ) {

			var $spinner = $(this).siblings('.spinner');
			
			// Wait for the spinner to disappear and run jquery
			var mtphr_widgets_check = setInterval( function() {
				if( $spinner.not(':visible') ) {
					clearInterval( mtphr_widgets_check );
					
					// Hide or show advanced fields
					setTimeout( function() {
						var $advanced = $widget.find('.mtphr-widget-advanced');
						mtphr_widgets_advanced_fields( $advanced );
					}, 200);	
				}
			}, 100);
		}
	});
	
	

	
	
});