/**
 * Metaphor Tabs
 * Date: 6/25/2014
 *
 * @author Metaphor Creations
 * @version 1.0.0
 *
 **/

( function($) {
	
	// Enabled strict mode
	"use strict";

	var methods = {

		init : function( options ) {

			return this.each( function(){

				// Create default options
				var settings = {
					anim_speed						: 1000,
					anim_ease							: 'easeOutExpo',
					after_load						: function(){}
				};

				// Add any set options
				if (options) {
					$.extend(settings, options);
				}

				var $wrapper = $(this),
						$links = $wrapper.find('.mtphr-tabs-links'),
						tabs = $links.find('a'),
						$container = $wrapper.find('.mtphr-tabs-content-container'),
						$container_inner = $wrapper.find('.mtphr-tabs-content-container-inner'),
						content = Array(),
						current = -1;

				// Move the content if tabs is used via shortcode
				if( $links.find('.mtphr-tabs-content').length > 0 ) {
					tabs.each( function(index) {
	
						$(this).children('a').attr('href', index);
						$container_inner.append( $(this).find('.mtphr-tabs-content') );
					});
				}

				// Save the content and update the colspan
				content = $container.find('.mtphr-tabs-content');
				$container.attr( 'colspan', $links.children().length );


				/**
				 * Display the content
				 *
				 * @since 1.0.1
				 */
				function mtphr_tabs_display_content( i ) {

					if( i != current ) {

						// Set the container height and fade out the old content
						if( current != -1 ) {
							var h = $(content[current]).outerHeight();
							$container_inner.css('height', h+'px');
							$(tabs[current]).removeClass('active');
							$(content[current]).css('position', 'absolute');
							$(content[current]).stop().fadeOut( settings.anim_speed );
						}

						$(tabs[i]).addClass('active');
						$(content[i]).stop().fadeIn( settings.anim_speed );

						// Save the current
						current = i;

						// Set the container height
						var h = $(content[i]).outerHeight(true);
						
						$container_inner.stop().animate( {
							height: h+'px'
						}, settings.anim_speed, settings.anim_ease, function() {

							// Set the position of the content
							$(this).removeAttr('style');
							$(content[current]).css('position', 'relative');
						});
					}
				}


				/**
				 * Add a click listener
				 *
				 * @since 1.0.0
				 */
				$links.find('a').click( function(e) {
					e.preventDefault();
					
					var index = $(this).attr('href');
					index = index.substr(1, index.length);
					mtphr_tabs_display_content( parseInt(index) );
				});

		    // Trigger the afterLoad callback
        settings.after_load.call(this, $wrapper);

        // Load the first tab
        setTimeout( function() {
        	mtphr_tabs_display_content( 0 );
        }, 500);
			});
		}
	};





	/**
	 * Setup the class
	 *
	 * @since 1.0.0
	 */
	$.fn.mtphr_tabs = function( method ) {

		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call(arguments, 1) );
		} else if ( typeof method === 'object' || !method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist in mtphr_tabs' );
		}
	};

})( jQuery );