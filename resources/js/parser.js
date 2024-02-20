import jQuery from "jquery";
window.$ = jQuery;

(function () {
    "use strict";
    let parserButton = $('#search-parser-button');
    let inputButton = $('#search-parser-field');
    let parserItemSet = $('.parser-set-input');
    let buffer = {id:0, value: ''};

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

    parserItemSet.on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    parserItemSet.on('keydown', function () {
        buffer.id = $(this).data('product');
        buffer.value = $(this).val();
    });

    parserItemSet.on('keyup', function () {
        let product_id = $(this).data('product');
        let _quantity = $(this).val();
        if (_quantity === 0 || _quantity.length === 0) _quantity = 1;
        if (buffer.id === product_id && buffer.value !== _quantity)
            $.post(
                '/parser/' + product_id + '/set',
                {quantity: _quantity},
                function (data) {
                    updateParserData(data);
                }
            );
    });

    parserButton.on('click', function () {
        if ($('#search-parser-field').val() !== '') {
            parserButton.prop("disabled",true);
            $('#parser-search-form').submit();
        }
    });

    function updateParserData(data) {
        let cart = data;
        $('#weight').html(cart.weight);
        $('#delivery').html(price_format(cart.delivery));
        $('#amount').html(price_format(cart.amount));
        $('#full-amount').html(price_format(cart.amount + cart.delivery));
        for (let i = 0; i < cart.items.length; i++) {
            $('#count-' + cart.items[i].product.id).val(cart.items[i].quantity);
        }
        //delivery

    }

    function price_format(_str) {
        if (_str === null || _str === '') return '';
        return _str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '  ₽';
    }
})();
