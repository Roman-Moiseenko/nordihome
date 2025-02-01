<!--template:3 группы товаров слайдом-->
@php
    /**
    * $widget->banner - Banner::class
    * $widget->name
    * $widget->url
    * $widget->caption
    * $widget->description
    * $widget->items - array WidgetItem
    * $item->slug
    * $item->name
    * $item->url
    * $item->caption
    * $item->description
    * $item-group - Group::class
    */

       use App\Modules\Page\Entity\Widget;
       /** @var Widget $widget  */
@endphp
<div class="text-center mt-5 py-4 widget-home-3-group">
    <h2 class="fw-semibold mt-5">{{ $widget->caption }}</h2>
    <h3>{{ $widget->description }}</h3>
    <ul class="caption-group">
        @foreach($widget->items as $i => $item)
            <li class="{{ $i == 0 ? 'active' : '' }}" data-id="tab-item-{{$item->id}}">
                {{ $item->caption }}
            </li>
        @endforeach
    </ul>
    <div class="slider-group">
        @foreach($widget->items as $i => $item)
            <div id="tab-item-{{$item->id}}" class="{{ $i == 0 ? '' : 'hidden' }}">
                <div class="owl-carousel owl-theme slider-best-group">
                    @foreach($item->group->products as $product)
                        <div style="scroll-snap-align: start;max-width: 100%; overflow: hidden;">
                            <a href="{{ route('shop.product.view', $product->slug) }}" style="max-width: 100%; overflow: hidden;">
                                <img loading="lazy" src="{{ $product->getImage('slide') }}" alt="{{ $product->name }}"  style="width: 100%;"/>
                            </a>
                            <a href="{{ route('shop.product.view', $product->slug) }}" class="flex justify-content-between">
                                <div>{{ $product->name }}</div>
                                <div>{{ price($product->getPriceRetail()) }}</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
