<div class="modification">
    @foreach($product['modification'] as $attribute)
        @if(isset($attribute['products']))
            @foreach($attribute['products'] as $value => $_product_mod)
                @if($product['is_sale'])
                    <span class="size {{ $product['id'] === $_product_mod[0]['id'] ? 'active' : '' }}"
                          data-id="{{ $_product_mod[0]['id'] }}"
                          title="{{ $_product_mod[0]['name'] }}"> {{ $value }}</span>
                @else
                    <a class="size"
                       href="{{ route('shop.product.view', $_product_mod[0]['id']) }}"
                       title="{{ $_product_mod[0]['name'] }}"> {{ $value }}</a>
                @endif
            @endforeach
        @endif
    @endforeach
</div>
