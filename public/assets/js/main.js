(function($) {
    "use strict";

    if ($('.inputusername').val() !== "") {
        $('.inputusername').addClass('has-val');
    }

    if ($('.inputpassword').val() !== "") {
        $('.inputpassword').addClass('has-val');
    }

    $('.inputusername').change(function() {
        if ($('.inputpassword').val() !== "") {
            $('.inputpassword').addClass('has-val');
        }
    });

    /*==================================================================
    [ Focus Contact2 ]*/
    $('.material-input').each(function() {
        $(this).on('blur', function() {
            if ($(this).val().trim() != "") {
                $(this).addClass('has-val');
            } else {
                $(this).removeClass('has-val');
            }
        })
    })


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .material-input');

    $('.validate-form').on('submit', function() {
        var check = true;

        for (var i = 0; i < input.length; i++) {
            if (validate(input[i]) == false) {
                showValidate(input[i]);
                check = false;
            }
        }

        return check;
    });


    $('.validate-form .material-input').each(function() {
        $(this).focus(function() {
            hideValidate(this);
        });
    });

    function validate(input) {
        if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        } else {
            if ($(input).val().trim() == '') {
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }


    $('.currency_format').on('keydown', function(e) {
        $('.currency_format').mask('000.000.000.000.000,-', {
            reverse: true
        });
    });

    $('.currency_format').on('change', function(e) {
        $('.currency_format').mask('000.000.000.000.000,-', {
            reverse: true
        });
    });



    $('#discount').on('keyup', function(e) {
        var disc = $('#discount').val() == null ? 0 : $('#discount').val();
        var discValue = $('#regular_price').cleanVal() - ($('#regular_price').cleanVal() * (disc / 100));
        $('#display_price').val($('#display_price').masked(discValue));
    });

    $('#regular_price').on('keyup', function(e) {
        var disc = $('#discount').val() == null ? 0 : $('#discount').val();
        var discValue = $('#regular_price').cleanVal() - ($('#regular_price').cleanVal() * (disc / 100));

        $('#display_price').val($('#display_price').masked(discValue));
    });

    $('#regular_price').on('change', function(e) {
        var disc = $('#discount').val() == null ? 0 : $('#discount').val();
        var discValue = $('#regular_price').cleanVal() - ($('#regular_price').cleanVal() * (disc / 100));

        $('#display_price').val($('#display_price').masked(discValue));
    });

    setTimeout(function() {
        $(".alert").fadeOut();
    }, 5000);

})(jQuery);

/* $('.currency_format').mask('000.000.000.000.000,-', {
    reverse: true
});

if ($('.currency_format').val() !== null) {
    $('.currency_format').val($('.currency_format').masked($('.currency_format').val()));
} */