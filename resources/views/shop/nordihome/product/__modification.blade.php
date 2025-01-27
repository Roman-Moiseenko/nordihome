
@if(!is_null($modification))
    <div class="view-modification">
        @foreach($modification->products as $_product)
            <div>
                @if($current_id == $_product->id)
                    <img src="{{ $product->getImage('thumb') }}"
                         alt="{{ $_product->name }}">
                @else
                    <a href="{{ route('shop.product.view', $_product->slug) }}"
                       title="{{ ($_product->isSale() ? '' : 'Снят с продажи! ') . $_product->name }}">
                        <img src="{{ $_product->getImage('thumb') }}"
                             alt="{{ $_product->name }}">
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@endif
