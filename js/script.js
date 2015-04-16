(function($) {
	$(document).ready( function() {
		/*select tags*/
		$( "select.rlt_select" ).select2();
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
		if ( $( "#rlt_price" ).length > 0 ) {
			$( "#rlt_price" ).slider({
				range: true,
				min: parseInt( $( '#rlt_min_price' ).val().replace( '.', '' ) ),
				max: parseInt( $( '#rlt_max_price' ).val().replace( '.', '' ) ),
				values: [ parseInt( $( '#rlt_current_min_price' ).val().replace( ',', '' ) ), parseInt( $( '#rlt_current_max_price' ).val().replace( ',', '' ) ) ],
				slide: function( event, ui ) {
					$( '.rlt_min_price' ).text( rlt_number_format( ui.values[ 0 ], 0, '.', ',' ) );
					$( '.rlt_max_price' ).text( rlt_number_format( ui.values[ 1 ], 0, '.', ',' ) );
					$( '#rlt_min_price' ).val( ui.values[ 0 ] );
					$( '#rlt_max_price' ).val( ui.values[ 1 ] );
					$( '#rlt_current_min_price' ).val( ui.values[ 0 ] );
					$( '#rlt_current_max_price' ).val( ui.values[ 1 ] );
				}
			});
		}
		
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
		var speed = 400,
			thumbs_step = $( '.rlt_thumbnails' ).width(),
			thumbs_num = $( '.rlt_thumbnails img' ).size();
		var thumb_length = thumbs_step / ( thumbs_step / ( $( '.rlt_thumbnails img' ).outerWidth( true ) + 3 ) );
		var full_length = thumbs_num * thumb_length;
		var max_length = full_length - thumbs_step;
		$( '#rlt_thumbnails_holder' ).width( full_length );
		$( '.rlt_home_slides .prev, .rlt_home_slides .next' ).addClass( 'disabled' );
		if ( full_length > thumbs_step )
			$('.rlt_home_slides .next').removeClass('disabled');
		$( '.rlt_home_slides .rlt_thumbnails img' ).click( function() {
			$( '.rlt_home_content_tab .home_image img' ).attr( 'src', $( this ).attr( 'rel' ) );
		})
		$( '.rlt_home_slides .prev, .rlt_home_slides .next' ).click( function() {
			var curr_pos = parseInt( $( '.rlt_home_slides .rlt_thumbnails #rlt_thumbnails_holder' ).css( 'margin-left' ) );
			if ( $( this ).hasClass( 'next' ) && ! $( this ).hasClass( 'disabled' ) ) {
				$( '.rlt_home_slides .prev' ).removeClass( 'disabled' );
				if ( ( curr_pos-thumbs_step ) <= ( -max_length ) ) {
					$( '.rlt_home_slides .rlt_thumbnails #rlt_thumbnails_holder' ).animate({ 'margin-left': - max_length + 'px' }, speed );
					$( this ).addClass( 'disabled' );
				} else {
					$( '.rlt_home_slides .rlt_thumbnails #rlt_thumbnails_holder' ).animate({ 'margin-left': curr_pos - thumbs_step + 'px' }, speed );
				}
			} else if ( $( this ).hasClass( 'prev' ) && ! $( this ).hasClass( 'disabled' ) ) {
				$( '.rlt_home_slides .next').removeClass( 'disabled' );
				if ( ( curr_pos + thumbs_step ) >= 0 ) {
					$( '.rlt_home_slides .rlt_thumbnails #rlt_thumbnails_holder' ).animate({ 'margin-left': '0px' }, speed );
					$( this ).addClass( 'disabled' );
				} else {
					$( '.rlt_home_slides .rlt_thumbnails #rlt_thumbnails_holder' ).animate({ 'margin-left': curr_pos + thumbs_step + 'px' }, speed );
				}
			}
		});

		/* search results */
		var count_preview_block = $( '#rlt_home_preview .rlt_home_preview' ).length;
		if ( count_preview_block > 1 ) {
			var parent_block_width = $( '#rlt_home_preview' ).width();
			var preview_block_width = $( '#rlt_home_preview .rlt_home_preview' ).outerWidth( true );
			var count_in_row = parseInt( parent_block_width / preview_block_width );
			var current_preview = 0;
			var all_preview = 0;
			var max_row_height = 0;
			var current_height = 0;
			$( '#rlt_home_preview .rlt_home_preview' ).each( function(){
				current_preview += 1;
				all_preview += 1;
				current_height = $( this ).height();
				if ( $( '#page #sidebar.sidebar + #content' ).length > 0 && 'MozAppearance' in document.documentElement.style ) {
					current_height += 15;
				}
				if ( current_preview == 1 ) {
					$( this ).addClass( 'first' );
					max_row_height = current_height;
				} else if ( current_preview > 1 && current_preview < count_in_row && all_preview < count_preview_block ) {
					if ( current_height > max_row_height ) {
						max_row_height = current_height;						
					}
				} else {
					if ( current_height > max_row_height ) {
						max_row_height = current_height;
					}
					var i = current_preview;
					var current_preview_block = $( this );
					$( this ).height( max_row_height );
					
					while ( i > 0 ) {
						current_preview_block = $( current_preview_block ).prev();
						if ( $( current_preview_block ).hasClass( 'rlt_home_preview' ) )
							$( current_preview_block ).height( max_row_height );
						if ( $( current_preview_block ).hasClass( 'first' ) )
							break;
						i--;
					}
					max_row_height = 0;
					current_preview = 0;
				}
				if ( $( '#page #sidebar.sidebar + #content' ).length > 0 && ( 'MozAppearance' in document.documentElement.style ) !== true ) {
					var current_preview_block = $( this );
					current_preview_block.hover( function() {
						$( current_preview_block ).height( $( current_preview_block ).height() + 10 );
					}, function(){
						$( current_preview_block ).height( $( current_preview_block ).height() - 10 );
					});
				}
			});
			
		}
		if ( $( '.twentyfifteen #content' ).length > 0 && 'MozAppearance' in document.documentElement.style ) {
			$( '.twentyfifteen #content .rlt_home_full_wrapper #rlt_home_preview .rlt_home_preview' ).hover( function() {
				$( this ).css( 'width', '210px');
			});
		}

		$( '#property_sale_search_form input[type="submit"], #property_rent_search_form input[type="submit"]' ).click( function() {
			var action = '';
			var form_id = $( this ).parent().parent().attr( 'id' );
			if ( rlt_translation.rlt_permalink == '' ) {
				if ( $( '#'+form_id+' #rlt_location' ).val() != '' )
					action = action + '&property_location=' + encodeURI( $( '#'+form_id+' #rlt_location' ).val().replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") );
				if ( $( '#'+form_id+' .property option:selected' ).val() != '' )
					action = action + '&property_type=' + encodeURI( $( '#'+form_id+' .property option:selected' ).val().replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") );
				else
					action = action + '&property_type=all';
				if ( $( '#'+form_id+' #rlt_min_price' ).length > 0 && $( '#' + form_id + ' #rlt_min_price' ).val() != '' )
					action = action + '&property_min_price=' + $( '#' + form_id + ' #rlt_current_min_price' ).val();
				if ( $( '#'+form_id+' #rlt_min_price' ).length > 0 && $( '#' + form_id + ' #rlt_max_price' ).val() != '' )
					action = action + '&property_max_price=' + $( '#' + form_id + ' #rlt_current_max_price' ).val();
				if ( $( '#'+form_id+' .bathrooms option:selected' ).val() != '' )
					action = action + '&property_bath=' + $( '#'+form_id+' .bathrooms option:selected' ).val();
				else
					action = action + '&property_bath=1';
				if ( $( '#'+form_id+' .bedrooms option:selected' ).val() != '' )
					action = action + '&property_bed=' + $( '#'+form_id+' .bedrooms option:selected' ).val();
				else
					action = action + '&property_bed=1';
				if ( $( '#'+form_id+' #rlt_type_id' ).val() != '' )
					action = action + '&property_typeid=' + $( '#'+form_id+' #rlt_type_id' ).val();
				action = '?post_type=property&s=properties&property_search_results=1' + action + '&property_sortby=newest';
			} else {
				action = '/';
				if ( $( '#'+form_id+' #rlt_location' ).val() != '' )
					action = action + 'loc-' + encodeURI( $( '#'+form_id+' #rlt_location' ).val().replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") ) + '/';
				if ( $( '#'+form_id+' .property option:selected' ).val() != '' )
					action = action + 'prop-' + encodeURI( $( '#'+form_id+' .property option:selected' ).val().replace(/(<([^>]+)>)/ig,"").replace(/\\/,"") ) + '/';
				else
					action = action + 'prop-all/';
				if ( $( '#'+form_id+' #rlt_min_price' ).length > 0 && $( '#'+form_id+' #rlt_min_price' ).val() != '' )
					action = action + 'minp-' + $( '#'+form_id+' #rlt_current_min_price' ).val() + '/';
				if ( $( '#'+form_id+' #rlt_min_price' ).length > 0 && $( '#'+form_id+' #rlt_max_price' ).val() != '' )
					action = action + 'maxp-' + $( '#'+form_id+' #rlt_current_max_price' ).val() + '/';
				if ( $( '#'+form_id+' .bathrooms option:selected' ).val() != '' )
					action = action + 'bath-' + $( '#'+form_id+' .bathrooms option:selected' ).val() + '/';
				else
					action = action + 'bath-1/';
				if ( $( '#'+form_id+' .bedrooms option:selected' ).val() != '' )
					action = action + 'bed-' + $( '#'+form_id+' .bedrooms option:selected' ).val() + '/';
				else
					action = action + 'bed-1/';
				if ( $( '#'+form_id+' #rlt_type_id' ).val() != '' )
					action = action + 'type-' + $( '#'+form_id+' #rlt_type_id' ).val() + '/';
				action = action + 'sort-newest/';
			}
			$( '#'+form_id ).attr( 'action', $( '#'+form_id ).attr( 'action' ) + action );
			$( '#'+form_id ).attr( 'method', 'post' );
			$( '#'+form_id ).submit();
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
