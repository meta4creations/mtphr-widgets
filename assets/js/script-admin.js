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
	$('body').on( 'click', '.mtphr-widget-advanced', function(e) {
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

	$('body').on('click', '.metaphor-widgets-social-icon', function(e) {
		e.preventDefault();

		var $table = $(this).parent().siblings('.metaphor-widgets-social-sites'),
				site = $(this).attr('href'),
				name = $(this).attr('data-name'),
				
		site = site.substr(1, site.length);

		if( $(this).hasClass('active') ) {
			$(this).removeClass('active');
			mtphr_widgets_social_settings_remove_icon( site, $table );
		} else {
			$(this).addClass('active');
			mtphr_widgets_social_settings_add_icon( $(this), $table, name );
		}
	});

	function mtphr_widgets_social_settings_remove_icon( site, $table ) {
		
		var $widget = $table.parents('.widget-inside'),
				$save_btn = $widget.find('.widget-control-save');
		
		var $row = $table.find('.metaphor-widgets-social-'+site);
		$row.remove();
		
		$save_btn.removeAttr('disabled');
		$save_btn.attr('value', 'Save');
	}

	function mtphr_widgets_social_settings_add_icon( $link, $table, name ) {
		
		var href = $link.attr('href'),
				prefix = $link.attr('data-prefix'),
				id = $link.attr('data-id');
				
		href = href.substr(1, href.length);
		
		var row = '<tr class="metaphor-widgets-social-site metaphor-widgets-social-'+href+'">';
		row += '<td class="metaphor-widgets-social-site-icon"><a tabindex="-1" href="#'+href+'"><i class="'+prefix+' '+prefix+'-'+id+'"></i></a></td>';
		row += '<td><input type="text" name="'+name+'['+href+']" value="" /></td>';
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
				var name = $(this).attr('data-name'),
						key = $(this).attr('data-key');
				$(this).attr('name', name+'['+index+']['+key+']');
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
	$('body').on( 'click', '.mtphr-widgets-default-list .mtphr-widgets-list-delete', function(e) {
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
	$('body').on( 'click', '.mtphr-widgets-default-list .mtphr-widgets-list-add', function(e) {
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
	
	
	/**
	 * Widget added listener
	*/
	$( document ).on( 'widget-added', function( e, $widget ) {
		mtphr_widgets_default_init( $('.mtphr-widgets-default-list') );
		mtphr_widgets_set_sortable( $('.mtphr-widgets-default-list') );
		mtphr_widgets_social_set_sortable( $('.metaphor-widgets-social-sites') );
	} );

	
	
	
	var $icon, $input;
	
	$('body').on( 'click', '.mtphr-shortcodes-modal-link', function() {
		
		var $modal = $('#mtphr-widgets-icon-modal'),
				$widget = $(this).parents('.widget-content'),
				$table = $(this).siblings('.metaphor-widgets-social-sites'),
				$icon_container = $(this).siblings('.metaphor-widgets-social-icon-container'),
				name = $(this).attr('data-name');
				
		$modal.children('.mtphr-shortcodes-icon-select').removeClass('active');

		$modal.on( 'click', '.mtphr-shortcodes-icon-select', function(e) {
			e.preventDefault();

			var prefix = $(this).attr('data-prefix'),
					id = $(this).attr('data-id'),
					$submit = $('.mtphr-shortcodes-modal-submit');
			
			$submit.removeAttr('disabled');
			
			$submit.click( function() {
				
				if( $table.find('.metaphor-widgets-social-'+prefix+'__'+id).length ) {
					$table.find('.metaphor-widgets-social-'+prefix+'__'+id).find('input').focus();
				} else {
	
					$new_icon = $('<a class="metaphor-widgets-social-icon active" href="#'+prefix+'__'+id+'" data-name="'+name+'" data-prefix="'+prefix+'" data-id="'+id+'"><i class="'+prefix+' '+prefix+'-'+id+'"></i></a>');
					$icon_container.append( $new_icon );
					
					mtphr_widgets_social_settings_add_icon( $new_icon, $table, name );
					
					var data = {
						action: 'mtphr_widgets_save_icon',
						prefix: prefix,
						id: id,
						security: mtphr_widgets_vars.security
					};
					jQuery.post( ajaxurl, data, function( response ) {
					});
				}

			});
		});
	});

});