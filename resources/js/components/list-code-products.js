(function () {
    "use strict";
    $(".list-code-products")
        .each(function () {
            let textAreaProducts = $(this).find('textarea[name="products"]');
            textAreaProducts.on("focus", function () {
                textAreaProducts.addClass("show-area");
                textAreaProducts.attr('rows', 10);
                textAreaProducts.css('height', '');
            });
            textAreaProducts.focusout(function () {
                textAreaProducts.removeClass("show-area");
                textAreaProducts.attr('rows', 1);
                textAreaProducts.css('height', 'auto');
            });
        });
})();
