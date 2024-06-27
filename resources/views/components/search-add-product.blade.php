<div class="flex">
    <span id="data" data-route="{{ $routeSearch }}" data-parser="{{ $parser }}" data-published="{{ $published }}"></span>
    <select id="search-product-component" class="tom-select form-control w-{{ $width }}">
        <option id="0"></option>
    </select>
    @if($quantity)
        <input id="input-quantity-component" class="form-control w-20 ml-2" type="number" value="1" min="1">
    @endif
    <button id="button-send-component" class="btn btn-primary ml-2" type="button" data-route="{{ $route }}"
            data-event="{{ $event }}">{{ $caption }}
    </button>

    <script>
        let selectProductId = 0;
        let inputQuantity = document.getElementById('input-quantity-component');
        let buttonSend = document.getElementById('button-send-component');
        let route = document.getElementById('data').dataset.route;
        let parser = document.getElementById('data').dataset.parser;
        let published = document.getElementById('data').dataset.published;
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
                let _params = '_token=' + '{{ csrf_token() }}' + '&search=' + encodeURIComponent(query);
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
                    @if($showStock)
                    if (item.stock) {
                        _stock = '<span class="circle green"></span>';
                    } else {
                        _stock = '<span class="circle red"></span>';
                    }
                    @endif
                    @if($showCount)
                        _count = '<span class="">' + item.count + '</span>';
                    @endif

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
        let selectTom = new TomSelect('#search-product-component', settings);
        if (inputQuantity !== null)
            inputQuantity.onkeydown = function (e) {
                if (e.keyCode === 13) {
                    event.preventDefault();
                    buttonSend.focus();
                }
            }
        buttonSend.addEventListener('click', function () {
            let routeAdd = buttonSend.dataset.route;
            let eventAdd = buttonSend.dataset.event;
            let quantity = 0;
            if (inputQuantity !== null) quantity = inputQuantity.value;
            if (selectProductId !== 0) {
                if (routeAdd !== '') send_route(routeAdd, quantity);
                if (eventAdd !== '') send_event(eventAdd, quantity);
            }
        });

        function send_route(routeAdd, quantity) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + selectProductId + '&quantity=' + quantity;
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
            Livewire.dispatch(
                eventAdd,
                {
                    product_id: selectProductId,
                    quantity: quantity,
                }
            );
            Livewire.on('update-amount-order', (event) => {
                selectTom.clearOptions();
                selectTom.clear();
            });
        }

    </script>
</div>
