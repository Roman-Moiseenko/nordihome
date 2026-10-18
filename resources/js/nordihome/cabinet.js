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

    let main = $('main');
    /**
     * Единая функция сохранения профиля клиента.
     * Собирает поля по унифицированным ID, отсутствующие отправляет как null.
     * Поля соответствуют ClientUpdateData (кроме priceType и discount — не меняются клиентом).
     *
     * @param {object} options - { route: string, onSuccess: function(data, res) }
     */
    function saveClientProfile(options) {
        let data = {
            lastName: _val('#input-lastname'),
            firstName: _val('#input-firstname'),
            middleName: _val('#input-middlename'),
            phone: _val('#input-phone'),
            email: _val('#input-email-notify'),
            birthDate: null,
            gender: _radioVal('gender'),
            country: _val('#input-country'),
            region: _val('#input-region'),
            regionCode: _val('#input-region-code'),
            city: _val('#input-city'),
            street: _val('#input-street'),
            postalCode: _val('#input-postal-code'),
            isPickup: _val('#input-is-pickup')
        };

        let computed = {
            fullName: [data.lastName, data.firstName, data.middleName].filter(Boolean).join(' '),
            fullAddr: [data.country, data.region, data.city, data.street, data.postalCode].filter(Boolean).join(', '),
            genderText: data.gender === 'male' ? 'Мужской' : (data.gender === 'female' ? 'Женский' : 'Не указан')
        };

        $.ajax({
            url: options.route,
            type: 'PUT',
            data: data,
            success: function (res) {
                common.error(res);

                // === Общие обновления data-view (единые ID в обоих шаблонах) ===
                $('#data-view-fullname').text(computed.fullName);
                $('#data-view-email').text(data.email);
                $('#data-view-phone').text(data.phone);
                $('#data-view-address').text(computed.fullAddr || 'Не указан');
                $('#data-view-gender').text(computed.genderText);

                // === Специфичные для оформления заказа (невидимые в кабинете — нет эффекта) ===
                let isPickup = data.isPickup == 1;
                $('#data-view-is-pickup').text(isPickup ? 'Самовывоз' : 'Доставка');
                $('#delivery-address').toggle(!isPickup);
                $('#input-address-hidden').val(computed.fullAddr);
                $('#input-fullname-hidden').val(computed.fullName);
                $('#input-phone-hidden').val(data.phone);

                // === Возврат в режим просмотра ===
                $('.box-card .edit-group, #personal-data .edit-group').hide();
                $('.box-card .data-view, #personal-data .data-view').show();
                $('#change-personal').show();
                $('#save-personal').hide();
                $('#cancel-personal').hide();

                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(data, computed);
                }
            }
        });
    }

    /**
     * Единая инициализация кнопок Изменить/Отмена/Сохранить для профиля.
     * Работает и в кабинете, и в оформлении заказа — селекторы пробуют оба варианта.
     *
     * @param {{ onSave: function|undefined }} options
     */
    function initClientProfileEdit(options) {
        options = options || {};
        let changeBtn = $('#change-personal');
        let saveBtn   = $('#save-personal');
        let cancelBtn = $('#cancel-personal');
        let container = $('.box-card, #personal-data').first();

        if (!changeBtn.length) return;

        changeBtn.on('click', function () {
            container.find('.data-view').hide();
            container.find('.edit-group').show();
            changeBtn.hide();
            saveBtn.show();
            cancelBtn.show();
        });

        cancelBtn.on('click', function () {
            container.find('.edit-group').hide();
            container.find('.data-view').show();
            changeBtn.show();
            saveBtn.hide();
            cancelBtn.hide();
        });

        saveBtn.on('click', function () {
            saveClientProfile({
                route: saveBtn.data('route'),
                onSuccess: options.onSave
            });
        });
    }
    /** ОФОРМЛЕНИЕ ЗАКАЗА  */
    if (main.hasClass('order-page-create') || main.hasClass('order-page-create-parser') ) {
        //Переключение способов доставки
        let deliveryStorageDIV = $('.block-delivery>.delivery-storage');
        let deliveryLocalDIV = $('#delivery-address');
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
            if (main.hasClass('order-page-create')) {
                data['order'] = 'cart'; //Стандартная корзина
            }
            if (main.hasClass('order-page-create-parser')) {
                data['order'] = 'parser'; //Корзина Парсер
            }
            return data;
        }
/*
        function writeElements(state) {
            //Записываем полученный результат в элементы
            //Данные по доставке
            let spanDelivery = $('#delivery-address .data-view');
            let deliveryText = state.delivery.delivery_local || state.delivery.delivery_address || '';
            spanDelivery.html(deliveryText);
            if (state.delivery.storage !== null) {
                inputStorage.each(function () {
                    $(this).prop('checked', ($(this).val() === state.delivery.storage));
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
            orderDelivery.html(common.price_format(state.amount.delivery.cost));
            //let orderAmount = $('#order-amount-pay');
            //orderAmount.html(price_format(Number(orderAmount.data('base-cost'))))
            let spanFullname = $('#personal-order .fullname-block .data-view');
            let spanPhone = $('#personal-order .phone-block .data-view');
            spanFullname.html(state.delivery.fullname);
            spanPhone.html(state.phone);
        }
*/
  /*      function sendToBackend() {
            let data = readElements();
               $.post('/order/checkorder', {data}, function (res) {
                   //console.log(res);
                   common.error(res);
                   writeElements(res);
               })
        }
*/
        //Навешиваем событие при изменении элементов //
  /*      $('input[data-state=change]').on('change', function () {
         //   sendToBackend();
        });
        $('.input-to-hidden').on('click', function () {
            let from = $('#' + $(this).attr('from'));
            let to = $('#' + $(this).attr('to'));
            to.val(from.val());
            from.val('');
            from.parent().hide();
            to.parent().show();
        //    sendToBackend();
        })

*/
        // === Переключение способа получения: показывать/скрывать адрес ===
        $('#input-is-pickup').on('change', function () {
            let isPickup = $(this).val() === 1;
            if (isPickup) {
                $('#delivery-address').hide();
            } else {
                $('#delivery-address').show();
            }
          //  sendToBackend();
        });

        // === Кнопки Изменить/Отмена/Сохранить ===
        initClientProfileEdit({ onSave: function () { /*sendToBackend();*/ } });

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
                    if (data !== 0) {
                        couponInfo.show();
                        couponAmount.html(common.price_format(data));
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
    /** КАБИНЕТ **/
    if (main.hasClass('cabinet')) {
        // === Кнопки Изменить/Отмена/Сохранить ===
        initClientProfileEdit();

        // --- Email для входа ---
        let emailButton = $('#change-email');
        let emailGroup = $('#group-email');
        let emailData = $('#data-email');
        let emailInput = $('#input-email');
        let emailSave = $('#save-email');
        emailButton.on('click', function () {
            emailButton.hide();
            emailData.hide();
            emailGroup.show();
            emailInput.val(emailData.text());
        });
        emailSave.on('click', function () {
            emailButton.show();
            emailData.show();
            emailGroup.hide();
            let new_value = emailInput.val();
            emailData.text(new_value);
            $.post(emailSave.data('route'), {email: new_value}, function (data) {
                if (data === true) {
                    location.reload()
                }
            })
        });

        // --- Пароль ---
        let passwordButton = $('#change-password');
        let passwordGroup = $('#group-password');
        let passwordInput = $('#input-password');
        let passwordSave = $('#save-password');
        passwordButton.on('click', function () {
            passwordButton.hide();
            passwordGroup.show();
            passwordInput.val('');
        });
        passwordSave.on('click', function () {
            passwordButton.show();
            passwordGroup.hide();
            let new_value = passwordInput.val();
            $.post(passwordSave.data('route'), {password: new_value}, function (data) {
                if (data === true) {
                    $('#new-password').show();
                }
            })
        });

        //Подписки
        let subscriptionCheck = $('.subscription-check');
        subscriptionCheck.on('change', function(element) {
            $.post(element.target.dataset.route, {}, function (data) {
                common.error(data);
            })
        });

    }


    // === Вспомогательные функции для профиля клиента ===
    function _val(selector) {
        let el = $(selector);
        return el.length ? el.val() : null;
    }
    function _radioVal(name) {
        let checked = $('input[name="' + name + '"]:checked');
        return checked.length ? checked.val() : null;
    }
})();
