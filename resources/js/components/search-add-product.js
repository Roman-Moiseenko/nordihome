(function () {
    "use strict";
    $('.search-add-product')
        .each(function () {

            let selectProductId = 0;
            let inputQuantity = $(this).find("#input-quantity-component");
            let buttonSend = $(this).find("#button-send-component");//document.getElementById('');
            let route = $(this).find("#data").data('route');//  document.getElementById('data').dataset.route;
            let parser = $(this).find("#data").data('parser');//document.getElementById('data').dataset.parser;
            let published = $(this).find("#data").data('published');//document.getElementById('data').dataset.published;
            let show_stock = $(this).find("#data").data('show-stock');//document.getElementById('data').dataset.parser;
            let show_count = $(this).find("#data").data('show-count');//document.getElementById('data').dataset.parser;
            let token = $(this).find("#data").data('token');//document.getElementById('data').dataset.parser;


            let settings = {
                placeholder: 'Поиск ...',
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'code', 'code_search'],
                create: false,
                maxOptions: 16,
                onChange: function (value) {//Выбор элемента
                    if (value === '') {
                        selectProductId = 0;
                        return;
                    }
                    selectProductId = value;
                    if (inputQuantity !== null) {
                        inputQuantity.focus();
                    } else {
                        buttonSend.focus();
                    }
                },
                onType: function (str) {//Ввод с клавиатуры
                },
                load: function (query, callback) {
                    let _params = '_token=' + token + '&search=' + encodeURIComponent(query);
                    if (parser === '1') _params = _params + '&parser=1';
                    if (published === '1') _params = _params + '&published=1';

                    let request = new XMLHttpRequest();
                    request.open('POST', route);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request.send(_params);
                    request.onreadystatechange = function () {
                        if (this.readyState === 4 && this.status === 200) {
                            callback(JSON.parse(request.response));
                        }
                    };
                },
                render: {
                    option: function (item, escape) {
                        let _stock = '', _count = '';
                        if (show_stock)
                            if (item.stock) {
                                _stock = '<span class="circle green"></span>';
                            } else {
                                _stock = '<span class="circle red"></span>';
                            }

                    if (show_count) _count = '<span class="">' + item.count + '</span>';

                    return `<div class="row border-bottom py-2">
							<div class="col-md-12">
								<div class="mt-0">${_stock} ${_count}
                                    ${item.name}<span class="">( ${item.code_search} )</span>
								</div>
							</div>
						</div>`;
                    },
                    item: function (item, escape) {
                        return `<div class="flex">
								<div class="mt-0">${item.name}
									<span class="small text-muted">( ${item.code} )</span>
								</div>
						</div>`;
                    }
                }
            };
            let _selectContainer = $(this).find('.tom-select');
            let selectTom = new TomSelect(_selectContainer, settings);

            if (inputQuantity !== null)
                inputQuantity.onkeydown = function (e) {
                    if (e.keyCode === 13) {
                        event.preventDefault();
                        buttonSend.focus();
                    }
                }
            buttonSend.on('click', function () {
                let routeAdd = buttonSend.data('route');
                let eventAdd = buttonSend.data('event');
                let quantity = 0;
                if (inputQuantity !== null) quantity = inputQuantity.val();
                if (selectProductId !== 0) {
                    if (routeAdd !== '') send_route(routeAdd, quantity);
                    if (eventAdd !== '') send_event(eventAdd, quantity);
                }
            });

            function send_route(routeAdd, quantity) {
                let _params = '_token=' + token + '&product_id=' + selectProductId + '&quantity=' + quantity;
                let request = new XMLHttpRequest();
                request.open('POST', routeAdd);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(_params);
                request.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        if (request.responseURL !== '') window.location.href = request.responseURL;
                    }
                };
            }

            function send_event(eventAdd, quantity) {
                window.Livewire.dispatch(
                    eventAdd,
                    {
                        product_id: selectProductId,
                        quantity: quantity,
                    }
                );
                window.Livewire.on('clear-search-product', (event) => {
                    selectTom.clearOptions();
                    selectTom.clear();
                });
                //TODO Заменить и Удалить Протестировать
            /*    window.Livewire.on('update-amount-order', (event) => {
                    selectTom.clearOptions();
                    selectTom.clear();
                });*/
            }

        });
})();
