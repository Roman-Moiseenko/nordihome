import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";
    /**  ПОИСК в ТОП-МЕНЮ    ***/
        //INPUT поиска
    let presearchInput = $('#pre-search');
    let presearch = $('.presearch');
    let suggestBlock = $('.presearch-suggest');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


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
    presearchInput.on('keyup', function () {
        //ajax запрос
        $.post(presearch.data('route'), {
                search: presearchInput.val(),
            },
            function (data) {
                suggestBlock.html('');
                for (let i = 0; i < data.length; i++) {
                    suggestBlock.append(_itemSuggestPresearch(data[i]));
                }
            });
    });

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
        return '<a class="presearch-suggest-item" href="' + item.url + '">\n' +
            '   <span class="suggest--icon">' + img + '</span>\n' +
            '   <span class="suggest--label">' + name + '</span>\n' +
            '   <span class="suggest--price">' + price + '</span>\n' +
            '</a>'
    }


    //???
    $(document).on('click', function (e) {
        if ($(e.target).closest(".presearch").length) {
            return;
        }// клик снаружи элемента
        $('.presearch-overlay').hide();
        $('.presearch-suggest').hide();
    });


    //Кнопки в INPUT
    $('#presearch--icon-clear').on('click', function () {
        suggestBlock.html('');
        $('#pre-search').val('');
        $(this).hide();
    });

    let catalogParentItems = $('.dropdown-item');
    catalogParentItems.first().addClass('active');
    catalogParentItems.hover(function () {
        catalogParentItems.removeClass('active');
        $(this).addClass('active');
    });


    //Каталог


})();

