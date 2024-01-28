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
        console.log(data)

    }
})();
