@php
    use App\Modules\Shop\Application\DTOs\Elements\UrlData;use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomSecondData;use App\Modules\Shop\Application\DTOs\PageElements\FilterData;
    /** @var FilterData  $filters*/
    /** @var CategoryRoomSecondData $secondInfo */
    /** @var CategoryRoomMainData $mainInfo */
    /** @var UrlData $back */

@endphp
<div class="filters">
    <div class="mobile-close"><i class="fa-light fa-xmark"></i></div>
    <div class="base-filter">
        <div class="children">
            <a href="{{ $mainInfo->back->url }}" class="heading">{{ $mainInfo->back->name }}</a>
            <div>
                <b>{{ $mainInfo->name }}</b>
            </div>
            @foreach($mainInfo->children as $child)
                <div class="m-l_10">
                    <a href="{{ route('shop.' . $mainInfo->entity. '.view', $child->slug) }}">{{ $child->name }}</a>
                </div>
            @endforeach
        </div>
        <br>

        <div class="children">
            <a href="{{ $secondInfo->back->url}}" class="heading">{{ $secondInfo->back->name }}</a>
            @foreach($secondInfo->children as $child)
                <div>
                    <a href="{{ route('shop.' . $secondInfo->entity. '.view', $child->slug) }}">{{ $child->name }}</a>
                </div>
            @endforeach
        </div>
        <x-widget.numeric name="price" min-value="{{ $filters->minPrice }}" max-value="{{ $filters->maxPrice }}"
                          current-min="{{ isset($request['price']) ? $request['price'][0] : '' }}"
                          current-max="{{ isset($request['price']) ? $request['price'][1] : '' }}"
                          class="mt-3">
            Цена
        </x-widget.numeric>

        <x-widget.check name="discount" class="mt-3" checked="{{ isset($request['discount']) }}">Акция</x-widget.check>
    </div>
    <div class="attribute-filter">
        @foreach($filters->attributes as $attribute)
            <div>
                @if(isset($attribute['isBool']))
                    <x-widget.check name="a_{{ $attribute['id'] }}" class="mt-2" value="{{ $attribute['id'] }}"
                                    checked="{{ isset($request['a_' . $attribute['id']]) }}">
                        {{ $attribute['name'] }}</x-widget.check>
                    <hr/>
                @endif
                @if(isset($attribute['isNumeric']))
                    <x-widget.numeric name="a_{{ $attribute['id'] }}" min-value="{{ $attribute['min'] }}"
                                      max-value="{{ $attribute['max'] }}"
                                      current-min="{{ isset($request['a_' . $attribute['id']]) ? $request['a_' . $attribute['id']][0] : '' }}"
                                      current-max="{{ isset($request['a_' . $attribute['id']]) ? $request['a_' . $attribute['id']][1] : '' }}"
                                      class="mt-3">
                        {{ $attribute['name'] }}
                    </x-widget.numeric>
                    <hr/>
                @endif
                @if(isset($attribute['isVariant']))
                    <x-widget.variant class="mt-3" caption="{{ $attribute['name'] }}" id="{{ $attribute['id'] }}">
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
