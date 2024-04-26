@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8 mb-3">
        <h2 class="text-lg font-medium mr-auto">
            {{ $movement->number . ' от ' . $movement->created_at->format('d-m-Y') . ' (' . $movement->storageOut->name. ' -> ' . $movement->storageIn->name . ')' }}
        </h2>

    </div>
    @if(!empty($movement->order()))
    <div class="m-2 p-2 box">
        <a class="text-success font-medium" href="{{ route('admin.sales.order.show', $movement->order()) }}" target="_blank">
            Заказ {{ $movement->order()->htmlNum() . ' от ' . $movement->order()->htmlDate()}}
        </a>
    </div>
    @endif
    @if($movement->isDraft())

        <div class="box flex p-3 items-center flex w-full">
            @if(empty($movement->order()))
            <form action="{{ route('admin.accounting.movement.add', $movement) }}" method="POST">
                @csrf
                <div class="mx-3 flex">
                    <x-searchProduct route="{{ route('admin.accounting.movement.search', $movement) }}"
                                     input-data="movement-product" hidden-id="product_id" class="w-56"/>
                    {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->show() }}
                    <x-base.button id="add-product" type="submit" variant="primary" class="ml-3">Добавить товар в документ
                    </x-base.button>
                </div>
            </form>
            @endif
            <button type="button" class="ml-auto btn btn-danger" onclick="document.getElementById('form-activate').submit();">Активировать документ</button>
        </div>

    @endif
    <form id="form-activate" method="post" action="{{ route('admin.accounting.movement.activate', $movement) }}">
        @csrf
    </form>

    @if($movement->isDeparture())
        <form action="{{ route('admin.accounting.movement.departure', $movement) }}" method="POST">
            @csrf
            <div class="box flex p-3 items-center">
                <div class="mx-3 flex w-full">
                    <div class="text-lg font-medium">СТАТУС: {{ $movement->statusHTML() }}</div>
                    <button type="submit" class="ml-auto btn btn-danger">Груз отправлен</button>
                </div>
            </div>
        </form>
    @endif
    @if($movement->isArrival())
        <form action="{{ route('admin.accounting.movement.arrival', $movement) }}" method="POST">
            @csrf
            <div class="box flex p-3 items-center">
                <div class="mx-3 flex w-full">
                    <div class="text-lg font-medium">СТАТУС: {{ $movement->statusHTML() }}</div>
                    <button type="submit" class="ml-auto btn btn-danger">Груз прибыл</button>
                </div>
            </div>
        </form>
    @endif

    @if($movement->isCompleted())
        <div class="box flex p-3 items-center">
            <div class="mx-3 flex w-full">
                <div class="text-lg font-medium">СТАТУС: {{ $movement->statusHTML() }}</div>
            </div>
        </div>
    @endif

    <div class="box flex items-center font-semibold p-2 mt-3">
        <div class="w-20 text-center">№ п/п</div>
        <div class="w-1/4 text-center">Товар</div>
        <div class="w-40 text-center">Кол-во</div>
        <div class="w-40 text-center">{{ $movement->storageOut->name }}</div>
        <div class="w-40 text-center">{{ $movement->storageIn->name }}</div>


    </div>
    @foreach($movement->movementProducts as $i => $item)
        <div class="box flex items-center px-2" data-id="{{ $item->id }}"
             data-route="{{ route('admin.accounting.movement.set', $item->id) }}">
            <div class="w-20">{{ ($i + 1) }}</div>
            <div class="w-1/4">{{ $item->product->name }}</div>

            <div class="w-40 input-group">
                <input id="quantity-{{ $item->id }}" type="number" class="form-control text-right movement-input-listen"
                       value="{{ $item->quantity }}" aria-describedby="input-quantity" min="0"
                       @if(!empty($movement->order()))
                           max="{{ $item->quantity }}"
                       @endif
                   @if(!$movement->isDraft())
                       readonly
                    @endif

                >
                <div id="input-quantity" class="input-group-text">шт.</div>
            </div>


            <div class="w-40 input-group">
                <input id="cost-{{ $item->id }}" type="number" class="form-control text-right departure-input-listen"
                       value="{{ $item->product->getQuantity($movement->storageOut->id) }}" aria-describedby="input-quantity" min="0" readonly>
                <div id="input-quantity" class="input-group-text">шт</div>
            </div>
            <div class="w-40 input-group">
                <input id="cost-{{ $item->id }}" type="number" class="form-control text-right departure-input-listen"
                       value="{{ $item->product->getQuantity($movement->storageIn->id) }}" aria-describedby="input-quantity" min="0" readonly>
                <div id="input-quantity" class="input-group-text">шт</div>
            </div>
            @if($movement->isDraft())
                <button class="btn btn-outline-danger ml-6" type="button" onclick="document.getElementById('form-delete-item-{{ $item->id }}').submit();">
                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                </button>
                <form id="form-delete-item-{{ $item->id }}" method="post" action="{{ route('admin.accounting.movement.remove-item', $item) }}">
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
            <input id="quantity-amount" type="number" class="form-control text-right movement-input-listen"
                   value="{{ $info['quantity'] }}" aria-describedby="input-quantity" min="0" readonly>
            <div id="input-quantity" class="input-group-text">шт.</div>
        </div>

    </div>


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить товар из списка?<br>Этот процесс не может быть отменен.')->show() }}



    <script>
        let elementListens = document.querySelectorAll('.movement-input-listen');
        let arrayListens = Array.prototype.slice.call(elementListens);
        arrayListens.forEach(function (element) {
            element.addEventListener('change', function (item) {
                let route = element.parentElement.parentElement.getAttribute('data-route');
                let id = element.parentElement.parentElement.getAttribute('data-id');
                let data = {
                    quantity: document.getElementById('quantity-' + id).value,
                };
                setMovementProduct(route, data);
            })
        });

        function setMovementProduct(route, data) {
            //AJAX
            let _params = '_token=' + '{{ csrf_token() }}' + '&quantity=' + data.quantity;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText);
                    if (data.quantity !== undefined) {
                        document.getElementById('quantity-amount').value = data.quantity;
                       // document.getElementById('cost-amount').value = data.cost;
                    }
                } else {
                }
            };
        }
    </script>
@endsection
