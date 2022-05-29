(function( $ ) {
    $( document ).on('load ready', function() {
        var checkboxValue = $('.customize-control-font-style input[type="checkbox"]');
        var fonts = nb_customize_typography.google_fonts;

        $('.customize-control-font-style input[type="checkbox"]:checked').addClass('active');
        checkboxValue.on('change', function() {
            $(this).toggleClass('active');
        });

        // var showWeightOption = function() {
        //     var select = $('.customize-control-typography');
        //
        //     select.each(function() {
        //         var val = $(this).find('.chosen-single').text();
        //
        //         $(this).find('select[name="fonts-weight"] option').each(function() {
        //             var value = $(this).attr("value");
        //             if(fonts[val].indexOf(value) > -1) {
        //                 $(this).show();
        //             } else {
        //                 $(this).hide();
        //             }
        //         });
        //     })
        //
        // };
        // showWeightOption();

        // $('.customize-control-typography select[name="google-fonts-select"]').on('change', function() {
        //     var val = $(this).val();
        //     var multiple = $(this).siblings('.customize-control-checkbox-multiple');
        //     multiple.find('select[name="fonts-weight"]').val(400);
        //     multiple.find('option').each(function() {
        //         var value = $(this).attr("value");
        //         if(fonts[val].indexOf(value) > -1) {
        //             $(this).show();
        //         } else {
        //             $(this).hide();
        //         }
        //     });
        // });
        //
        $('.customize-control-font-style input[type="checkbox"], .customize-control-font-style select' ).on(
            'change',
            function() {
                fontWeight = $(this).closest('.customize-control-font-style').find('select[name="fonts-weight"]').val();
                values = $( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
                    function() {
                        return this.value;
                    }
                ).get().join( ',' );
                if(values) {
                    values = values + ',' + fontWeight;
                } else {
                    values = fontWeight;
                }

                $( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( values ).trigger( 'change' );
            }
        );

    } );


})( jQuery );

