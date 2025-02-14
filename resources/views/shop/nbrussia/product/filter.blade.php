<div class="filters">
    <div class="mobile-close"><i class="fa-light fa-xmark"></i></div>
    <div class="base-filter">
        @foreach($children as $child)
            <div>
                <a href="{{ route('shop.category.view', $child['slug']) }}">{{ $child['name'] }}</a>
            </div>
        @endforeach


        <x-widget.numeric name="price" min-value="{{ $minPrice }}" max-value="{{ $maxPrice }}"
                          current-min="{{ isset($request['price']) ? $request['price'][0] : '' }}"
                          current-max="{{ isset($request['price']) ? $request['price'][1] : '' }}"
                          class="mt-3">
            Цена
        </x-widget.numeric>

        <x-widget.check name="discount" class="mt-3" checked="{{ isset($request['discount']) }}" >Акция</x-widget.check>
    </div>
    <div class="attribute-filter">
        @foreach($prod_attributes as $attribute)
            <div>
                @if(isset($attribute['isBool']))
                    <x-widget.check name="a_{{ $attribute['id'] }}" class="mt-2" value="{{ $attribute['id'] }}"
                                    checked="{{ isset($request['a_' . $attribute['id']]) }}">
                        {{ $attribute['name'] }}</x-widget.check>
                    <hr/>
                @endif
                @if(isset($attribute['isNumeric']))
                    <x-widget.numeric name="a_{{ $attribute['id'] }}" min-value="{{ $attribute['min'] }}" max-value="{{ $attribute['max'] }}"
                                      current-min="{{ isset($request['a_' . $attribute['id']]) ? $request['a_' . $attribute['id']][0] : '' }}"
                                      current-max="{{ isset($request['a_' . $attribute['id']]) ? $request['a_' . $attribute['id']][1] : '' }}"
                                      class="mt-3">
                        {{ $attribute['name'] }}
                    </x-widget.numeric>
                        <hr/>
                @endif
                @if(isset($attribute['isVariant']))
                    <x-widget.variant class="mt-3" caption="{{ $attribute['name'] }}">
                        @foreach($attribute['variants'] as $variant)
                            <x-widget.variant-item name="a_{{ $attribute['id'] }}[]" id="{{ $variant['id'] }}"
                                                   caption="{{ $variant['name'] }}"
                                                   image="{{ $variant['image'] }}"
                                                   checked="{{ isset($request['a_' . $attribute['id']]) ? in_array($variant['id'], $request['a_' . $attribute['id']]) : false }}"
                                                   alt="{{ $variant['name'] }}"
                            />
                        @endforeach
                    </x-widget.variant>
                        <hr/>
                @endif

            </div>
        @endforeach
    </div>
    <div class="buttons-filter">
        <button class="btn btn-dark w-auto">Применить</button>
        <button id="clear-filter" class="btn btn-outline-dark w-auto" type="button">Сбросить</button>

    </div>
</div>
