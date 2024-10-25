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

    <div class="grid grid-cols-12 gap-4 mt-5">

        <div class="col-span-12">
            <!-- Управление -->
            @if($supply->isCreated())
            <div class="box flex p-5">

                <x-searchAddProduct route-save="{{ route('admin.accounting.supply.add-product', $supply) }}" quantity="1"/>
                <x-listCodeProducts route="{{ route('admin.accounting.supply.add-products', $supply) }}" caption-button="Добавить товары в документ" class="ml-3"/>

                <form method="post" action="{{ route('admin.accounting.supply.sent', $supply) }}" class="ml-auto">
                    @csrf
                    <button class="btn btn-danger">Отправить в работу</button>
                </form>
            </div>
            @endif
            @if($supply->isSent())
                <div class="flex">
                    <button type="button" class="btn btn-primary">Скачать Документ</button>
                    <button type="button" class="btn btn-primary ml-2">Отправить по почте</button>

                    <form method="post" action="{{ route('admin.accounting.supply.completed', $supply) }}" class="ml-auto">
                        @csrf
                        <button class="btn btn-danger">Создать поступления</button>
                    </form>
                </div>
            @endif
            @if($supply->isCompleted())
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
                <div class="w-20 text-center">Кол-во</div>
                <div class="w-20 text-center">Х</div>
            </div>
            @foreach($supply->products as $i => $product)
                <div class="box flex items-center p-2">
                    <div class="w-20 text-center">{{ $i + 1 }}</div>
                    <div class="w-40 text-center">{{ $product->product->code }}</div>
                    <div class="w-1/4">
                        <a class="text-success font-medium" href="{{ route('admin.product.show', $product->product) }}">{{ $product->product->name }}</a>
                    </div>
                    <div class="w-20 px-1 text-center">
                        <input id="quantity-{{ $product->id }}" type="number" class="form-control text-center update-data-ajax"
                               value="{{ $product->quantity }}" aria-describedby="input-quantity"
                               min="{{ $supply->getQuantityStack($product->product) }}" autocomplete="off"
                               data-route="{{ route('admin.accounting.supply.set-product', $product) }}" @if(!$supply->isCreated()) disabled @endif
                        >
                    </div>
                    <div class="w-20 text-center">
                        @if($supply->isCreated())
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
            let route = input.dataset.route;
            let quantity = input.value;
            if(Number(quantity) < Number(input.getAttribute('min'))) {
                input.value = input.getAttribute('min');
            } else {
                setAjax(route, quantity);
            }
        });
    });

    function setAjax(route, quantity) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&quantity=' + quantity;
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
