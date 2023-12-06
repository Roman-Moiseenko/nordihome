import jQuery from "jquery";
window.$ = jQuery;

(function () {
    "use strict";
    //Устанавливаем в сессию таймзону клиента
    sessionStorage.setItem("time", -(new Date().getTimezoneOffset()));
    let _counter = 0; //Кол-во товаров в корзине
    /* От Заказчика*/
    //Кол-во столбцов в меню 1 - для маленьких, 3 для огромных - дублируется во _shop-l-classes.scss
    const countColSubMenu = 1;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Проверяем корзину виджета при загрузке
    if ($('#cart-header').length) {
        setTimeout(function () {
            $.post('/cart_post/cart/', {tz: -(new Date().getTimezoneOffset())}, function (data) {
                widget_cart(data);


            });
        }, 250);


        $('#clear-cart').on('click', function () {
            let route = $(this).data('route');
            $.post(route,{tz: -(new Date().getTimezoneOffset())}, function (data) {
                widget_cart(data);
            })
        });
    }



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
    //По таймеру - предотвращаем ajax при быстром наборе
    let timerInput;
    presearchInput.on('keyup', function () {
        timerInput = setTimeout(function (){
            $.post(presearch.data('route'), {search: presearchInput.val()},//ajax запрос
                function (data) {
                    suggestBlock.html('');
                    if ($.isArray(data))
                    for (let i = 0; i < data.length; i++) {
                        suggestBlock.append(_itemSuggestPresearch(data[i]));
                    }
                });
        }, 180);

    });
    presearchInput.on('keydown', function (){ //отменяем таймер при нажатии клавиши
        clearTimeout(timerInput);
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
        return '<a class="presearch-suggest-item" href="' + item.url + '">\n' +
            '   <span class="suggest--icon">' + img + '</span>\n' +
            '   <span class="suggest--label">' + name + '</span>\n' +
            '   <span class="suggest--price">' + price + '</span>\n' +
            '</a>'
    }

    //Кнопки в INPUT
    $('#presearch--icon-clear').on('click', function () {
        suggestBlock.html('');
        $('#pre-search').val('');
        $(this).hide();
    });

    /** КАТАЛОГ В МЕНЮ **/
    let timer;
    let catalog = $('.catalog');
    let catalogParentItems = $('.dropdown-item');
    let catalogSubmenu = $('#catalog-submenu');
    catalogParentItems.first().addClass('active');
    _updateSubMenu(catalogParentItems.first());

    //Загрузка sub-menu при hover по пунктам меню 1го уровня
    catalogParentItems.hover(function () { //ф-ция для hoverIn
        catalogParentItems.removeClass('active'); //Удаляем метку выделенного пункта
        let ___item = $(this);
        timer = setTimeout(function () {//таймер закончился, удаляем метку активного элемента и запускаем обновление sub-menu
            catalogParentItems.removeClass('active-item');
            _updateSubMenu(___item);
        }, 180);
    }, function () {//ф-ция для hoverOut
        clearTimeout(timer);
        catalogParentItems.each(function (){//перебираем все пункты и для активного элемента навешиваем выделение пункта
            if ($(this).hasClass('active-item')) $(this).addClass('active');
        });
    });

    function _updateSubMenu(element) {
        element.addClass('active-item');
        $.post(catalog.data('route'), {
                category: element.data('id')
            },
            function (data) {
                catalogSubmenu.html('');
                if (data.length > 0) {
                    for (let i = 0; i < data.length; i++) {
                        let _item = data[i];
                        catalogSubmenu.append(_subFirst_a(_item));
                        if (_item.children.length > 0) catalogSubmenu.append(_subSecond_div(_item.children));
                    }
                    catalogSubmenu.parent().show();
                } else {
                    catalogSubmenu.parent().hide();
                }
            });
    }

    //HTML построители
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
        let col;

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

    //LOGIN POPUP
    let loginPopup = $('#login-popup');
    if (loginPopup.length) {
        let form = $('form#login-form');
        let buttonLogin = $('#button-login');
        let inputEmail = loginPopup.find('input[name="email"]');
        let inputPassword = loginPopup.find('input[name="password"]');
        let inputVerify = loginPopup.find('input[name="verify_token"]');
        inputVerify.parent().hide();
        buttonLogin.on('click', function () {
            if (inputEmail.val().length === 0 || inputPassword.val().length === 0 || !isEmail(inputEmail.val())) {
                form.addClass('was-validated');
                return true;
            }
            if (inputVerify.parent().is(':visible') && inputVerify.val().length === 0) {
                form.addClass('was-validated');
                return true;
            }


            $.post(
                '/login_register',
                {
                    email: inputEmail.val(),
                    password: inputPassword.val(),
                    verify_token: inputVerify.val()
                }, function (data) {
                    $('#token-error').hide();
                    $('#password-error').hide();

                    if (data.token === true) {
                        $('#token-error').show();
                    }

                    if (data.verification === true || data.register === true) { //требуется верификация
                        inputEmail.prop('disabled', true);
                        inputPassword.prop('disabled', true);
                        inputVerify.prop('required', true);
                        inputVerify.parent().show();
                    }

                    if (data.password === true) { //неверный пароль
                        $('#password-error').show();
                    }

                    if (data.login === true) {
                        //loginPopup.find('input[name="intended"]').val(window.location.href);
                        location.reload();
                        //form.submit();
                    }
                    console.log(data);
                }
            );

        });

    }

    //КНОПКА В КОРЗИНУ
    $('.to-cart').on('click', function (item) {
        item.preventDefault();
        let _productId = $(this).data('product');
        let _quantity = 1;
        let _options = $(this).data('options');
        $.post(
            '/cart_post/add/' + _productId, {
                quantity: _quantity,
                options: _options,
                tz: -(new Date().getTimezoneOffset()),
            }, function (data) {//Получаем кол-во товаров в корзине
                widget_cart(data);//Меняем кол-во и сумму товаров в виджете корзины в хеадере
            }
        );
    });

    ///СТРАНИЦА CART - КОРЗИНА

    //ОПЕРАЦИИ
    $('.cartitem-plus').on('click', function (item) {
        item.preventDefault();
        let _productId = $(this).data('product');
        let _input = $(this).prev();
        let _new_val = Number(_input.val()) + 1;
        _input.val(_new_val);
        set_count_item_cart(_productId, _new_val, $(this));
    });
    $('.cartitem-sub').on('click', function (item) {
        item.preventDefault();
        let _productId = $(this).data('product');
        let _input = $(this).next();
        let _new_val = Number(_input.val()) - 1;
        _input.val(_new_val);
        set_count_item_cart(_productId, _new_val, $(this))
    });
    $('.cartitem-set').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('.cartitem-set').on('keyup', function () {
        let _productId = $(this).data('product');
        let _quantity = $(this).val();
        if (_quantity === 0 || _quantity.length === 0) _quantity = 1;
        set_count_item_cart(_productId, _quantity);
    })
    function set_count_item_cart(_prod_id, _quantity, _obj = null) {
        if (_obj !== null) _obj.prop('disabled', true);
        $.post(
            '/cart_post/set/' + _prod_id, {
                quantity: _quantity,
                tz: -(new Date().getTimezoneOffset()),
            }, function (data) {
                widget_cart(data);
                page_cart(data);
                if (_obj !== null) setTimeout(function () {
                    _obj.prop('disabled', false);
                }, 250);
            }
        );
    }

    $('.cartitem-wish').on('click', function (item) {
        let _productId = $(this).data('product');
        //TODO Добавление в wish и учет и показывать, что товар уже в списке, повторное нажатие - удаление из wish
    });
    $('.cartitem-trash').on('click', function (item) {
        let _productId = $(this).data('product');
        //TODO Удалить из корзины
        $(this).prop(':disabled', true);
        $.post(
            '/cart_post/remove/' + _productId, {
                tz: -(new Date().getTimezoneOffset()),
            }, function (data) {
                widget_cart(data);
                page_cart(data);
                $('#full-cart-item-' + _productId).remove();
            }
        );
    });

    //Обновление виджета корзины
    function widget_cart(items) {
        let cartItemTemplate = $('#cart-item-template');
        let counterCart = $('#counter-cart');
        let _text,  _amount = 0, _discount_amount = 0;
        _counter = 0;

        $('div[id^="cart-item-N"]').remove();
        if (items.length === 0) { //Элементов нет, показываем пустую заглушку
            $('#cart-empty').show();
            $('#cart-not-empty').hide();
            counterCart.hide();
        } else {
            $('#cart-empty').hide();
            $('#cart-not-empty').show();
            counterCart.show();
        }
        console.log(items.length, items);
        for (let i = 0; i < items.length; i++) {
            let _item = cartItemTemplate.clone();
            _item.attr('id', 'cart-item-N' + (i + 1));
            _text = _item.html();
            _text = _text.replace('{img}', items[i].img)
            _text = _text.replace('{name}', items[i].name)
            _text = _text.replace('{quantity}', items[i].quantity)
            _text = _text.replace('{url}', items[i].url)
            _text = _text.replaceAll('{cost}', price_format(items[i].cost))
            _text = _text.replace('{discount_cost}', price_format(items[i].discount_cost))
            _text = _text.replace('{remove}', items[i].remove)
            _text = _text.replace('{id}', items[i].id)

            _counter += items[i].quantity;
            _amount += items[i].cost;
            _item.html(_text);
            _item.appendTo('.cart-body');
            if (items[i].discount_cost !== null) { //Для данного товара есть скидка
                _item.find($('.cart-item-cost')).hide();
                _item.find($('.cart-item-combined')).show();
                _discount_amount += items[i].discount_cost;
            } else {
                _discount_amount += items[i].cost;
            }
            _item.show();
        }
        if (_discount_amount < _amount) { //общая сумма со скидкой
            $('.cart-all-amount').hide();
            $('.cart-all-combined').show()
            $('#widget-cart-all-discount').text(price_format(_discount_amount));
            $('#widget-cart-all-amount-mini').text(price_format(_amount));
        } else {
            $('#widget-cart-all-amount').text(price_format(_amount));
        }
        counterCart.text(_counter);
        $('#widget-cart-all-count').text(_counter);

        $(document).on('click', '.remove-item-cart', function () {
            let route = $(this).data('route');
            $.post(route,{tz: -(new Date().getTimezoneOffset())}, function (data) {
                widget_cart(data);
            })
        });
    }
    //Обновление страницы корзины
    function page_cart(items) {
        if (items.length === 0) console.log('Произошла непонятная херня');
        let _amount = 0, _discount_amount = 0;
        _counter = 0;

        for (let i = 0; i < items.length; i++) {
            let _blockItem = $('#full-cart-item-' + items[i].product_id);
            _counter += items[i].quantity;
            _amount += items[i].cost;
            if (items[i].discount_cost === null) {
                _blockItem.find($('.full-cart-item--discount')).hide();
                _blockItem.find($('.full-cart-item--cost')).show();
                _blockItem.find($('.full-cart-item--combinate')).hide();
                _discount_amount += items[i].cost;

            } else {
                _blockItem.find($('.full-cart-item--discount')).show();
                _blockItem.find($('.full-cart-item--cost')).hide();
                _blockItem.find($('.full-cart-item--combinate')).show();
                _blockItem.find($('.discount-cost')).html(price_format(items[i].discount_cost));
                _blockItem.find($('.full-cart-item--discount')).find('span').html(items[i].discount_name);
                _discount_amount += items[i].discount_cost;
            }
            _blockItem.find($('.current-price')).html(price_format(items[i].price) + '/шт.');
            _blockItem.find($('.current-cost')).html(price_format(items[i].cost));
        }
        $('#cart-count-products').html(_counter);
        $('#cart-full-amount').html(price_format(_amount));
        $('#cart-full-discount').html(price_format(_amount - _discount_amount));
        $('#cart-amount-pay').html(price_format(_discount_amount));
        if (_discount_amount < _amount) { //общая сумма со скидкой
            //$('.cart-all-amount').hide();
            //$('.cart-all-combined').show()
            //$('#widget-cart-all-discount').text(price_format(_discount_amount));
            //$('#widget-cart-all-amount-mini').text(price_format(_amount));
        } else {
            //$('#widget-cart-all-amount').text(price_format(_amount));
        }
        //Обновить общее кол-во товаров и стоимость и показать есть ли скидка
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
    });
    upButton.on('click', function () {
        $('html, body').stop().animate({scrollTop: 0}, 1000);
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

    //Валидация email
    function isEmail(email) {
        let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    //Приведение числа в цену формата 1 000 000 ₽
    function price_format(_str) {
        if (_str === null || _str === '') return '';
        return _str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '  ₽';
    }

})();

