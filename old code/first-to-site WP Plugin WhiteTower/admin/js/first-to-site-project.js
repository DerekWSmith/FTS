jQuery(document).ready(function($) {

    var wc_meta_boxes_order_notes = {
        init: function() {
            $( '.acf-field-61dcd1b8134d2' )
                .on( 'click', 'button.add_note', this.add_order_note )
                .on( 'click', 'button.clear_note', this.clear_order_note )
                .on( 'click', 'a.delete_note', this.delete_order_note )
                ;
        },
    
        add_order_note: function() {
            if ( ! $( 'textarea#add_order_note' ).val() ) {
                return;
            }
    
            $( '.acf-field-61dcd1b8134d2' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
    
            var data = {
                action:    'add_project_message_callback',
                post_id:   ajax.post_id,
                note:      $( 'textarea#add_order_note' ).val(),
                security:  ajax.add_project_message_nonce
            };
            // console.log(data);

            $.post( ajax.ajax_url, data, function( response ) {
                $( 'ul.order_notes .no-items' ).remove();
                $( 'ul.order_notes' ).prepend( response );
                $( '.acf-field-61dcd1b8134d2' ).unblock();
                $( '#add_order_note' ).val( '' );
            });
    
            return false;
        },
    
        delete_order_note: function() {
            if ( window.confirm( ajax.delete_note ) ) {
                var note = $( this ).closest( 'li.message' );
    
                $( note ).block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
    
                var data = {
                    action:   'delete_project_message_callback',
                    note_id:  $( note ).attr( 'data-rel' ),
                    security: ajax.delete_project_message_nonce
                };
                // console.log(data);
                $.post( ajax.ajax_url, data, function() {
                    $( note ).remove();
                });
            }
    
            return false;
        },

        clear_order_note: function() {
            $( '#add_order_note' ).val( '' );
            return false;
        }
    };

    wc_meta_boxes_order_notes.init();

});
