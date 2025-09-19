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

       use App\Modules\Page\Entity\ProductWidget;
       /** @var ProductWidget $product  */
@endphp
<div class="text-center mt-5 py-4 widget-home-3-group">
    <h2 class="fw-semibold mt-5">{{ $product->caption }}</h2>
    <h3>{{ $product->description }}</h3>
    <ul class="caption-group">
        @foreach($product->items as $i => $item)
            <li class="{{ $i == 0 ? 'active' : '' }}" data-id="tab-item-{{$item->id}}">
                {{ $item->caption }}
            </li>
        @endforeach
    </ul>
    <div class="slider-group">
        @foreach($product->items as $i => $item)
            <div id="tab-item-{{$item->id}}" class="{{ $i == 0 ? '' : 'hidden' }}">
                <div class="owl-carousel owl-theme slider-best-group">
                    @foreach($item->group->products as $_product)
                        <div style="scroll-snap-align: start;max-width: 100%; overflow: hidden;text-align: left;">
                            <a href="{{ route('shop.product.view', $_product->slug) }}"
                               style="max-width: 100%; overflow: hidden;">
                                <img loading="lazy" src="{{ $_product->getImage('slide') }}"
                                     alt="{{ $_product->getName() }}" style="width: 100%;"/>
                            </a>
                            <a href="{{ route('shop.product.view', $_product->slug) }}">
                                <div class="d-flex justify-content-between">
                                    <div class="name">{{ $_product->getName() }}</div>
                                    <div class="price">{{ price($_product->getPrice()) }}</div>
                                </div>
                                <div class="category">
                                    {{ $_product->category->name }}
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
