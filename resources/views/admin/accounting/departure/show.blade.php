@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $departure->number . ' от ' . $departure->created_at->format('d-m-Y') . ' (' . $departure->storage->name. ')' }}
        </h2>
    </div>
    @if(!$departure->isCompleted())
        <div class="box flex p-5 items-center">
            <form action="{{ route('admin.accounting.departure.add', $departure) }}" method="POST">
                @csrf
                <div class="mx-3 flex w-full">
                    <x-searchProduct route="{{ route('admin.accounting.departure.search', $departure) }}"
                                     input-data="departure-product" hidden-id="product_id" class="w-56"/>
                    {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->show() }}
                    <x-base.button id="add-product" type="submit" variant="primary" class="ml-3">Добавить товар в документ</x-base.button>
                </div>
            </form>
            <x-listCodeProducts route="{{ route('admin.accounting.departure.add-products', $departure) }}" caption-button="Добавить товары в документ" class="ml-3"/>
            <button type="button" class="ml-auto btn btn-danger" onclick="document.getElementById('form-completed').submit();">Провести документ</button>
            <form id="form-completed" method="post" action="{{ route('admin.accounting.departure.completed', $departure) }}">
                @csrf
            </form>
        </div>
    @endif

    <div class="box flex items-center font-semibold p-2 mt-3">
        <div class="w-20">№ п/п</div>
        <div class="w-32">Артикул</div>
        <div class="w-1/4">Товар</div>
        <div class="w-40 text-center">Кол-во</div>
        <div class="w-40 text-center">Цена</div>
    </div>
    @foreach($departure->departureProducts as $i => $item)
        <div class="box flex items-center px-2" data-id="{{ $item->id }}"
             data-route="{{ route('admin.accounting.departure.set', $item->id) }}">
            <div class="w-20">{{ ($i + 1) }}</div>
            <div class="w-32">{{ $item->product->code }}</div>
            <div class="w-1/4">{{ $item->product->name }}</div>
            <div class="w-40 input-group">
                <input id="quantity-{{ $item->id }}" type="number" class="form-control text-right departure-input-listen"
                       value="{{ $item->quantity }}" aria-describedby="input-quantity" min="0" {{ $departure->isCompleted() ? 'readonly' : '' }}>
                <div id="input-quantity" class="input-group-text">шт.</div>
            </div>
            <div class="w-40 input-group">
                <input id="cost-{{ $item->id }}" type="number" class="form-control text-right departure-input-listen"
                       value="{{ $item->cost }}" aria-describedby="input-quantity" min="0" readonly>
                <div id="input-quantity" class="input-group-text">₽</div>
            </div>
            @if(!$departure->isCompleted())
                <button class="btn btn-outline-danger ml-6" type="button" onclick="document.getElementById('form-delete-item-{{ $item->id }}').submit();">
                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                </button>
                <form id="form-delete-item-{{ $item->id }}" method="post" action="{{ route('admin.accounting.departure.remove-item', $item) }}">
                    @method('DELETE')
                    @csrf
                </form>
            @endif
        </div>
    @endforeach

    <div class="box flex items-center px-2 mt-3">
        <div class="w-20"></div>
        <div class="w-32"></div>
        <div class="w-1/4">ИТОГО</div>

        <div class="w-40 input-group">
            <input id="quantity-amount" type="number" class="form-control text-right departure-input-listen"
                   value="{{ $info['quantity'] }}" aria-describedby="input-quantity" min="0" readonly>
            <div id="input-quantity" class="input-group-text">шт.</div>
        </div>
        <div class="w-40 input-group">
            <input id="cost-amount" type="number" class="form-control text-right departure-input-listen"
                   value="{{ $info['cost'] }}" aria-describedby="input-quantity" min="0" readonly>
            <div id="input-quantity" class="input-group-text">₽</div>
        </div>
    </div>

    <div class="box mt-3 p-5">
        <livewire:admin.accounting.edit-comment :document="$departure" />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить товар из списка?<br>Этот процесс не может быть отменен.')->show() }}

    <script>
        let elementListens = document.querySelectorAll('.departure-input-listen');
        let arrayListens = Array.prototype.slice.call(elementListens);
        arrayListens.forEach(function (element) {
            element.addEventListener('change', function (item) {
                let route = element.parentElement.parentElement.getAttribute('data-route');
                let id = element.parentElement.parentElement.getAttribute('data-id');
                let data = {
                    quantity: document.getElementById('quantity-' + id).value,
                };

                setDepartureProduct(route, data);
            })
        });

        function setDepartureProduct(route, data) {
            //AJAX
            let _params = '_token=' + '{{ csrf_token() }}' + '&quantity=' + data.quantity;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText);
                    console.log(data);
                    if (data.quantity !== undefined) {
                        document.getElementById('quantity-amount').value = data.quantity;
                        document.getElementById('cost-amount').value = data.cost;
                    }
                } else {
                }
            };
        }
    </script>
@endsection
