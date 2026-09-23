<!--template:Слайдер товаров -->
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
<div class="text-center mt-5 py-4 widget-home-3-group container-xl">
    <h2 class="fw-semibold mt-5">{{ $widget->caption }}</h2>
    <h3>{{ $widget->description }}</h3>

    <div id="" class="owl-carousel owl-theme slider-images-product">
        @foreach($widget->products(12) as $_product)
            <div style="">
                <a href="{{ route('shop.product.view', $_product->slug) }}"
                   style="max-width: 100%; overflow: hidden;">
                    <img loading="lazy" src="{{ $_product->getImage('catalog') }}"
                         alt="{{ $_product->getName() }}" style="width: 100%;"/>
                </a>
                <a href="{{ route('shop.product.view', $_product->slug) }}">
                    <div class="name">{{ $_product->getName() }}</div>
                    <div class="price">{{ price($_product->getPrice()) }}</div>
                </a>
                <div>
                    <button class="to-cart btn btn-black e-add" data-product="{{$_product->id}}">
                        В Корзину
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @if(!empty($widget->getUrl()))
        <div class="t-a_center m-t_10">
            <a href="{{ $widget->getUrl() }}">{{ $widget->button_name }}</a>
        </div>
    @endif
</div>
