(function () {
    "use strict";
    $(".search-product")
        .each(function () {
            let resultBlock = $(this).find(".search-product-result"); //Блок отображения результата
            let inputSearch = $(this).find('input[name="search"]');
            let listBox = resultBlock.find('div[role="listbox"]');
            let route = inputSearch.data('route');

            inputSearch.on("focus", function () {
                if (inputSearch.val().length > 2) {
                    _ajax_search_product(route, inputSearch.val(), listBox);
                } else {
                    listBox.html('');
                    listBox.append('<div class="text-slate-400">Вводите наименование или артикул товара</div>');
                }
                resultBlock.addClass("show");

            }); //Открыть блок при начале поиска

            $(document).on('click', function (e) {
                if ($(e.target).closest(".search-product").length) {
                    return;
                }// клик снаружи элемента
                resultBlock.removeClass("show");
            });

            //Событие при вводе данных
            inputSearch.on('keyup', function () {
                let search_str = $(this).val();

                if (search_str.length === 0) {
                    listBox.html('');
                    listBox.append('<div class="text-slate-400">Вводите наименование или артикул товара</div>');
                }
                if (search_str.length > 2) _ajax_search_product(route, search_str, listBox);
            });
            $('body').on('click', '.search-option', function () {
                inputSearch.val($(this).data('name'));
                inputSearch.attr('data-id', $(this).data('id'));
                inputSearch.attr('data-name', $(this).data('name'));
                inputSearch.attr('data-img', $(this).data('img'));
                inputSearch.attr('data-price', $(this).data('price'));
                inputSearch.attr('data-code', $(this).data('code'));
                resultBlock.removeClass("show");
            });
        });

    function _ajax_search_product(_route, _search_str, _listBox)
    {
        $.ajax({
            url: _route,
            type: "POST",
            data: {
                search: _search_str,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {

                if (Array.isArray(data)) {
                    _listBox.html('');
                    if (data.length === 0) {
                        _listBox.append('<div class="text-slate-400">Вводите наименование или артикул товара</div>');
                    }
                    data.forEach(function (item) {
                        _listBox.append('<div class="search-option" data-id="' + item.id +
                            '" data-name="' + item.name + '" data-img="' + item.image + '" data-code="' + item.code + '" data-price="' + item.price + '">' +
                            item.name + ' (' + item.code + ')'+
                            '</div>');
                    });
                }
            }
        });
    }
})();
