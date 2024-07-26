<div>
    <input id="input-search-component" type="text" class="form-control w-{{ $width }}" placeholder="Введите артикул или ссылку" autocomplete="off"/>
    @if($quantity)
        <input id="input-quantity-component" class="form-control w-20 ml-2" type="number" value="1" min="1" autocomplete="off">
    @endif
    <button id="button-send-component" class="btn btn-primary ml-2" type="button" data-route="{{ $route }}"
            data-event="{{ $event }}">{{ $caption }}
    </button>

    <script>
        let buttonSend = document.getElementById('button-send-component');
        let inputSearch = document.getElementById('input-search-component');
        let inputQuantity = document.getElementById('input-quantity-component');

        buttonSend.addEventListener('click', function () {
            let routeAdd = buttonSend.dataset.route;
            let eventAdd = buttonSend.dataset.event;
            let search = inputSearch.value;
            let quantity = 0;
            if (inputQuantity !== null) quantity = inputQuantity.value;
            if (search !== '') {
                inputSearch.disabled = true;
                buttonSend.disabled = true;
                if (inputQuantity !== null) inputQuantity.disabled = true;
                if (routeAdd !== '') send_route(routeAdd, search, quantity);
                if (eventAdd !== '') send_event(eventAdd, search, quantity);
            }
        });

        function send_route(routeAdd, search, quantity) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&search=' + search + '&quantity=' + quantity;
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

        function send_event(eventAdd, search, quantity) {
            Livewire.dispatch(
                eventAdd,
                {
                    search: search,
                    quantity: quantity,
                }
            );

            Livewire.on('clear-search-product', (event) => {
                inputSearch.value = '';
                inputSearch.disabled = false;
                if (inputQuantity !== null) {
                    inputQuantity.value = 1;
                    inputQuantity.disabled = false;
                    buttonSend.disabled = false;
                }
            });
        }

    </script>
</div>
