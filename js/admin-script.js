(function($) {
	$(document).ready( function() {
		if ( $('.rlt_add_photo').length > 0 ) {
			if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
				$( '.wrap' ).on( 'click', '.rlt_add_photo', function(e) {
					e.preventDefault();
					var button = $( this );
					var id = button.prev();
					wp.media.editor.send.attachment = function(props, attachment) {
						/*id.val(attachment.id);*/
						var content = '<li id="'+attachment.id+'" class="rlt_image_block rlt_new">'+
							'<div class="rlt_drag">'+
								'<div class="rlt_image">'+
									'<img src="'+attachment.url+'" alt="'+attachment.title+'" width="150" />'+
								'</div>'+
								'<div class="rlt_delete"><a href="javascript:void(0);" onclick="img_delete('+attachment.id+');">'+rlt_translation.rlt_delete_image+'</a><div/>'+
							'</div>'+
						'</li>';
						$( '#rlt_gallery' ).append( content );
						$( '#rlt_add_images' ).append( '<input type="hidden" name="rlt_add_images[]" id="rlt_add_images_' + attachment.id + '" value="' + attachment.id + '" />' );
					};
					wp.media.editor.open( button );
					return false;
				});
			}
		};

		if ( $.fn.sortable ) {
			$( '#rlt_gallery.rlt-gallery' ).sortable();
		}

		$( '#rlt_currency' ).change( function() {
			$( '#rlt_currency_custom_display_false' ).attr( 'checked', 'checked' );
		});
		$( '#rlt_custom_currency' ).change( function() {
			$( '#rlt_currency_custom_display_true' ).attr( 'checked', 'checked' );			
		});
		$( '#rlt_unit_area' ).change( function() {
			$( '#rlt_unit_area_custom_display_false' ).attr( 'checked', 'checked' );
		});
		$( '#rlt_custom_unit_area' ).change( function() {
			$( '#rlt_unit_area_custom_display_true' ).attr( 'checked', 'checked' );			
		});
	});
})(jQuery);

function img_delete( id ) {
	(function($) {
		$( '#' + id ).hide();
		if ( $( '#rlt_add_images_' + id ).length > 0 )
			$( '#rlt_add_images_' + id ).remove();
		else
			$( '#rlt_delete_images' ).append( '<input type="hidden" name="rlt_delete_images[]" value="' + id + '" />' );
	})(jQuery);
}