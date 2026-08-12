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


    /**  ПОИСК в ТОП-МЕНЮ    ***/
        //INPUT поиска
    let presearchInput = $('#pre-search');
    let presearch = $('.presearch');
    let suggestBlock = $('.presearch-suggest');

    // Восстанавливаем значение поискового запроса из URL при загрузке страницы
    (function () {
        let params = new URLSearchParams(window.location.search);
        let searchQuery = params.get('search');
        if (searchQuery) {
            presearchInput.val(decodeURIComponent(searchQuery));
            $('#presearch--icon-clear').show();
        }
    })();

    presearchInput.on('input', function () {
        if ($(this).val().length > 0) {
            $('#presearch--icon-clear').show();
        } else {
            $('#presearch--icon-clear').hide();
        }
    });
    presearchInput.on('focus', function () {
        $('.presearch-overlay').show();
        $('.presearch-suggest').show();
    });
    $('.presearch-overlay').on('click', function (e) {
        $('.presearch-overlay').hide();
        $('.presearch-suggest').hide();
    });
    //По таймеру - предотвращаем ajax при быстром наборе
    let timerInput;
    presearchInput.on('keyup', function () {
        timerInput = setTimeout(function () {
            $.post(presearch.data('route'), {search: presearchInput.val()},//ajax запрос
                function (data) {
                    common.error(data);
                    suggestBlock.html('');
                    if ($.isArray(data))
                        for (let i = 0; i < data.length; i++) {
                            suggestBlock.append(_itemSuggestPresearch(data[i]));
                        }
                });
        }, 180);
    });
    presearchInput.on('keydown', function (e) { //отменяем таймер при нажатии клавиши
        clearTimeout(timerInput);
        if (e.which === 13) {
            // Предотвращаем стандартное поведение (например, отправку формы)
            e.preventDefault();
            // Получаем значение поля, обрезаем пробелы по краям
            let value = $(this).val().trim();

            // Отправляем запрос только если длина >= 4
            if (value.length >= 4) {
                window.location.href = presearch.data('route') + '?search=' + encodeURIComponent(value);
            }
        }
    });



    //HTML построители
    function _itemSuggestPresearch(item) {
        let img = '<i class="fa-light fa-magnifying-glass"></i>';
        let price = item.price + ' ₽';
        let name = item.name;
        if (item.image !== '') {
            img = '<img class="" src="' + item.image + '"/>';
        }
        if (item.price === '') {
            name = '<strong>' + name + '</strong>'
            price = '';
        }

        const button = (item.code !== '') ? ('<button class="to-cart btn btn-black e-add" data-product="'+ item.id +'"><i class="fa-sharp fa-light fa-cart-plus"></i></button>') : '';
        return '<div class="presearch-suggest-item">' +
        '<a class="" href="' + item.url + '">\n' +
            '   <span class="suggest--icon">' + img + '</span>\n' +
            '   <span class="suggest--label">' + name + '</span>\n' +
            '   <span class="suggest--price">' + price + '</span>\n' +

            '</a>' + button + '</div>'
    }

    //Кнопки в INPUT
    $('#presearch--icon-clear').on('click', function () {
        suggestBlock.html('');
        $('#pre-search').val('');
        $(this).hide();
    });

})();
