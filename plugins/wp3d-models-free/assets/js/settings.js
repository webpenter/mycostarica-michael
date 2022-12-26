jQuery(document).ready(function($) {

    /***** Slug Change Alert *****/
    
    // get the current Single Slug value
    var wp3dOrigSlug;
    wp3dOrigSlug = $('#single_slug').val();
    
    $('#single_slug').focusout(function() {

        if (confirm("By changing this 'slug', you understand that all existing 'model' URLs will be updated and this may create broken links. Additionally, you need to visit your 'Settings -> Permalinks' page in order to flush your site's current rewrite rules.") == true) {
        } else { // if canceled, reset the value of the input to the previously stored value
            $('#single_slug').val(wp3dOrigSlug);
        }

      });
        

    /***** Color picker *****/

    $('.wp3d-colorpicker').hide();
    $('.wp3d-colorpicker').each( function() {
        $(this).farbtastic( $(this).closest('.wp3d-color-picker').find('.wp3d-color') );
    });

    $('.wp3d-color').click(function() {
        $(this).closest('.wp3d-color-picker').find('.wp3d-colorpicker').fadeIn();
    });

    $(document).mousedown(function() {
        $('.wp3d-colorpicker').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });


    /***** Uploading images *****/

    var file_frame;

    jQuery.fn.uploadMediaFile = function( button, preview_media ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( '_button', '' );
        var preview_id = button_id.replace( '_button', '_preview' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
          file_frame.open();
          return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: jQuery( this ).data( 'uploader_title' ),
          button: {
            text: jQuery( this ).data( 'uploader_button_text' ),
          },
          multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          attachment = file_frame.state().get('selection').first().toJSON();
          jQuery("#"+field_id).val(attachment.id);
          if( preview_media ) {
            jQuery("#"+preview_id).attr('src',attachment.sizes.thumbnail.url);
          }
          file_frame = false;
        });

        // Finally, open the modal
        file_frame.open();
    }

    jQuery('.image_upload_button').click(function() {
        jQuery.fn.uploadMediaFile( jQuery(this), true );
    });

    jQuery('.image_delete_button').click(function() {
        jQuery(this).closest('td').find( '.image_data_field' ).val( '' );
        jQuery(this).closest('td').find( '.image_preview' ).remove();
        return false;
    });

});