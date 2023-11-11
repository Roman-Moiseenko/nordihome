@extends('layouts.side-menu')

@section('subcontent')
    @php
        $colorForAttr = ['primary', 'success', 'warning'];
    @endphp
    <script>
        function AddModification(_n) {
            let spanDataArray = document.querySelectorAll('.span-attr-' + _n);
            let selectData = document.getElementById('modification-' + _n);
            let _data_array = [];
            spanDataArray.forEach(function (item) {
                _data_array.push({
                    attribute: Number(item.getAttribute('data-attribute')),
                    variant: Number(item.getAttribute('data-variant'))
                })
            });
            let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + selectData.getAttribute('data-id') + '&values=' + JSON.stringify(_data_array);
            let request = new XMLHttpRequest();
            request.open('POST', '/admin/product/modification/' + {{ $modification->id }} + '/add-product');
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let _result = JSON.parse(request.responseText);
                    if (_result !== true) window.scrollTo(0,0);
                    window.location.reload();
                }
            };
        }
    </script>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $modification->name }}
        </h2>
    </div>
    <div class="box p-5 mt-3">
        <div class="font-medium text-dark">
            Базовый товар: {{ $modification->base_product->name }} ({{ $modification->base_product->code }})
        </div>
        @foreach($modification->prod_attributes as $key => $attribute)
            <div class="mt-3 flex items-center">
                <div class="image-fit w-10 h-10">
                    <img class="rounded-full" src="{{ $attribute->getImage() }}"
                         alt="{{ $attribute->name }}">
                </div>
                <div class="font-medium ml-3 text-lg text-primary">
                    {{ $attribute->name }}
                </div>
            </div>
            <div class="flex ml-6 pl-10 flex-wrap">
                @foreach($attribute->variants as $variant)
                    <div class="ml-3 flex items-center">
                        <div class="image-fit w-6 h-6">
                            <img class="rounded-full" src="{{ $variant->getImage() }}"
                                 alt="{{ $variant->name }}">
                        </div>
                        <div class="ml-1 font-medium">
                            @if($key == 0)
                                <a href="#{{ $variant->name }}">{{ $variant->name }}</a>
                            @else
                                {{ $variant->name }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Список товаров
        </h2>
    </div>
    @foreach($modification->products as $product)
        <div class="box m-5 p-5 flex items-center">
            <div class="flex items-center">
                <div class="image-fit w-10 h-10">
                    <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
                </div>
                <div class="ml-3">
                    <a href="{{ route('admin.product.show', $product) }}"
                       class="font-medium whitespace-nowrap">{{ $product->name }}</a>
                </div>
            </div>
            <div class="ml-3">
                @php
                    $j = 0;
                @endphp
                @foreach(json_decode($product->pivot->values_json, true) as $attr_id => $variant_id)
                    <span class="px-3 py-2 mr-3 rounded-full border border-{{ $colorForAttr[$j] }} text-{{ $colorForAttr[$j] }}">
                        {{ $product->getProdAttribute($attr_id)->getVariant($variant_id)->name }}
                    </span>
                    @php $j++; @endphp
                @endforeach
            </div>
            <div class="ml-3">
                <a class="flex items-center text-danger" href="#"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                   data-route = {{ route('admin.product.modification.del-product', ['modification' => $modification, 'product_id' => $product->id]) }}
                ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                    Delete </a>
            </div>
        </div>
    @endforeach
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавить товары в модификацию
        </h2>
    </div>

    @foreach($modification->getVariations(true) as $i => $variation)
        <div class="box m-5 p-5 flex items-center">
            <div>
                @foreach($modification->prod_attributes as $j => $attribute)
                    <!--span class="text-{{ $colorForAttr[$j] }} font-medium">{{ $attribute->name }}</span-->
                    <span {{ $j == 0 ? 'id=' . $attribute->getVariant($variation[$attribute->id])->name : '' }}
                           class="span-attr-{{ $i }} px-3 py-2 mr-6 rounded-full border border-{{ $colorForAttr[$j] }} text-{{ $colorForAttr[$j] }}"
                        data-attribute="{{ $attribute->id }}" data-variant="{{ $variation[$attribute->id] }}">
                {{ $attribute->getVariant($variation[$attribute->id])->name }}
            </span>
                @endforeach
            </div>
            <div class="w-1/4">
                <x-searchproduct route="{{ route('admin.product.modification.search', ['action' => 'show']) }}"
                                 input-data="modification-{{ $i }}"
                                 class="w-full" callback="AddModification({{ $i }})"/>
            </div>
        </div>
    @endforeach

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить товар из группы?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
