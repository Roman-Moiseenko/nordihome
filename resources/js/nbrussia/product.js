import jQuery from "jquery";
import common from "@/_common.js";
window.$ = jQuery;


let sizes = $('.modification > .size')
let to_cart = $('#to-cart')
if (sizes.length) {
    sizes.each(function () {
        let size = $(this)
        size.on('click', function () {
            sizes.removeClass('active')
            size.addClass('active')
            let id = size.data('id')
            to_cart.attr('data-product', id)
        })
    })
}


to_cart.on('click', function (item) {
    item.preventDefault();
    let _productId = $(this).attr('data-product');
    let _quantity = 1;
    let _options = $(this).data('options');
    //console.log(_productId)
    $.post(
        '/cart_post/add/' + _productId, {
            quantity: _quantity,
            options: _options,
            tz: -(new Date().getTimezoneOffset()),
        }, function (data) {//Получаем кол-во товаров в корзине
            if (data.error !== undefined) {
                common.notification('error', data.error)
            } else {
                common.notification('success', data)
            }

            if (!common.error(data)) window.Livewire.dispatch('update-header-cart');//Меняем кол-во и сумму товаров в виджете корзины в хеадере
        }
    ).done(function (msg) {
        console.log(msg);
    }).fail(function(xhr, status, error) {
        console.log(xhr, status, error);
        }
    );
});
