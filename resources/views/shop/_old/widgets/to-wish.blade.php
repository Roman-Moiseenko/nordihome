@if(!is_null($user))
    <a class="product-wish-toggle
                {{ $product->isWish($user->id) ? 'is-wish' : 'to-wish' }}" data-product="{{ $product->id }}"
       type="button" title="В Избранное"><i
            class="{{ $product->isWish($user->id) ? 'fa-solid' : 'fa-light' }} fa-heart"></i></a>
@else
    <a class="to-wish" data-bs-toggle="modal" data-bs-target="#login-popup"
       onclick="event.preventDefault();"><i
            class="fa-light fa-heart" type="button" title="В Избранное"></i></a>
@endif
