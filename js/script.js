(function($) {
	$(document).ready( function() {
		/*select tags*/
		if( ! rlt_translation.realestate_active ) {
			$( "select.rlt_select" ).select2();
		}
		/*tabs*/
		$( '.rlt_tabs .tab' ).click( function() {
			if ( ! $( this ).hasClass( 'active' ) ) {
				var new_active = $( this );
				var last_active = new_active.parent().find( '.active' );
				last_active.removeClass( 'active' );
				var last_active_num = last_active.attr( 'class' ).substr( ( last_active.attr( 'class' ).length-1 ), 1 );
				var new_active_num = new_active.attr( 'class' ).substr( ( new_active.attr( 'class' ).length-1 ), 1 );
				new_active.addClass( 'active' );
				$( new_active ).parent().parent().find( ".rlt_tab_block_" + last_active_num ).hide();
				$( new_active ).parent().parent().find( ".rlt_home_content_tab" ).removeClass( 'active' ).hide();
				$( new_active ).parent().parent().find( ".rlt_tab_block_" + new_active_num ).show();
				$( new_active ).parent().parent().find( ".rlt_home_content_" + new_active_num ).show().addClass( 'active' ).css( 'z-index', '1' );
			}
		})

		/*shadow*/
		var shade = $( '.rlt_tab_block' ).css( 'box-shadow' );
		$( '.rlt_tab_block' ).css( 'position', 'relative' );
		$( '.rlt_home_content_tab' ).css( 'position', 'relative' );

		/*dragging*/
		$( ".rlt_prices" ).each( function() {
			var this_slider = $( this );
			var min_price = this_slider.siblings( '#rlt_min_price' );
			var max_price = this_slider.siblings( '#rlt_max_price' );
			var current_min_price = this_slider.siblings( '#rlt_current_min_price' );
			var current_max_price = this_slider.siblings( '#rlt_current_max_price' );
			if ( this_slider.find( '#rlt_price' ).length > 0 ) {
				this_slider.find( '#rlt_price' ).slider({
					range: true,
					min: min_price.val() * 1000,
					max: max_price.val() * 1000,
					values: [ current_min_price.val() * 1000, current_max_price.val() * 1000 ],
					slide: function( event, ui ) {
						this_slider.find( '.rlt_min_price' ).text( rlt_number_format( ui.values[ 0 ] / 1000 ) );
						this_slider.find( '.rlt_max_price' ).text( rlt_number_format( ui.values[ 1 ] / 1000 ) );
						min_price.val( ui.values[ 0 ] / 1000 );
						max_price.val( ui.values[ 1 ] / 1000 );
						current_min_price.val( ui.values[ 0 ] / 1000 );
						current_max_price.val( ui.values[ 1 ] / 1000 );
					}
				});
			}
		});

		/* Placeholder for IE */
		if ( $.browser.msie ) {
			var color = $( 'input' ).css( 'color' );
			$( "form" ).find( "input[type='text'], input[type='password'], input[type='email'], textarea" ).each( function() {
				var tp = $( this ).attr( "placeholder" );
				$( this ).attr( 'value',tp ).css( 'color', color );
			} ).focusin( function() {
		 		var val = $( this ).attr( 'placeholder' );
				if ( $( this ).val() == val ) {
					$( this ).attr( 'value', '' ).css( 'color', color );
				}
			} ).focusout( function() {
				var val = $( this ).attr( 'placeholder' );
				if ( $( this ).val() == "" ) {
					$( this ).attr( 'value', val ).css( 'color', color );
				}
			} );
			/* Protected send form */
			$( "form" ).submit( function() {
				$( this ).find( "input[type='text'], input[type='password'], input[type='email'], textarea" ).each( function() {
					var val = $( this ).attr( 'placeholder' );
					if ( $( this ).val() == val ) {
						$( this ).attr( 'value', '' );
					}
				} )
			} );
		}

		/* property single image slider*/
		$('#rlt_thumbnails_holder').slick({
			slidesToShow: 3,
			slidesToScroll: 1,
			dots: false,
			centerMode: true,
			focusOnSelect: true

		}).on( 'afterChange', function( slick, currentSlide ) {
			var src = $( '#rlt_thumbnails_holder .slick-current' ).attr( 'rel' ),
				image = $( '.home_image img' );
			image.attr( 'src', src ).attr( 'srcset', src );

		});

		$( '#rlt_thumbnails_holder .slick-slide' ).on( 'click', function(){
		var src = $( '#rlt_thumbnails_holder .slick-current' ).attr( 'rel' ),
			image = $( '.home_image img' );
			image.attr( 'src', src ).attr( 'srcset', src );
		});

		/* search results */
		var count_preview_block = $( '#rlt_home_preview .rlt_home_preview' ).length;
		if ( count_preview_block > 1 ) {
			$( window ).resize( function() {
				rlt_resize_changes();
			}).trigger( 'resize' );
		}
		$(window).load( function() {
			rlt_resize_changes();
		} );


		if ( $( '.rlt_twentyfifteen #content' ).length > 0 && 'MozAppearance' in document.documentElement.style ) {
			$( '.rlt_twentyfifteen #content .rlt_home_full_wrapper #rlt_home_preview .rlt_home_preview' ).hover( function(){
				$( this ).css( 'width', '210px');
			});
		}

		if( $( '.rlt_twentythirteen #colophon .widget-area' ).length > 0 && $( '.widget_realty_recent_items_widget' ).length > 0 ) {
			var height_widget = $( '.widget_realty_recent_items_widget' ).height();
			var height_block = $( '.rlt_twentythirteen #colophon .widget-area' ).height();
			if( height_block < height_widget ) {
				$( '.rlt_twentythirteen #colophon .widget-area' ).css( { 'min-height' : height_widget + 30 } );
			}
		}

		var tab_block_count = 1;
		$( '.rlt_tab_block' ).each( function() {
			$( this ).addClass( 'rlt_tab_block_count_' + tab_block_count );
			tab_block_count ++;
		} );

		$( '#property_sale_search_form input[type="submit"], #property_rent_search_form input[type="submit"]' ).click( function() {
			var action = '';
			var tab = $( this ).parent().parent().parent().attr( 'class' );
			tab = tab.slice( tab.search( 'rlt_tab_block_count_' ) );
			var property_type = $( '.'+tab+' .property option:selected' ).val();
			if ( rlt_translation.rlt_permalink == '' ) {
				if ( $( '.'+tab+' #rlt_location' ).val() != '' )
					action = action + '&property_location=' + encodeURI( $( '.'+tab+' #rlt_location' ).val().replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") );
				if ( '' != property_type && 'all' != property_type )
					action = action + '&property_type=' + encodeURI( property_type.replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") );
				if ( $( '.'+tab+' #rlt_min_price' ).length > 0 && $( '.' + tab + ' #rlt_min_price' ).val() != '' )
					action = action + '&property_min_price=' + $( '.' + tab + ' #rlt_current_min_price' ).val();
				if ( $( '.'+tab+' #rlt_min_price' ).length > 0 && $( '.' + tab + ' #rlt_max_price' ).val() != '' )
					action = action + '&property_max_price=' + $( '.' + tab + ' #rlt_current_max_price' ).val();
				if ( $( '.'+tab+' .bathrooms option:selected' ).val() != '' )
					action = action + '&property_bath=' + $( '.'+tab+' .bathrooms option:selected' ).val();
				else
					action = action + '&property_bath=1';
				if ( $( '.'+tab+' .bedrooms option:selected' ).val() != '' )
					action = action + '&property_bed=' + $( '.'+tab+' .bedrooms option:selected' ).val();
				else
					action = action + '&property_bed=1';
				if ( $( '.'+tab+' #rlt_info_type' ).val() != '' )
					action = action + '&property_type_info=' + $( '.'+tab+' #rlt_info_type' ).val();
				action = '?post_type=property&s=properties&property_search_results=1' + action + '&property_sortby=newest';
			} else {
				action = '/';
				if ( $( '.'+tab+' #rlt_location' ).val() != '' )
					action = action + 'loc-' + encodeURI( $( '.'+tab+' #rlt_location' ).val().replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") ) + '/';
				if ( '' != property_type && 'all' != property_type )
					action = action + 'prop-' + encodeURI( property_type.replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") ) + '/';
				if ( $( '.'+tab+' #rlt_min_price' ).length > 0 && $( '.'+tab+' #rlt_min_price' ).val() != '' )
					action = action + 'minp-' + $( '.'+tab+' #rlt_current_min_price' ).val() + '/';
				if ( $( '.'+tab+' #rlt_min_price' ).length > 0 && $( '.'+tab+' #rlt_max_price' ).val() != '' )
					action = action + 'maxp-' + $( '.'+tab+' #rlt_current_max_price' ).val() + '/';
				if ( $( '.'+tab+' .bathrooms option:selected' ).val() != '' )
					action = action + 'bath-' + $( '.'+tab+' .bathrooms option:selected' ).val() + '/';
				else
					action = action + 'bath-1/';
				if ( $( '.'+tab+' .bedrooms option:selected' ).val() != '' )
					action = action + 'bed-' + $( '.'+tab+' .bedrooms option:selected' ).val() + '/';
				else
					action = action + 'bed-1/';
				if ( $( '.'+tab+' #rlt_info_type' ).val() != '' )
					action = action + 'type-' + $( '.'+tab+' #rlt_info_type' ).val() + '/';
				action = action + 'sort-newest/';
			}
			$( '.'+tab+' form' ).attr( 'action', $( '.'+tab+' form' ).attr( 'action' ) + action );
			$( '.'+tab+' form' ).attr( 'method', 'post' );
			$( '.'+tab+' form' ).submit();
			return false;
		});
	});
})(jQuery);

function rlt_number_format( number, decimals, dec_point, thousands_sep ) {
  /*  discuss at: http://phpjs.org/functions/number_format/
 original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
 improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 improved by: davook
 improved by: Brett Zamir (http://brett-zamir.me)
 improved by: Brett Zamir (http://brett-zamir.me)
 improved by: Theriault
 improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 bugfixed by: Michael White (http://getsprink.com)
 bugfixed by: Benjamin Lupton
 bugfixed by: Allan Jensen (http://www.winternet.no)
 bugfixed by: Howard Yeend
 bugfixed by: Diogo Resende
 bugfixed by: Rival
 bugfixed by: Brett Zamir (http://brett-zamir.me)
  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  revised by: Luke Smith (http://lucassmith.name)
    input by: Kheang Hok Chin (http://www.distantia.ca/)
    input by: Jay Klehr
    input by: Amir Habibi (http://www.residence-mixte.com/)
    input by: Amirouche
   example 1: number_format(1234.56);
   returns 1: '1,235'
   example 2: number_format(1234.56, 2, ',', ' ');
   returns 2: '1 234,56'
   example 3: number_format(1234.5678, 2, '.', '');
   returns 3: '1234.57'
   example 4: number_format(67, 2, ',', '.');
   returns 4: '67,00'
   example 5: number_format(1000);
   returns 5: '1,000'
   example 6: number_format(67.311, 2);
   returns 6: '67.31'
   example 7: number_format(1000.55, 1);
   returns 7: '1,000.6'
   example 8: number_format(67000, 5, ',', '.');
   returns 8: '67.000,00000'
   example 9: number_format(0.9, 0);
   returns 9: '1'
  example 10: number_format('1.20', 2);
  returns 10: '1.20'
  example 11: number_format('1.20', 4);
  returns 11: '1.2000'
  example 12: number_format('1.2000', 3);
  returns 12: '1.200'
  example 13: number_format('1 000,50', 2, '.', ' ');
  returns 13: '100 050.00'
  example 14: number_format(1e-8, 8, '.', '');
  returns 14: '0.00000001' */

	number = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
	toFixedFix = function(n, prec) {
		var k = Math.pow(10, prec);
		return '' + ( Math.round(n * k) / k ).toFixed(prec);
	};
	/*Fix for IE parseFloat(0.55).toFixed(0) = 0; */
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}

/**
* Function for different window sizes
**/
function rlt_resize_changes() {
	(function( $ ) {
		var rlt_home_preview = 0;
		var rlt_home_info = 0;

		var width = $( '.rlt_widget_content' ).width();
		if( 0 != width && width <= 385 ) {
			$( '#rlt_home_preview .rlt_home_preview' ).css( 'width', width );
		}
		$( '#rlt_home_preview .rlt_home_preview .rlt_home_info' ).each( function () {
			$( this ).css( 'height', 'auto' );
			var height_block = parseInt( $(this).height() );
			if ( height_block > rlt_home_info ) {
				rlt_home_info = height_block;
			}
		});


		$( '#rlt_home_preview .rlt_home_preview' ).each( function () {
			$( this ).css( 'height', 'auto' );
			var height_block = parseInt( $(this).height() );
			if ( height_block > rlt_home_preview ) {
				rlt_home_preview = height_block + 25;
			}
		});

		if ( $( window ).width() >= 405 ) {
			$( '#rlt_home_preview .rlt_home_preview' ).css( 'height', rlt_home_preview );
			$( '#rlt_home_preview .rlt_home_preview .rlt_home_info' ).css( 'height', rlt_home_info );

		}

	})( jQuery );
};