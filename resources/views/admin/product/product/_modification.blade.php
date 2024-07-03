@php
    $colorForAttr = ['primary', 'success', 'warning'];
@endphp
<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        @if(is_null($product->modification))
            <div class="w-full text-slate-400 mt-6">
                Для данного товара Модификации не заданы. Создать группу модификаций можно в разделе
                <a class="text-primary"
                    href="{{ route('admin.product.modification.create') }}"
                    target="_blank">Модификации</a>
            </div>
        @else
            <div class="font-medium ml-3 text-lg text-primary">
                {{ $product->modification->name }}
            </div>
            <div>
                @foreach($product->modification->prod_attributes as $key => $attribute)
                    <div class="mt-3 flex items-center">
                        <div class="image-fit w-10 h-10">
                            <img class="rounded-full" src="{{ $attribute->getImage() }}"
                                 alt="{{ $attribute->name }}">
                        </div>
                        <div class="font-medium ml-3 text-lg text-primary">
                            {{ $attribute->name }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="w-full text-slate-400 mt-6">
                Изменить товары в модификации или удалить товар из списка можно в разделе
                <a class="text-primary"
                   href="{{ route('admin.product.modification.show', $product->modification) }}"
                   target="_blank">Модификации</a>
            </div>
        @endif

    </div>
    <div class="col-span-12 lg:col-span-8">
        @if(!empty($product->modification))
        @foreach($product->modification->products as $_product)
            <div class="relative pl-5 pr-5 xl:pr-10 py-10 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3 flex items-center">
                <div class="flex items-center">
                    <div class="image-fit w-10 h-10">
                        <img class="rounded-full" src="{{ $_product->getImage() }}" alt="{{ $_product->name }}">
                    </div>
                    <div class="ml-3">
                        <a href="{{ route('admin.product.show', $_product) }}"
                           class="font-medium ">{{ $_product->name }}</a>
                    </div>
                    <div class="ml-3 font-medium whitespace-nowrap">{{ $_product->code }}</div>
                </div>
                <div class="ml-auto">
                    @php
                        $j = 0;
                    @endphp

                    @foreach(json_decode($_product->pivot->values_json, true) as $attr_id => $variant_id)
                        @if(!is_null($_product->getProdAttribute($attr_id)))
                        <span class="px-3 py-2 mr-3 rounded-full whitespace-nowrap border border-{{ $colorForAttr[$j] }} text-{{ $colorForAttr[$j] }}">
                    {{ $_product->getProdAttribute($attr_id)->getVariant($variant_id)->name }}
                </span>
                        @php $j++; @endphp
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
        @endif
    </div>
</div>


