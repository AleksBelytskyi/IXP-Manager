<script>
    $( document ).ready( function() {
        $( "#list-area").show();
    });

    /**
     * on click even allow to add a mac address using prompt popup
     */
    $('#add-l2a' ).on( 'click', function( e ) {
        e.preventDefault();

        bootbox.prompt( {
            title: "Enter a MAC Address.",
            inputType: 'text',
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className: 'btn-secondary'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Create'
                }
            },
            callback: function ( result ) {
                if( result != null ) {
                    $.ajax( "<?= route ( 'l2-address@create', [ 'showFeMessage' => true ] ) ?>", {
                        type: 'POST',
                        data: {
                            vlan_interface_id : <?= $t->vli->id ?>,
                            mac : result,
                            _token : "<?= csrf_token() ?>"
                        }
                    })
                    .done( function() {
                        location.reload();
                    })
                    .fail( function() {
                        alert( `Couldn't add MAC address. API / AJAX / network error` );
                        throw new Error("Error running ajax query for <?= route ( 'l2-address@create' ) ?>");
                    });
                }
            }
        });
    });

    /**
     * function to delete a mac address using a confirm popup
     */
    $( '.btn-2f-list-delete' ).on( 'click', function( event ) {
        event.preventDefault();

        let url = $( this ).attr( 'data-url');
        bootbox.confirm({
            message: "Do you really want to delete this MAC Address?",
            buttons: {
                confirm: {
                    label: '<i class="fa fa-check"></i> Delete',
                    className: 'btn-danger'
                },
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {
                if( result) {
                    $.ajax( url , {
                        type : 'DELETE'
                    } )
                    .done( function() {
                        location.reload();
                    })
                    .fail( function(){
                        alert( `Couldn't add MAC address. API / AJAX / network error` );
                        throw new Error("Error running ajax query for api/v4/l2-address/{id}/delete");
                    })
                }
            }
        });
    });
</script>
