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
	
	$(document).on( 'widget-updated', function( e, widget) {
		
		$(widget).find('.mtphr-widgets-default-list').each( function(index) {
			mtphr_widgets_default_set_order( $(this) );
			mtphr_widgets_set_sortable( $(this) ) ;
		});
		
		$(widget).find('.metaphor-widgets-social-sites').each( function(index) {
			mtphr_widgets_social_set_sortable( $(this) );
		});
		
		var $advanced = $(widget).find('.mtphr-widget-advanced');
		if( $advanced.length > 0 ) {
			mtphr_widgets_advanced_fields( $advanced );
		}
		
	});

	
	
	/* --------------------------------------------------------- */
	/* !Social sites - 2.1.8 */
	/* --------------------------------------------------------- */
	
	function mtphr_widgets_social_set_sortable( $list ) {
		$list.sortable( {
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
	}

	$('.metaphor-widgets-social-icon').live('click', function(e) {
		e.preventDefault();

		var $table = $(this).parent().siblings('.metaphor-widgets-social-sites'),
				site = $(this).attr('href'),
				prefix = $(this).attr('data-prefix'),
				
		site = site.substr(1, site.length);

		if( $(this).hasClass('active') ) {
			$(this).removeClass('active');
			mtphr_widgets_social_settings_remove_icon( site, $table );
		} else {
			$(this).addClass('active');
			mtphr_widgets_social_settings_add_icon( site, $table, prefix );
		}
	});

	function mtphr_widgets_social_settings_remove_icon( site, $table ) {
		var $row = $table.find('.metaphor-widgets-social-'+site);
		$row.remove();
	}

	function mtphr_widgets_social_settings_add_icon( site, $table, prefix ) {
		var row = '<tr class="metaphor-widgets-social-site metaphor-widgets-social-'+site+'">';
		row += '<td class="metaphor-widgets-social-site-icon"><a tabindex="-1" href="#'+site+'"><i class="metaphor-widgets-ico-'+site+'"></i></a></td>';
		row += '<td><input type="text" name="'+prefix+'['+site+']" value="" /></td>';
		row += '</tr>';

		var $row = $(row);

		$table.append( $row );
		$row.find('input').focus();
	}
	
	mtphr_widgets_social_set_sortable( $('.metaphor-widgets-social-sites') );
	
	
	
	/* --------------------------------------------------------- */
	/* !Default list - 2.1.15 */
	/* --------------------------------------------------------- */
	
	function mtphr_widgets_default_handle_toggle( $table ) {
		if( $table.find('.mtphr-widgets-list-item').length > 1 ) {
			$table.find('.mtphr-widgets-list-handle').show();
			$table.find('.mtphr-widgets-list-delete').show();
		} else {
			$table.find('.mtphr-widgets-list-handle').hide();
			$table.find('.mtphr-widgets-list-delete').hide();
		}
	}

	function mtphr_widgets_default_set_order( $table ) {
		$table.find('.mtphr-widgets-list-item').each( function(index) {	
			$(this).find('textarea, input, select').each( function() {
				var prefix = $(this).attr('data-prefix'),
						key = $(this).attr('data-key');
				$(this).attr('name', prefix+'['+index+']['+key+']');
			});
		});
		
		mtphr_widgets_default_handle_toggle( $table );
	}
	
	function mtphr_widgets_default_init( $list ) {
		$list.each( function(index) {
			mtphr_widgets_default_set_order( $(this) );
		});
	}
	
	function mtphr_widgets_set_sortable( $list ) {
		$list.sortable( {
			handle: '.mtphr-widgets-list-handle',
			items: '.mtphr-widgets-list-item',
			axis: 'y',
		  helper: function(e, tr) {
		    var $originals = tr.children();
		    var $helper = tr.clone();
		    $helper.children().each(function(index) {
		      $(this).width($originals.eq(index).width());
		      $(this).height($originals.eq(index).height());
		    });
		    return $helper;
		  },
		});
	}
	
	// Delete list item
	$('.mtphr-widgets-default-list').find('.mtphr-widgets-list-delete').live( 'click', function(e) {
		e.preventDefault();
		
		var $table = $(this).parents('.mtphr-widgets-default-list');

		// Fade out the item
		$(this).parents('.mtphr-widgets-list-item').fadeOut( function() {
			$(this).remove();
			mtphr_widgets_default_set_order( $table );
			mtphr_widgets_default_handle_toggle( $table );
		});
	});
	
	// Add new row
	$('.mtphr-widgets-default-list').find('.mtphr-widgets-list-add').live( 'click', function(e) {
	  e.preventDefault();

	  // Save the container
	  var $table = $(this).parents('.mtphr-widgets-default-list'),
	  		$container = $(this).parents('.mtphr-widgets-list-item'),
	  		$new = $container.clone();
	  		
	  $new.find('textarea, input, select').each( function() {
			$(this).val('');
		});
		
		// Add the new row
		$container.after( $new );
		mtphr_widgets_default_set_order( $table );
		mtphr_widgets_default_handle_toggle( $table );
	});	
	
	mtphr_widgets_default_init( $('.mtphr-widgets-default-list') );
	mtphr_widgets_set_sortable( $('.mtphr-widgets-default-list') );

});