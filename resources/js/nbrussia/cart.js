import jQuery from "jquery";
import common from "@/_common.js";
window.$ = jQuery;

/** Быстрое оформление Заказа **/
let newOrderPopup = $('#new-order');
if (newOrderPopup.length) {
    let formNewOrder = newOrderPopup.find('form#buy-click-form');
    let buttonNewOrder = newOrderPopup.find('#button-buy-click');

    let inputNOEmail = newOrderPopup.find('input[name="email"]');
    let inputNOPhone = newOrderPopup.find('input[name="phone"]');
    let inputNOFIO = newOrderPopup.find('input[name="fullname"]');
    let inputNOPersonal = newOrderPopup.find('input[name="personal"]');

    let selectNODelivery = newOrderPopup.find('select[name="delivery"]');
    let inputNOAddress = newOrderPopup.find('input[name="address"]');
    buttonNewOrder.on('click', function (item) {
        console.log(inputNOPersonal.is(':checked'))
        let errorBlock = newOrderPopup.find('#buy-click-error');
        item.preventDefault();
        if (inputNOEmail.val() === '' || inputNOPhone.val() === '' || inputNOFIO.val() === '' || selectNODelivery.val() === '') {
            errorBlock.html('Не заполнены поля');
            return false;
        }
        if (!inputNOPersonal.is(':checked')) {
            errorBlock.html('Подтвердите согласие на обработку ПД');
            return false;
        }
        if ((selectNODelivery.val() === 'local' || selectNODelivery.val() === 'region') && inputNOAddress.val() === '') {
            errorBlock.html('Не заполнен адрес доставки');
            return false;
        }

        formNewOrder.submit();

    });

}
