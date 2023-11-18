(function () {
    "use strict";
    console.log('Start Widget Component');
    $('.input-numeric').on('keyup', function () {
        let input = $(this);
        let spanClear = $('#' + input.attr('id') + '-clear');

        if (input.val().length === 0) {
            spanClear.removeClass('active');
        } else {
            spanClear.addClass('active');
        }
        spanClear.on('click', function () {
            spanClear.removeClass('active');
            input.val('');
        });
    });


})();
