import jQuery from "jquery";
import common from "@/_common.js";

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

    const $body = $('body')

    $body.on('click', '.to-wish', function (e) {

        const button = $(this)
        const id = button.data('product')
        $.post('/cabinet/wish/toggle/' + id, {}, function (data) {
            button.removeClass('to-wish')
            button.addClass('is-wish')
        })
    })

    $body.on('click', '.is-wish', function (e) {

        const button = $(this)
        const id = button.data('product')
        $.post('/cabinet/wish/toggle/' + id, {}, function (data) {
            button.removeClass('is-wish')
            button.addClass('to-wish')
        })
    })

})();
