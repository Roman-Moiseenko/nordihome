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
    const presearchInput = $('#pre-search');
    const presearch = $('.presearch');
    const suggestBlock = $('.presearch-suggest');

    const presearchIconClear = $('#presearch--icon-clear')
    const presearchOverlay = $('.presearch-overlay')
    const presearchSuggest = $('.presearch-suggest');

    // Восстанавливаем значение поискового запроса из URL при загрузке страницы
    (function () {
        let params = new URLSearchParams(window.location.search);
        let searchQuery = params.get('search');
        if (searchQuery) {
            presearchInput.val(searchQuery);
            presearchIconClear.show();
        }
    })();


    presearchInput.on('input', function () {
        if ($(this).val().length > 0) {
            presearchIconClear.show();
        } else {
            presearchIconClear.hide();
        }
    });
    presearchInput.on('focus', function () {
        overlay(true)
    });
    presearchOverlay.on('click', function (e) {
        overlay(false)
    });
    //По таймеру - предотвращаем ajax при быстром наборе
    let timerInput;
    presearchInput.on('keyup', function () {
        timerInput = setTimeout(function () {
            $.post(presearch.data('route'), {search: presearchInput.val()},//ajax запрос
                function (data) {
                    common.error(data);
                    suggestBlock.html('');
                    console.log(data)
                    if (data.products.length > 0) {
                        for (let i = 0; i < data.categories.length; i++) {
                            suggestBlock.append(_itemSuggestPresearch(data.categories[i]));
                        }

                        for (let i = 0; i < data.products.length; i++) {
                            suggestBlock.append(_itemSuggestPresearch(data.products[i]));
                        }
                        const _url = presearch.data('route') + '?search=' + encodeURIComponent(presearchInput.val())
                        suggestBlock.append('<a href="' + _url + '" class="btn btn-dark">Смотреть все результаты</a>');
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

    function overlay(show) {
        if (show) {
            presearchOverlay.show();
            presearchSuggest.show();
            $('body').addClass('no-scroll')
        } else {
            presearchOverlay.hide();
            presearchSuggest.hide();

            $('body').removeClass('no-scroll')
        }
    }

    //HTML построители
    function _itemSuggestPresearch(item) {
        let img = '<i class="fa-light fa-magnifying-glass"></i>';
        let price = item.price + ' ₽';
        let name = item.name;
        if (item.image !== null) {
            img = '<img class="" src="' + item.image + '"/>';
        }
        if (item.price === null) {
            name = '<strong>' + name + '</strong>'
            price = '';
        }

        const button = (item.code !== null) ? ('<button class="to-cart btn btn-small btn-black e-add" data-product="' + item.id + '"><i class="fa-sharp fa-light fa-cart-plus"></i></button>') : '';

        return '<div class="presearch-suggest-item">' +
            '<a class="" href="' + item.url + '">\n' +
            '   <span class="suggest--icon">' + img + '</span>\n' +
            '   <span class="suggest--label">' + name + '</span>\n' +
            '   <span class="suggest--price">' + price + '</span>\n' +

            '</a>' + button + '</div>'
    }

    //Кнопки в INPUT
    presearchIconClear.on('click', function () {
        suggestBlock.html('');
        $('#pre-search').val('');
        $(this).hide();
    });

})();
