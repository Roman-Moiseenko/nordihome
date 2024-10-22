@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            {{ $pricing->htmlNum() . ' от ' . $pricing->htmlDate() }}
        </h2>
    </div>
    @if(!$pricing->isCompleted())
        <div class="box flex p-5 items-center">
            <x-searchAddProduct route-save="{{ route('admin.accounting.pricing.add', $pricing) }}" quantity="0"/>
            <x-listCodeProducts route="{{ route('admin.accounting.pricing.add-products', $pricing) }}" caption-button="Добавить товары в документ" class="ml-3"/>
            <button type="button" class="ml-auto btn btn-danger" onclick="document.getElementById('form-completed').submit();">Провести документ</button>
            <form id="form-completed" method="post" action="{{ route('admin.accounting.pricing.completed', $pricing) }}">
                @csrf
            </form>
        </div>
    @endif

    <div class="box flex items-center font-semibold p-2 mt-3">
        <div class="w-20 text-center">№ п/п</div>
        <div class="w-32">Артикул</div>
        <div class="w-56 text-center">Товар</div>
        <div class="w-40 text-center">Себестоимость (₽)</div>
        <div class="w-40 text-center">Розн. цена (₽)</div>
        <div class="w-40 text-center">Опт. цена (₽)</div>
        <div class="w-40 text-center">Спец. цена (₽)</div>
        <div class="w-40 text-center">Мин. цена (₽)</div>
        <div class="w-40 text-center">Заказ. цена (₽)</div>
    </div>


    @foreach($pricing->pricingProducts as $i => $item)
        <div class="box flex items-center px-2"
             data-route="{{ route('admin.accounting.pricing.set', $item->id) }}">
            <div class="w-20 text-center">{{ ($i + 1) }}</div>
            <div class="w-32">{{ $item->product->code }}</div>
            <div class="w-56 ">{{ $item->product->name }}</div>

            <div class="w-40 input-group">
                <input type="number" class="form-control text-right" value="{{ $item->product->getPriceCost($pricing->isCompleted()) }}" readonly>
                <input id="price_cost-{{ $item->id }}" type="number" name="price_cost"
                       class="form-control text-right pricing-input-listen" value="{{ $item->price_cost }}"
                       @if($pricing->isCompleted()) readonly @endif autocomplete="off">
            </div>
            <div class="w-40 input-group">
                <input type="number" class="form-control text-right" value="{{ $item->product->getPriceRetail($pricing->isCompleted()) }}" readonly>
                <input id="price_retail-{{ $item->id }}" type="number" name="price_retail"
                       class="form-control text-right pricing-input-listen" value="{{ $item->price_retail }}"
                       @if($pricing->isCompleted()) readonly @endif autocomplete="off">
            </div>
            <div class="w-40 input-group">
                <input type="number" class="form-control text-right" value="{{ $item->product->getPriceBulk($pricing->isCompleted()) }}" readonly>
                <input id="price_bulk-{{ $item->id }}" type="number" name="price_bulk"
                       class="form-control text-right pricing-input-listen" value="{{ $item->price_bulk }}"
                       @if($pricing->isCompleted()) readonly @endif autocomplete="off">
            </div>
            <div class="w-40 input-group">
                <input type="number" class="form-control text-right" value="{{ $item->product->getPriceSpecial($pricing->isCompleted()) }}" readonly>
                <input id="price_special-{{ $item->id }}" type="number"  name="price_special"
                       class="form-control text-right pricing-input-listen" value="{{ $item->price_special }}"
                       @if($pricing->isCompleted()) readonly @endif autocomplete="off">
            </div>
            <div class="w-40 input-group">
                <input type="number" class="form-control text-right" value="{{ $item->product->getPriceMin($pricing->isCompleted()) }}" readonly>
                <input id="price_min-{{ $item->id }}" type="number"  name="price_min"
                       class="form-control text-right pricing-input-listen" value="{{ $item->price_min }}"
                       @if($pricing->isCompleted()) readonly @endif autocomplete="off">
            </div>
            <div class="w-40 input-group">
                <input type="number" class="form-control text-right" value="{{ $item->product->getPricePre($pricing->isCompleted()) }}" readonly>
                <input id="price_pre-{{ $item->id }}" type="number"  name="price_pre"
                       class="form-control text-right pricing-input-listen" value="{{ $item->price_pre }}"
                       @if($pricing->isCompleted()) readonly @endif autocomplete="off">
            </div>

            @if(!$pricing->isCompleted())
                <button class="btn btn-outline-danger ml-6" type="button" onclick="document.getElementById('form-delete-item-{{ $item->id }}').submit();">
                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                </button>
                <form id="form-delete-item-{{ $item->id }}" method="post" action="{{ route('admin.accounting.pricing.remove-item', $item) }}">
                    @method('DELETE')
                    @csrf
                </form>
            @endif
        </div>
    @endforeach



    <div class="box mt-3 p-5">
        <livewire:admin.accounting.edit-comment :document="$pricing" />
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить товар из списка?<br>Этот процесс не может быть отменен.')->show() }}



    <script>
        let elementListens = document.querySelectorAll('.pricing-input-listen');
        let arrayListens = Array.prototype.slice.call(elementListens);
        arrayListens.forEach(function (element) {
            element.addEventListener('change', function (item) {
                let route = element.parentElement.parentElement.dataset.route;
                let name = element.getAttribute('name');
                let value = element.value;
                element.disabled = true;
                //console.log(route, name, value);
                setPricing(route, name, value, element);
            })
        });

        function setPricing(route, name, value, element) {
            //AJAX
            let _params = '_token=' + '{{ csrf_token() }}' + '&' + name + '=' + value;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText)
                    console.log(data)
                } else {
                    //console.log(request.responseText);
                }
                element.disabled = false;
            };
        }
    </script>
@endsection
