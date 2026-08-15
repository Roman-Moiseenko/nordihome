import jQuery from "jquery";
import common from "@/_common.js";

window.$ = jQuery;

(function () {
    "use strict";
    $(document).ready(function () {
        // Проверяем наличие cookie "cookie.agreement"
        if (getCookie('cookie.agreement') === 'accepted') {
            return; // уже согласились — ничего не показываем
        }

        // HTML уведомления
        var html = '' +
            '<div>' +
            '  <p>Продолжая использовать сайт nordihome.ru Вы соглашаетесь на использование файлов cookie. ' +
            'Более подробную информацию можно прочитать в разделе ' +
            '<a href="/politika-obrabotki-personalnyh-dannyh/"><b>Политика конфиденциальности сайта</b></a></p>' +
            '  <button class="button cookie_accept">Принять</button>' +
            '</div>';

        // Через 2 секунды подставляем в контейнер
        setTimeout(function () {
            $('#cookie_notification').html(html).addClass('show');
        }, 3000);

        // Обработчик клика на кнопку "Принять" (делегирование)
        $('#cookie_notification').on('click', '.cookie_accept', function () {
            // Устанавливаем cookie на 1 год
            setCookie('cookie.agreement', 'accepted', 365);
            // Очищаем контейнер и скрываем
            $('#cookie_notification').empty().removeClass('show');
        });
    });

    // Функция получения cookie по имени
    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            '(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    // Функция установки cookie
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
    }

})();
