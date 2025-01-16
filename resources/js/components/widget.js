(function () {
    "use strict";
    let inputNumeric = $('.input-numeric');

    inputNumeric.each(function () {
        let input = $(this);
        let spanClear = $('#' + input.attr('id') + '-clear');

        spanClear.on('click', function () {
            spanClear.removeClass('active');
            input.val('');
        });
        input.on('keyup', function () {
            _toggleClear(input, spanClear);
        });
        input.bind('focus', function () {
            _toggleClear(input, spanClear);
        });
    });

    function _toggleClear(_input, _span) {
        if (_input.val().length === 0) {
            _span.removeClass('active');
        } else {
            _span.addClass('active');
        }
    }

    $('.variant-image-input').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('active');
        } else {
            $(this).parent().removeClass('active');
        }
    });


})();
