@if(!is_null($modification))
    <div class="view-modification">
        @foreach($modification->products as $_product)
            <div>
                @if($current_id == $_product->id)
                    <img src="{{ $product->photo->getThumbUrl('thumb') }}"
                         alt="{{ $_product->photo->alt }}">
                @else
                    <a href="{{ route('shop.product.view', $_product->slug) }}"
                       title="{{ $_product->name }}">
                        <img src="{{ $_product->photo->getThumbUrl('thumb') }}"
                             alt="{{ $_product->photo->alt }}">
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@endif
