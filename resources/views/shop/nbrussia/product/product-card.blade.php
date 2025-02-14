<div>
    <div class="product-card">
        <div class="product-card-image">
            <a href="{{ route('shop.product.view', $product['slug']) }}">
                <img class="product-card-image-main"
                     src="{{ $product['images']['catalog']['src'] }}"
                     alt="{{ $product['images']['catalog']['alt'] }}">
                <img class="product-card-image-hover"
                     src="{{ $product['images-next']['catalog']['src'] }}"
                     alt="{{ $product['images-next']['catalog']['alt'] }}">

                @if($product['promotion']['has'])
                    <div class="product-label promotion"><span>Акция</span></div>
                @endif
                @if($product['is_new'])
                    <div class="product-label new"><span>Новинка</span></div>
                @endif

                <div class="product-label wish">
                    @if(!is_null($user))
                        <button class="{{ $product['is_wish'] ? 'is-wish' : 'to-wish' }}" type="button"
                                title="В Избранное">
                            <i class="{{ $product['is_wish'] ? 'fa-solid' : 'fa-light' }} fa-heart"></i>
                        </button>
                    @else
                        <button class="to-wish" data-bs-toggle="modal" data-bs-target="#login-popup"
                                type="button"
                                onclick="event.preventDefault();">
                            <i class="fa-light fa-heart" type="button" title="В Избранное"></i>
                        </button>
                    @endif
                </div>
            </a>
        </div>

        @if(!is_null($product['modification']))
        <div class="modification">
            @foreach($product['modification'] as $attribute)
                @foreach($attribute['products'] as $value => $_product_mod)
                    <a href="{{ route('shop.product.view', $product['slug']) }}" title="{{ $product['name'] }}"> {{ $value }}</a>
                @endforeach
            @endforeach
        </div>
        @endif
        <div class="product-card-info">
            <div class="name">
                <a class="product-trunc" href="{{ route('shop.product.view', $product['slug']) }}"
                   title="{{ $product['name'] }}">{{ $product['name'] }}</a>
            </div>
            <div class="price">
                @if($product['is_sale'])
                    @if($product['price_previous'] > $product['price'])
                        <span class="discount-price">{{ price($product['price']) }}</span><span
                            class="base-price">{{ price($product['price_previous']) }}</span>
                    @else
                        {{ price($product['price']) }}
                    @endif
                @else
                    {{ price($product['price']) }}
                @endif
            </div>
        </div>


    </div>
</div>
