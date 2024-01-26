import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";
    let parserButton = $('#search-parser-button');
    let inputButton = $('#search-parser-field');
    parserButton.on('click', function () {
        $.post(
            '/parser/search',
            {
                search: inputButton.val()
            },
            function (data) {
                console.log(data)
            }
        );
    });

})();
