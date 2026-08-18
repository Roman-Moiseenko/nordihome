<!--template:3 группы товаров слайдом-->
@php
    /**
    * $widget->name
    * $widget->url
    * $widget->caption
    * $widget->description
    * $widget->products - array Products
 */

       use App\Modules\Content\Entity\Widgets\ProductWidget;
       /** @var ProductWidget $widget  */
@endphp
<div class="text-center mt-5 py-4 widget-home-3-group">
    <h2 class="fw-semibold mt-5">{{ $widget->caption }}</h2>
    <h3>{{ $widget->description }}</h3>


    @foreach($widget->products(5) as $_product)
        <div style="scroll-snap-align: start;max-width: 100%; overflow: hidden;text-align: left;">
            <a href="{{ route('shop.product.view', $_product->slug) }}"
               style="max-width: 100%; overflow: hidden;">
                <img loading="lazy" src="{{ $_product->getImage('catalog') }}"
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


    <a href="{{ $widget->getUrl() }}">Все товары</a>
    </div>
</div>
