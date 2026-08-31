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

    function feedbackNew(feedback) {
        let _id = feedback.attr('id');
        const route = '/feedback/form/feedback'

        let hideBlock = $('#' + _id + '-callback');
        let button = feedback.find('button');
        button.on('click', function () {
            let fields = {
                url: window.location.href,
                data: {},
            };
            let res = true;
            feedback.find('[name]').removeClass('field-error');

            feedback.find('[name]').each(function () {
                const $field = $(this);
                const type = $field.attr('type');
                const isRequired = $field.is('[required]');

                if (isRequired) {
                    let isValid = true;

                    if (type === 'checkbox' || type === 'radiobutton') {
                        if (!$field.is(':checked')) {
                            isValid = false;
                        }

                    } else {
                        if ($field.val() === '') {
                            isValid = false;
                        }
                    }
                    if (!isValid) {
                        $field.addClass('field-error');
                        res = false;
                        return;
                    }
                }
                // --- Сбор данных (только если поле валидно или не required) ---
                if (type === 'checkbox' || type === 'radiobutton') {
                    if ($field.is(':checked')) {
                        fields.data[$field.attr('name')] = $field.val();
                    }
                } else {
                    fields.data[$field.attr('name')] = $field.val();
                }
            });
            if (!res) alert('Не заполнены поля');
            if (res === true) {
                $.post(route, fields, function () {
                        hideBlock.show();
                        if (!feedback.is('[not-hide]')) feedback.hide()
                    }
                )
            }
        });
    }

    $(document).find('.feedback').each(function () {
        feedbackNew($(this));
    });

    $(document).find('.feedback-form').each(function () {
        feedbackNew($(this));
    });
})();
