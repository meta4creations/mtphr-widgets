jQuery( document ).ready( function($) {
	
	
		
		// Slide the collapsible content up/down
		$('.mtphr-collapse-widget-heading').children('a').click( function(e) {
			e.preventDefault();
			
			if( $(this).parent().hasClass('active') ) {
				$(this).parent().removeClass('active');
				$(this).parent().siblings('.mtphr-collapse-widget-description').slideUp( 500, 'easeOutExpo' );
			} else {
				$(this).parent().addClass('active');
				$(this).parent().siblings('.mtphr-collapse-widget-description').slideDown(  500, 'easeOutExpo' );
			}
		});

	
	
});