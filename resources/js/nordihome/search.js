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

                    if (data.products.length > 0) {
                        for (let i = 0; i < data.categories.length; i++) {
                            suggestBlock.append(_itemSuggestPresearch(data.categories[i], 'category', i));
                        }

                        for (let i = 0; i < data.products.length; i++) {
                            suggestBlock.append(_itemSuggestPresearch(data.products[i], 'product', i));
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
    function _itemSuggestPresearch(item, type, position) {
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
            '<a class="js-search-result" href="' + item.url + '"' +
            ' data-result-type="' + type + '"' +
            ' data-result-id="' + item.id + '"' +
            ' data-result-position="' + position + '"' +
            ' data-search-query="' + _escapeAttr(presearchInput.val() || '') + '">\n' +
            '   <span class="suggest--icon">' + img + '</span>\n' +
            '   <span class="suggest--label">' + name + '</span>\n' +
            '   <span class="suggest--price">' + price + '</span>\n' +

            '</a>' + button + '</div>'
    }

    function _escapeAttr(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Клик по результату поиска: фиксируем строку поиска и выбор на сервере,
    // не блокируя переход по ссылке.
    $(document).on('click', '.js-search-result', function () {
        const $el = $(this);
        const payload = {
            query: $el.attr('data-search-query'),
            resultId: $el.attr('data-result-id'),
            resultType: $el.attr('data-result-type'),
            position: $el.attr('data-result-position'),
        };
        const body = JSON.stringify(payload);
        const url = window.ANALYTICS_SEARCH_CLICK_URL || '/analytics/track-search-click';

        // fetch + keepalive — надёжно уходит даже при переходе на другую страницу.
        if (window.fetch) {
            window.fetch(url, {
                method: 'POST',
                keepalive: true,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '',
                },
                body: body,
            });
            return;
        }

        if (navigator.sendBeacon) {
            navigator.sendBeacon(url, new URLSearchParams(payload));
            return;
        }

        $.post(url, payload);
    });

    //Кнопки в INPUT
    presearchIconClear.on('click', function () {
        suggestBlock.html('');
        $('#pre-search').val('');
        $(this).hide();
    });

})();
