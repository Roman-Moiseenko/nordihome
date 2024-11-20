(function () {
    "use strict";
    $(".search-product")
        .each(function () {
            let idSearch= $(this).attr('id');
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
                if (search_str.length > 2) _ajax_search_product(route, search_str, listBox, idSearch);
            });
            $('body').on('click', '.search-option', function () {
                if (idSearch === $(this).data('for')) {
                    let _inputSearch = $(this).parent().parent().parent().find('input[name="search"]');
                    let _inputHidden = $(this).parent().parent().parent().find('#hidden-id');
                    let callback = $(this).parent().parent().parent().attr('data-callback');

                    if (_inputHidden !== undefined) {

                        _inputHidden.val($(this).data('id'));
                        _inputHidden.trigger('change');
                        //console.log('****');
                        //document.getElementById('hidden-id').value = $(this).data('id');
                        //document.getElementById('hidden-id').dispatchEvent(new Event('input'));
                        //document.getElementById('hidden-id').dispatchEvent(new Event('change'));
                    }
                    _inputSearch.val($(this).data('name'));
                    _inputSearch.attr('data-id', $(this).data('id'));
                    _inputSearch.attr('data-url', $(this).data('url'));
                    _inputSearch.attr('data-name', $(this).data('name'));
                    _inputSearch.attr('data-img', $(this).data('img'));
                    _inputSearch.attr('data-price', $(this).data('price'));
                    _inputSearch.attr('data-code', $(this).data('code'));
                    if ($(this).data('other') !== undefined) _inputSearch.attr('data-other', $(this).data('other'));
                    resultBlock.removeClass("show");

                    //_inputHidden.change();
                    //_inputHidden.input();
                    if (callback !== undefined) { //Колбек при выборе элемента
                        callback = callback.replace(/"/g, '');
                        eval(callback);
                    }
                }
            });
        });

    function _ajax_search_product(_route, _search_str, _listBox, _id_search)
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
                        let _other = '', _count = '';
                        if (item.other !== undefined) {
                            _other = 'data-other="' + item.other + '"';
                        }
                        if (item.count !== undefined) {
                            if (item.count === 0) {
                                _count = '<span class="circle red"></span>';
                            } else {
                                _count = '<span class="circle green"></span>';
                            }
                        }
                        _listBox.append('<div class="search-option" data-for="' + _id_search + '" data-url="' + item.url + '" data-id="' + item.id +
                            '" data-name="' + item.name + '" data-img="' + item.image + '" data-code="' + item.code + '" data-price="' + item.price + '" ' + _other + '>' +
                            _count + item.name + ' (' + item.code + ')'+
                            '</div>');
                    });
                }
            }
        });
    }
})();
