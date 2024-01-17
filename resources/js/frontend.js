import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";
    //Устанавливаем в сессию таймзону клиента
    sessionStorage.setItem("time", -(new Date().getTimezoneOffset()));

    /* От Заказчика*/
    //Кол-во столбцов в меню 1 - для маленьких, 3 для огромных - дублируется во _shop-l-classes.scss
    const countColSubMenu = 1;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let CartData; //Корзина
    let main = $('main');

    //Проверяем корзину виджета при загрузке
    if ($('#cart-header').length) {
        setTimeout(function () {
            $.post('/cart_post/cart/', {tz: -(new Date().getTimezoneOffset())}, function (data) {
                _error(data);
                widget_cart(data);
                CartData = data;
            });
        }, 250);
        $('#clear-cart').on('click', function () {
            let route = $(this).data('route');
            $.post(route, {tz: -(new Date().getTimezoneOffset())}, function (data) {
                _error(data);
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
        timerInput = setTimeout(function () {
            $.post(presearch.data('route'), {search: presearchInput.val()},//ajax запрос
                function (data) {
                    _error(data);
                    suggestBlock.html('');
                    if ($.isArray(data))
                        for (let i = 0; i < data.length; i++) {
                            suggestBlock.append(_itemSuggestPresearch(data[i]));
                        }
                });
        }, 180);
    });
    presearchInput.on('keydown', function () { //отменяем таймер при нажатии клавиши
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
        catalogParentItems.each(function () {//перебираем все пункты и для активного элемента навешиваем выделение пункта
            if ($(this).hasClass('active-item')) $(this).addClass('active');
        });
    });

    function _updateSubMenu(element) {
        element.addClass('active-item');
        $.post(catalog.data('route'), {
                category: element.data('id')
            },
            function (data) {
                _error(data);
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
        return '<a href="' + item.url + '" class="' + _class + '">' + item.name + _count + '</a>'
    }

    function _subSecond_div(items) {
        let html = '', sub_html = '';
        let n = 0;

        let countItems = items.length;
        let col;

        for (let j = 1; j <= countColSubMenu; j++) {
            col = Math.ceil((countItems - n) / (countColSubMenu - j + 1))
            sub_html = '';
            for (let i = 0; i < col; i++) {
                if (items[n] !== undefined) sub_html = sub_html + _subFirst_a(items[n], 2) + ' <br>';
                n++;
            }
            html = html + '<div class="submenu-second-level-column">' + sub_html + '</div>';
        }
        return '<div class="submenu-second-level-div">' + html + '</div>';
    }

    /** LOGIN POPUP **/
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
                    console.log(data);
                    _error(data);
                    $('#token-error').hide();
                    $('#password-error').hide();

                    if (data.token === true) $('#token-error').show(); //неверный токен
                    if (data.verification === true || data.register === true) { //требуется верификация
                        inputEmail.prop('disabled', true);
                        inputPassword.prop('disabled', true);
                        inputVerify.prop('required', true);
                        inputVerify.parent().show();
                    }
                    if (data.password === true) $('#password-error').show(); //Неверный пароль
                    if (data.login === true) location.reload(); //Аутентификация прошла
                }
            );

        });
    }

    /** КНОПКА В КОРЗИНУ **/
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
                _error(data);
                widget_cart(data);//Меняем кол-во и сумму товаров в виджете корзины в хеадере
            }
        );
    });
    //Обновление виджета корзины
    function widget_cart(data) {
        console.log(data);
        let items = data.items;
        let common = data.common;
        let cartItemTemplate = $('#cart-item-template');
        let counterCart = $('#counter-cart');

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
        for (let i = 0; i < items.length; i++) {
            let _item = cartItemTemplate;

            _item.find($('.cart-item-img')).attr('src', items[i].img);
            _item.find($('.cart-item-url')).html(items[i].name);
            _item.find($('.cart-item-url')).attr('href', items[i].url);
            if (items[i].check === false) {
                _item.find($('.cart-item-quantity')).html('<s>' + items[i].quantity + ' шт</s>');
            } else {
                _item.find($('.cart-item-quantity')).html(items[i].quantity + ' шт');
            }
            _item.find($('.cart-item-cost')).each(function () {
                    $(this).html(price_format(items[i].cost));
                }
            );
            _item.find($('.cart-item-discount_cost')).html(price_format(items[i].discount_cost));
            _item.find($('.remove-item-cart')).attr('data-route', items[i].remove);
            _item.find($('.remove-item-cart')).attr('data-item', items[i].id);

            if (items[i].discount_cost !== null) { //Для данного товара есть скидка
                _item.find($('.cart-item-costonly')).hide();
                _item.find($('.cart-item-combined')).show();
            } else {
                _item.find($('.cart-item-costonly')).show();
                _item.find($('.cart-item-combined')).hide();
            }
            _item = _item.clone();
            _item.attr('id', 'cart-item-N' + (i + 1));
            _item.show();
            _item.appendTo('.cart-body');
        }
        if (common.discount > 0) { //общая сумма со скидкой
            $('.cart-all-amount').hide();
            $('.cart-all-combined').show()
            $('#widget-cart-all-discount').text(price_format(common.amount + common.full_cost_preorder));
            $('#widget-cart-all-amount-mini').text(price_format(common.full_cost + common.full_cost_preorder));
        } else {
            $('#widget-cart-all-amount').text(price_format(common.full_cost + common.full_cost_preorder));
        }
        counterCart.text(Number(common.count + common.count_preorder));
        $('#widget-cart-all-count').text(Number(common.count + common.count_preorder));

        $(document).on('click', '.remove-item-cart', function () {
            let route = $(this).data('route');
            $.post(route, {tz: -(new Date().getTimezoneOffset())}, function (data) {
                _error(data);
                widget_cart(data);
            })
        });
    }


    /** СТРАНИЦА CART - КОРЗИНА **/
    if (main.hasClass('cart-page')) {
        let cartItemSet = $('.cartitem-set');

        //ОПЕРАЦИИ НА СТРАНИЦЕ
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
        cartItemSet.on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        cartItemSet.on('keyup', function () {
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
                    _error(data);
                    widget_cart(data);
                    page_cart(data);
                    if (_obj !== null) setTimeout(function () {
                        _obj.prop('disabled', false);
                    }, 250);
                }
            );
        }

        $('.cartitem-trash').on('click', function (item) {
            let _productId = $(this).data('product');
            $(this).prop(':disabled', true);
            $.post(
                '/cart_post/remove/' + _productId, {
                    tz: -(new Date().getTimezoneOffset()),
                }, function (data) {
                    _error(data);
                    widget_cart(data);
                    page_cart(data);
                    $('#full-cart-item-' + _productId).remove();
                }
            );
        });

        //Выделение и сброс всех элементов
        $('#checked-all').on('change', function () {
            if (this.checked) {
                $('.checked-item').each(function () {
                    $(this).prop('checked', true);
                });
                $('#cart-trash').show();
            } else {
                $('.checked-item').each(function () {
                    $(this).prop('checked', false);
                });
                $('#cart-trash').hide();
            }
            $.post('/cart_post/check-all/', {
                json: JSON.stringify({
                    all: $(this).prop('checked'),
                    tz: -(new Date().getTimezoneOffset())
                })
            }, function (data) {
                _error(data);
                page_cart(data);
            });
        });
        $('.checked-item').on('change', function () {
            let _all_check = true;
            let _show_trash = false;
            $('.checked-item').each(function () {
                if (!$(this).prop('checked')) _all_check = false;
                if ($(this).prop('checked')) _show_trash = true;
            });
            if (_all_check) {
                $('#checked-all').prop('checked', true);
            } else {
                $('#checked-all').prop('checked', false);
            }
            if (_show_trash) {
                $('#cart-trash').show();
            } else {
                $('#cart-trash').hide();
            }
            $.post('/cart_post/check/' + $(this).data('product'), {tz: -(new Date().getTimezoneOffset()),}, function (data) {
                _error(data);
                page_cart(data);
            });
        });
        //Очистка корзины
        $('#cart-trash').on('click', function () {
        let products = new Array();
        $('.checked-item').each(function () {
            if ($(this).prop('checked')) products.push($(this).data('product'));
        });
        if (products.length >= 0) {
            $.post(
                '/cart_post/clear/', {
                    product_ids: products,
                    tz: -(new Date().getTimezoneOffset()),
                },
                function (data) {

                    location.reload();
                    _error(data);
                }
            );
        }
    });

        $('#preorder-true').on('change', function () {
            if (this.checked) $('input[name=preorder]').val(true);
            console.log('true - ', $('input[name=preorder]').val());
        });
        $('#preorder-false').on('change', function () {
            if (this.checked) $('input[name=preorder]').val(false);
            console.log('false - ', $('input[name=preorder]').val());
        });
        //Обновление страницы корзины
        function page_cart(data) {
            CartData = data;
            let items = data.items;
            let common = data.common;
            if (items.length === 0) console.log('Произошла непонятная херня');

            for (let i = 0; i < items.length; i++) {
                let _blockItem = $('#full-cart-item-' + items[i].product_id);
                if (items[i].discount_cost === null) {
                    _blockItem.find($('.full-cart-item--discount')).hide();
                    _blockItem.find($('.full-cart-item--cost')).show();
                    _blockItem.find($('.full-cart-item--combinate')).hide();
                } else {
                    _blockItem.find($('.full-cart-item--discount')).show();
                    _blockItem.find($('.full-cart-item--cost')).hide();
                    _blockItem.find($('.full-cart-item--combinate')).show();
                    _blockItem.find($('.discount-cost')).html(price_format(items[i].discount_cost));
                    _blockItem.find($('.full-cart-item--discount')).find('span').html(items[i].discount_name);
                }
                _blockItem.find($('.current-price')).html(price_format(items[i].price) + '/шт.');
                _blockItem.find($('.current-cost')).html(price_format(items[i].cost));

                if (items[i].available !== null) {
                    _blockItem.find($('.full-cart-item--available')).show();
                    _blockItem.find($('.full-cart-item--available-count')).text(items[i].available);

                } else {
                    _blockItem.find($('.full-cart-item--available')).hide();
                }

            }
            //Итоговые данные по корзине
            if (CartData.length === 0) return false;

            let new_data = {count: 0, full_cost: 0, discount: 0, amount: 0};
            for (let i = 0; i < CartData.items.length; i++) {
                let prod_id = CartData.items[i].product_id;

                if ($('#full-cart-item-' + prod_id).find($('.checked-item')).prop('checked')) {

                    new_data.count += CartData.items[i].quantity;
                    new_data.full_cost += CartData.items[i].cost;
                    if (CartData.items[i].discount_cost === null) {
                        new_data.amount += CartData.items[i].cost

                    } else {
                        new_data.amount += CartData.items[i].discount_cost
                    }
                }
            }
            new_data.discount = new_data.full_cost - new_data.amount;
            //
            $('#cart-count-products').html(new_data.count);
            $('#cart-full-amount').html(price_format(new_data.full_cost));
            $('#cart-full-discount').html(price_format(new_data.discount));
            $('#cart-amount-pay').html(price_format(new_data.amount));
            if (new_data.count === 0) {
                $('#button-to-order').prop('disabled', true);
            } else {
                $('#button-to-order').prop('disabled', false);
            }
            if (data.common.preorder) {
                $('#cart-preorder').show();
            } else {
                $('#cart-preorder').hide();
            }
        }
}
    /** ДОБАВИТЬ В ИЗБРАННОЕ **/
    $('.product-wish-toogle').on('click', function (item) {
        let _productId = $(this).data('product');
        if (_productId !== undefined) {
            //POST запрос
            //TODO Добавление в wish и учет и показывать, что товар уже в списке, повторное нажатие - удаление из wish
        }
    });

    /** ОФОРМЛЕНИЕ ЗАКАЗА  */
    if (main.hasClass('order-page-create')) {
        //Переключение способов доставки
        let deliveryStorageDIV = $('.block-delivery>.delivery-storage');
        let deliveryLocalDIV = $('.block-delivery>.delivery-local');
        let deliveryRegionDIV = $('.block-delivery>.delivery-region');
        let inputStorage = $('input[name=storage]');

        function readElements() {
            //Считываем все поля для отправки
            let data = {};
            $('input').each(function () {
                let name = $(this).attr('name');
                if ($(this).attr('type') === 'radio') {
                    if ($(this).prop('checked')) data[name] = $(this).val();
                } else {
                    data[name] = $(this).val();
                }
            });
            return data;
        }

        function writeElements(state) {
            //Записываем полученный результат в элементы
            console.log('state = ', state);
            //Данные по доставке
            let spanRegion = $('.delivery-region').find('.address-delivery--info');
            let spanLocal = $('.delivery-local').find('.address-delivery--info');
            spanLocal.html(state.delivery.delivery_local);
            spanRegion.html(state.delivery.delivery_address);
            if (state.delivery.storage !== null) {
                inputStorage.each(function () {
                    $(this).prop('checked', ($(this).val() == state.delivery.storage));
                });
            }

            //По счетам
            let invoiceBlock = $('#invoice-data');
            if (state.payment.is_invoice) {
                invoiceBlock.find('.address-delivery--info').html(state.payment.invoice);
                invoiceBlock.show();
            } else {
                invoiceBlock.hide();
            }
            if (state.payment.invoice === '') {
                invoiceBlock.find('#input-inn').parent().show();
                invoiceBlock.find('#input-inn-hidden').parent().hide();
            } else {
                invoiceBlock.find('#input-inn').parent().hide();
                invoiceBlock.find('#input-inn-hidden').parent().show();
            }

            //Общие данные
            //Доступность, Оплатить/Оформить, Стоимость доставки, Купон
            let buttonOrder = $('#button-to-order');
            buttonOrder.prop('disabled', !state.amount.enabled);
            buttonOrder.html(state.amount.caption);
            let orderDelivery = $('#order-full-delivery');
            orderDelivery.html(price_format(state.amount.delivery.cost));
            let orderAmount = $('#order-amount-pay');
            orderAmount.html(price_format(Number(orderAmount.data('base-cost')) - state.amount.delivery.cost))
        }

        function sendToBackend() {
            let data = readElements();
            $.post('/order/checkorder/', {data}, function (res) {
                _error(res);
                writeElements(res);
            })
        }

        //Навешиваем событие при изменении элементов //
        $('input[data-state=change]').on('change', function () {
            sendToBackend();
        });
        $('.input-to-hidden').on('click', function () {
            let from = $('#' + $(this).attr('from'));
            let to = $('#' + $(this).attr('to'));
            to.val(from.val());
            from.val('');
            from.parent().hide();
            to.parent().show();
            sendToBackend();
        })
        //Изменить адрес (открыть блок)
        $('.address-delivery--change').on('click', function () {
            let id = $(this).attr('for');
            $('#' + id).show();
        });
        //Переключение типов доставки
        $('input[name=delivery]').on('change', function () {
            deliveryStorageDIV.hide();
            deliveryLocalDIV.hide();
            deliveryRegionDIV.hide();
            if ($(this).attr('id') === 'delivery_storage') {
                deliveryStorageDIV.show();
            }
            if ($(this).attr('id') === 'delivery_local') {
                deliveryLocalDIV.show();
            }
            if ($(this).attr('id') === 'delivery_region') {
                deliveryRegionDIV.show();
            }
        });
        //Ввод купона
        let inputCoupon = $('input[name=coupon]');
        inputCoupon.on('input', function () {
            let code = inputCoupon.val();
            let couponInfo = $('.coupon-info');
            let couponAmount = $('.coupon-amount');

            if (code.length > 2) {
                $.post('/order/coupon', {
                        code: code,
                        tz: -(new Date().getTimezoneOffset()),
                    }, function (data) {
                        if (data != 0) {
                            couponInfo.show();
                            couponAmount.html(price_format(data));
                        } else {
                            couponInfo.hide();
                            couponAmount.html('');
                        }
                    });
            } else {
                couponInfo.hide();
                couponAmount.html('');
            }
        });

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

    //Валидация email
    function isEmail(email) {
        let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    //Валидация input/number
    function inn_format(_num) {
        let regex = /^([0-9]{10,12})+$/;
        return regex.test(_num);
    }

    //Приведение числа в цену формата 1 000 000 ₽
    function price_format(_str) {
        if (_str === null || _str === '') return '';
        return _str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '  ₽';
    }

    //Отображение ошибок
    function _error(data) {
        if (data.error !== undefined) console.log(data.error);
    }
    //Карусели
    let optionsSliderBase = {
        rtl: false,
        startPosition: 0,
        items: 1,
        autoplay: false, //
        smartSpeed: 1500, //Время движения слайда
        autoplayTimeout: 1000, //Время смены слайда
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        margin: 10,
        loop: false,
        dots: false,
        nav: true,
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
        singleItem: true,
        transitionStyle: "fade",
        touchDrag: true,
        mouseDrag: false,
        responsive: {
            0: {
                items: 1,
                smartSpeed: 500
            },
            576: {
                items: 2,
                smartSpeed: 500
            },
            991: {
                items: 6,
                smartSpeed: 500
            },
        }
    };
    if (document.getElementById('slider-payment') !== null) {
        let sliderPayment = $('#slider-payment');
        sliderPayment.owlCarousel(optionsSliderBase);
        sliderPayment.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('slider-delivery-company') !== null) {
        let sliderDeliveryCompany = $('#slider-delivery-company');
        sliderDeliveryCompany.owlCarousel(optionsSliderBase);
        sliderDeliveryCompany.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }

})();

