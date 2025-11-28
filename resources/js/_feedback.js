import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";
    //Устанавливаем в сессию таймзону клиента
    sessionStorage.setItem("time", -(new Date().getTimezoneOffset()));


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.feedback').each(function (element) {
        let feedback = $(this)
        let _id = feedback.attr('id');
        $.post('/admin/feedback/form/get/' + _id, {}, function (data) {
            let route = data;
            let hideBlock = $('#' + _id + '-callback');
            let button = feedback.find('button');
            button.on('click', function () {
                let fields = {
                    id: _id,
                    url: window.location.href,
                };
                let res = true;
                feedback.find('[name]').each(function (item) {
                    if ($(this).is(':required') && $(this).val() === '') {
                        if (res) alert('Не заполнены поля');
                        res = false;
                        return;
                    }
                    if ($(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radiobutton') {
                        if ($(this).is(':checked')) {
                            fields[$(this).attr('name')]= $(this).val();
                        }
                    } else {
                        fields[$(this).attr('name')]= $(this).val();
                    }
                });
                if (res === true) {
                    $.post(route, fields, function (data) {
                        hideBlock.show();
                        if (!feedback.is('[not-hide]')) feedback.hide()
                        }
                    )
                }
            });
        });
    });

})();

