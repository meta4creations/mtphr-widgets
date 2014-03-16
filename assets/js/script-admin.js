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

		if( $advanced.find('input[type="checkbox"]').is(':checked') ) {
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
	
	
	
	
	/* --------------------------------------------------------- */
	/* !Social sites - 2.1.8 */
	/* --------------------------------------------------------- */

	$('.metaphor-widgets-social-sites').sortable( {
		handle: '.metaphor-widgets-social-site-icon',
		items: '.metaphor-widgets-social-site',
		axis: 'y',
		helper: function(e, tr) {
	    var $originals = tr.children();
	    var $helper = tr.clone();
	    $helper.children().each(function(index) {
	      // Set helper cell sizes to match the original sizes
	      $(this).width($originals.eq(index).width())
	    });
	    return $helper;
	  }
	});

	$('.metaphor-widgets-social-icon').live('click', function(e) {
		e.preventDefault();

		var $table = $(this).parent().siblings('.metaphor-widgets-social-sites'),
				site = $(this).attr('href'),
				prefix = $(this).attr('data-prefix'),
				
		site = site.substr(1, site.length);

		if( $(this).hasClass('active') ) {
			$(this).removeClass('active');
			apex_social_settings_remove_icon( site, $table );
		} else {
			$(this).addClass('active');
			apex_social_settings_add_icon( site, $table, prefix );
		}
	});

	function apex_social_settings_remove_icon( site, $table ) {
		var $row = $table.find('.metaphor-widgets-social-'+site);
		$row.remove();
	}

	function apex_social_settings_add_icon( site, $table, prefix ) {
		var row = '<tr class="metaphor-widgets-social-site metaphor-widgets-social-'+site+'">';
		row += '<td class="metaphor-widgets-social-site-icon"><a tabindex="-1" href="#'+site+'"><i class="metaphor-widgets-ico-'+site+'"></i></a></td>';
		row += '<td><input type="text" name="'+prefix+'['+site+']" value="" /></td>';
		row += '</tr>';

		var $row = $(row);

		$table.append( $row );
		$row.find('input').focus();
	}
	
	

	
	
});