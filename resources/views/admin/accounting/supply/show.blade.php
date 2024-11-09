@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-5">
            <h1 class="text-lg font-medium mr-auto flex ">
                {{ $supply->htmlNum() . ' от ' . $supply->htmlDate() }}
                @if($supply->isCompleted())
                    - Завершен
                    <button class="btn btn-sm btn-outline-warning ml-3"
                            onclick="event.preventDefault(); document.getElementById('copy-supply').submit();">
                        <x-base.lucide icon="copy" class="w-4 h-4"/>Copy
                    </button>

                    <form id="copy-supply" method="post" action="{{ route('admin.accounting.supply.copy', $supply) }}">
                        @csrf
                    </form>
                @endif
            </h1>
        </div>
    </div>

    Шапка Заказа

    <div class="grid grid-cols-12 gap-4 mt-5">
        <div class="col-span-12">
            <!-- Управление -->
            @if(!$supply->isCompleted())
            <div class="box flex p-5">

                <x-searchAddProduct route-save="{{ route('admin.accounting.supply.add-product', $supply) }}" quantity="1"/>
                <x-listCodeProducts route="{{ route('admin.accounting.supply.add-products', $supply) }}" caption-button="Добавить товары в документ" class="ml-3"/>

                <form method="post" action="{{ route('admin.accounting.supply.sent', $supply) }}" class="ml-auto">
                    @csrf
                    <button class="btn btn-danger">Провести документ</button>
                </form>
            </div>
            @else
                <div class="flex">
                    <button type="button" class="btn btn-primary">Скачать Документ</button>
                    <button type="button" class="btn btn-primary ml-2">Отправить по почте</button>
                    **** Создать на основании *****
                    <form method="post" action="{{ route('admin.accounting.supply.completed', $supply) }}" class="ml-auto">
                        @csrf
                        <button class="btn btn-danger">Создать поступления</button>
                    </form>
                </div>
                <h3 class="font-medium">Поступление на основе заказа: </h3>
                @foreach($supply->arrivals as $arrival)
                    <div class="box p-2 m-2">
                        <a class="text-success font-medium" href="{{ route('admin.accounting.arrival.show', $arrival) }}">{{ $arrival->htmlNum() . ' от ' . $arrival->htmlDate() . ' (' . $arrival->storage->name . ')' }}</a>
                    </div>

                @endforeach
            @endif
            <h2 class=" mt-3 font-medium">Товары в заказе</h2>
            <div class="box flex items-center font-semibold p-2">
                <div class="w-20 text-center">№ п/п</div>
                <div class="w-40 text-center">Артикул</div>
                <div class="w-1/4 text-center">Товар</div>
                <div class="w-40 text-center">Закупочная цена</div>
                <div class="w-40 text-center">Кол-во</div>
                <div class="w-20 text-center">Х</div>
            </div>
            @foreach($supply->products as $i => $product)
                <div class="box flex items-center p-2"  data-id="{{ $product->id }}"
                     data-route="{{ route('admin.accounting.supply.set-product', $product) }}">
                    <div class="w-20 text-center">{{ $i + 1 }}</div>
                    <div class="w-40 text-center">{{ $product->product->code }}</div>
                    <div class="w-1/4">
                        <a class="text-success font-medium" href="{{ route('admin.product.show', $product->product) }}">{{ $product->product->name }}</a>
                    </div>
                    <div class="w-40 px-1 text-center input-group">
                        <input id="cost-{{ $product->id }}" type="number" class="form-control text-center update-data-ajax"
                               value="{{ $product->cost_currency }}" aria-describedby="input-currency"
                               min="0" autocomplete="off" @if($supply->isCompleted()) disabled @endif
                        >
                        <div id="input-currency" class="input-group-text">Zl</div>
                    </div>
                    <div class="w-40 px-1 text-center input-group">
                        <input id="quantity-{{ $product->id }}" type="number" class="form-control text-center update-data-ajax"
                               value="{{ $product->quantity }}" aria-describedby="input-quantity"
                               min="{{ $supply->getQuantityStack($product->product) }}" autocomplete="off" @if($supply->isCompleted()) disabled @endif
                        >
                        <div id="input-quantity" class="input-group-text">шт.</div>
                    </div>
                    <div class="w-20 text-center">
                        @if(!$supply->isCompleted())
                            <button class="btn btn-outline-danger ml-6 product-remove" data-num = "{{ $i }}"
                                    data-id="{{ $product->id }}" type="button" onclick="document.getElementById('form-remove-item-{{ $product->id }}').submit()">X</button>
                            <form id="form-remove-item-{{ $product->id }}" method="post" action="{{ route('admin.accounting.supply.del-product', $product) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="box mt-3 p-5">
        <livewire:admin.accounting.edit-comment :document="$supply" />
    </div>

<script>
    let inputUpdateData = document.querySelectorAll('.update-data-ajax');
    Array.from(inputUpdateData).forEach(function (input) {
        input.addEventListener('change', function (event) {
            let route = input.parentElement.parentElement.getAttribute('data-route');
            let id = input.parentElement.parentElement.getAttribute('data-id');

            let data = {
                cost: document.getElementById('cost-' + id).value,
                quantity: document.getElementById('quantity-' + id).value,
            };
            setAjax(route, data);

          //  let route = input.dataset.route;

            /*
            let quantity = input.value;
            if(Number(quantity) < Number(input.getAttribute('min'))) {
                input.value = input.getAttribute('min');
            } else {
                setAjax(route, quantity);
            } */
        });
    });

    function setAjax(route, data) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&quantity=' + data.quantity + '&cost=' + data.cost;
        let request = new XMLHttpRequest();
        request.open('POST', route);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let data = JSON.parse(request.responseText);
                if (data.error !== undefined) {
                    //Notification
                    window.notification('Ошибка!', data.error, 'danger');
                }
            }
        };
    }
</script>


@endsection
