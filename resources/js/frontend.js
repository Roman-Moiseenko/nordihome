import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";

    /* От Заказчика*/
    //Кол-во столбцов в меню 1 - для маленьких, 3 для огромных - дублируется во _shop-l-classes.scss
    const countColSubMenu = 1;
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

    //Каталог
    let catalog = $('.catalog');
    let catalogParentItems = $('.dropdown-item');
    let catalogSubmenu = $('#catalog-submenu');
    _updateSubMenu(catalogParentItems.first());

    catalogParentItems.hover(function () {
        catalogParentItems.removeClass('active');
        _updateSubMenu($(this));
    }, function () {
        return false;
    });

    function _updateSubMenu(element) {
        element.addClass('active');

        $.post(catalog.data('route'), {
                category: element.data('id')
            },
            function (data) {
                //console.log(data);
                catalogSubmenu.html('');
                if (data.length > 0) {
                    for (let i = 0; i < data.length; i++) {
                        let _item = data[i];
                        catalogSubmenu.append(_subFirst_a(_item));
                        if (_item.children.length > 0) catalogSubmenu.append(_subSecond_div(_item.children));
                    }
                    catalogSubmenu.show();
                } else {
                    catalogSubmenu.hide();
                }
            });
    }

    function _subFirst_a(item, level = 1) {
        let _class, _count = '';
        if (level === 1) _class = 'submenu-first-level';
        if (level === 2) {
            _class = 'submenu-second-level';
            if (Number(item.products > 0)) _count = '<span> ' + item.products + '</span>';
        }
        return '<a href="' + item.url +'" class="' + _class + '">' + item.name + _count + '</a>'
    }

    function _subSecond_div(items) {
        let html = '', sub_html = '';
        let n = 0;

        let countItems = items.length;
        let col;// = Math.ceil(countItems/countColSubMenu);

        for (let j = 1; j <= countColSubMenu; j++) {
            col = Math.ceil((countItems - n)/(countColSubMenu - j + 1))

            sub_html = '';
            for (let i = 0; i < col; i++) {
                if (items[n] !== undefined) sub_html = sub_html + _subFirst_a(items[n], 2) + ' <br>';
                n++;
            }
            html = html + '<div class="submenu-second-level-column">' + sub_html + '</div>';
        }
        return '<div class="submenu-second-level-div">' + html + '</div>';
    }


})();

