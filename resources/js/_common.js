import jQuery from "jquery";
window.$ = jQuery;

"use strict";
//Устанавливаем в сессию таймзону клиента
sessionStorage.setItem("time", -(new Date().getTimezoneOffset()));


//Запрашиваем csrf-token

let csrfMeta = $('meta[name="csrf-token"]')
if (csrfMeta.length) {
    $.post('/csrf-token', {}, function (data) {
        csrfMeta.attr('content', data)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': data
            }
        });
    })

}




//Доп.элементы
let upButton = $('#upbutton');
$(window).on('scroll', function () {
    if ($(this).scrollTop() > 100) {
        if (!upButton.hasClass('is-active')) {
            upButton.addClass('is-active');
        }
    } else {
        upButton.removeClass('is-active');
    }
    if ($(this).scrollTop() > 300) {
        $('.menu-bottom').addClass('sticky-menu');
    } else {
        $('.menu-bottom').removeClass('sticky-menu');
    }
    //
});
upButton.on('click', function () {
    $('html, body').stop().animate({scrollTop: 0}, 700);
});


//Показать скрыть пароль
let showHidePassword = $('#show-hide-password');
if (showHidePassword !== undefined) {
    let inputPassword = $(showHidePassword.data('target-input'));
    showHidePassword.on('click', function () {
        if (inputPassword.attr('type') === 'password') {
            inputPassword.attr('type', 'text');
        } else {
            inputPassword.attr('type', 'password');
        }
    });
}


const common = {
    csrf_token: {

    },

    notification(type, msg) {
        let notification = $('#notification');
        notification.find('.toast-body').html(msg);
        notification.find('.toast-header').addClass(type)
        notification.remove('hide');
        notification.addClass('show');
        notification.find('button[data-bs-dismiss=toast]').on('click', function () {
            notification.addClass('hide');
            notification.remove('show');
        });
    },
    //Отображение ошибок
    error(data) {
        if (data.error !== undefined) {
            if (Array.isArray(data.error)) {
                console.log(data.error);
            } else {
                this.notification('error', data.error)
            }
            return true;
        }
        return false;
    },
    //Валидация email
    isEmail(email) {
        let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    },
    //Валидация input/number
    inn_format(_num) {
        let regex = /^([0-9]{10,12})+$/;
        return regex.test(_num);
    },
    //Приведение числа в цену формата 1 000 000 ₽
    price_format(_str) {
        if (_str === null || _str === '') return '';
        return _str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '  ₽';
    },
}

export default common
