import jQuery from "jquery";
window.$ = jQuery;

(function () {
    "use strict";
    let parserButton = $('#search-parser-button');
    let inputButton = $('#search-parser-field');

    $('.increase-button').on('click', function () {
        let product_id = $(this).data('code');
        $.post(
            '/parser/' + product_id + '/add',
            {},
            function (data) {
                updateParserData(data);
            }
        );
    });
    $('.decrease-button').on('click', function () {
        let product_id = $(this).data('code');
        $.post(
            '/parser/' + product_id + '/sub',
            {},
            function (data) {
                updateParserData(data)
            }
        );
    });

    function updateParserData(data) {
        let cart = data;
        $('#weight').html(cart.weight);
        $('#delivery').html(price_format(cart.delivery));
        $('#amount').html(price_format(cart.amount));
        $('#full-amount').html(price_format(cart.amount + cart.delivery));
        for (let i = 0; i < cart.items.length; i++) {
            $('#count-' + cart.items[i].product.id).html(cart.items[i].quantity);
        }
        //delivery

    }

    function price_format(_str) {
        if (_str === null || _str === '') return '';
        return _str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '  ₽';
    }
})();
