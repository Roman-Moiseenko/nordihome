@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $arrival->number . ' от ' . $arrival->created_at->format('d-m-Y') . ' (' . $arrival->distributor->name. ') - ' . $arrival->storage->name }}
        </h2>
    </div>
    <div class="box flex p-5 mt-3 items-center">
        <h3>Установка цен</h3>
    @if(empty($arrival->pricing))
        <button class="btn btn-success ml-5" onclick="document.getElementById('create-pricing-arrival').submit();">Создать</button>
        <form id="create-pricing-arrival" method="post" action="{{ route('admin.accounting.pricing.create-arrival', $arrival) }}">
            @csrf
        </form>
    @else
        <a class="ml-5 text-success font-medium" href="{{ route('admin.accounting.pricing.show', $arrival->pricing) }}" target="_blank">{{ $arrival->pricing->htmlNum() . ' от ' . $arrival->pricing->htmlDate() }}</a>
    @endif
    </div>
    @if(!$arrival->isCompleted())
    <form action="{{ route('admin.accounting.arrival.add', $arrival) }}" method="POST">
        @csrf
        <div class="box flex p-5 mt-3 items-center">
            <div class="mx-3 flex w-full">
                <x-searchProduct route="{{ route('admin.accounting.arrival.search', $arrival) }}"
                                 input-data="arrival-product" hidden-id="product_id" class="w-56"/>
                {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->show() }}
                <x-base.button id="add-product" type="submit" variant="primary" class="ml-3">Добавить товар в документ
                </x-base.button>
                <a class="btn btn-outline-primary ml-5" href="{{ route('admin.accounting.arrival.edit', $arrival) }}">
                    <x-base.lucide icon="check-square" class="w-4 h-4"/>
                    Редактировать параметры</a>
                <button type="button" class="ml-auto btn btn-danger" onclick="document.getElementById('form-completed').submit();">Провести документ</button>
            </div>
        </div>
    </form>
    @endif
    <form id="form-completed" method="post" action="{{ route('admin.accounting.arrival.completed', $arrival) }}">
        @csrf
    </form>
    <div class="box flex items-center font-semibold p-2 mt-3">
        <div class="w-20 text-center">№ п/п</div>
        <div class="w-1/4 text-center">Товар</div>
        <div class="w-40 text-center">Закупочная цена</div>
        <div class="w-40 text-center">Кол-во</div>
        <div class="w-40 input-group">Закупочная цена</div>
        <div class="w-40 input-group">Цена продажи</div>
    </div>
    @foreach($arrival->arrivalProducts as $i => $item)
        <div class="box flex items-center px-2" data-id="{{ $item->id }}"
             data-route="{{ route('admin.accounting.arrival.set', $item->id) }}">
            <div class="w-20">{{ ($i + 1) }}</div>
            <div class="w-1/4">{{ $item->product->name }}</div>
            <div class="w-40 input-group">
                <input id="currency-{{ $item->id }}" type="number" class="form-control text-right arrival-input-listen"
                       value="{{ $item->cost_currency }}" aria-describedby="input-currency" min="0" {{ $arrival->isCompleted() ? 'readonly' : '' }}>
                <div id="input-currency" class="input-group-text">{{ $item->document->currency->sign }}</div>
            </div>
            <div class="w-40 input-group">
                <input id="quantity-{{ $item->id }}" type="number" class="form-control text-right arrival-input-listen"
                       value="{{ $item->quantity }}" aria-describedby="input-quantity" min="0" {{ $arrival->isCompleted() ? 'readonly' : '' }}>
                <div id="input-quantity" class="input-group-text">шт.</div>
            </div>
            <div class="w-40 input-group">
                <input id="cost_ru-{{ $item->id }}" type="number" class="form-control text-right"
                       value="{{ $item->cost_ru }}" aria-describedby="input-cost_ru" readonly>
                <div id="input-cost_ru" class="input-group-text">₽</div>
            </div>
            <div class="w-40 input-group">
                <input id="sell-{{ $item->id }}" type="number" class="form-control text-right arrival-input-listen"
                       value="{{ $item->price_sell }}" aria-describedby="input-sell" min="0" readonly>
                <div id="input-sell" class="input-group-text">₽</div>
            </div>
            @if(!$arrival->isCompleted())
                <button class="btn btn-outline-danger ml-6" type="button" onclick="document.getElementById('form-delete-item-{{ $item->id }}').submit();">
                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                </button>
                <form id="form-delete-item-{{ $item->id }}" method="post" action="{{ route('admin.accounting.arrival.remove-item', $item) }}">
                    @method('DELETE')
                    @csrf
                </form>
            @endif
        </div>
    @endforeach

    <div class="box flex items-center px-2 mt-3">
        <div class="w-20"></div>
        <div class="w-1/4">ИТОГО</div>
        <div class="w-40 input-group">
            <input id="currency-amount" type="number" class="form-control text-right arrival-input-listen"
                   value="{{ $info['cost_currency'] }}" aria-describedby="input-currency" min="0" readonly>
            <div id="input-currency" class="input-group-text">{{ $arrival->currency->sign }}</div>
        </div>
        <div class="w-40 input-group">
            <input id="quantity-amount" type="number" class="form-control text-right arrival-input-listen"
                   value="{{ $info['quantity'] }}" aria-describedby="input-quantity" min="0" readonly>
            <div id="input-quantity" class="input-group-text">шт.</div>
        </div>
        <div class="w-40 input-group">
            <input id="cost_ru-amount" type="number" class="form-control text-right"
                   value="{{ $info['cost_ru'] }}" aria-describedby="input-cost_ru" readonly>
            <div id="input-cost_ru" class="input-group-text">₽</div>
        </div>
        <div class="w-40 input-group">
            <input id="sell-amount" type="number" class="form-control text-right arrival-input-listen"
                   value="{{ $info['price_sell'] }}" aria-describedby="input-sell" min="0" readonly>
            <div id="input-sell" class="input-group-text">₽</div>
        </div>
    </div>


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить товар из списка?<br>Этот процесс не может быть отменен.')->show() }}



    <script>
        let elementListens = document.querySelectorAll('.arrival-input-listen');
        let arrayListens = Array.prototype.slice.call(elementListens);
        arrayListens.forEach(function (element) {
            element.addEventListener('change', function (item) {
                let route = element.parentElement.parentElement.getAttribute('data-route');
                let id = element.parentElement.parentElement.getAttribute('data-id');
                let data = {
                    currency: document.getElementById('currency-' + id).value,
                    quantity: document.getElementById('quantity-' + id).value,
                    sell: document.getElementById('sell-' + id).value,
                };

                console.log(route, element.value);
                let result = document.getElementById('cost_ru-' + id);
                setArrivalPoduct(route, data, result);
            })
        });

        function setArrivalPoduct(route, data, result) {
            //AJAX
            let _params = '_token=' + '{{ csrf_token() }}' + '&quantity=' + data.quantity + '&cost=' + data.currency + '&price=' + data.sell;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText)
                    result.value = data.cost_ru;
                    document.getElementById('currency-amount').value = data.info.cost_currency;
                    document.getElementById('quantity-amount').value = data.info.quantity;
                    document.getElementById('cost_ru-amount').value = data.info.cost_ru;
                    document.getElementById('sell-amount').value = data.info.price_sell;

                } else {
                    //console.log(request.responseText);
                }
            };
        }
    </script>
@endsection
