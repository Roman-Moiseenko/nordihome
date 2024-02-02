@extends('cabinet.cabinet')
@section('body')
    @parent
    wish
@endsection
@section('h1')
Избранное
@endsection

@section('subcontent')
    @foreach($products as $product)
        <div class="box-card d-flex wish-item">
            <div class="wish-item-img">
                <a href="{{ route('shop.product.view', $product->slug) }}" target="_blank"><img
                        src="{{ $product->photo->getThumbUrl('thumb') }}"/></a>
            </div>
            <div class="wish-item-info">
                <a href="{{ route('shop.product.view', $product->slug) }}" target="_blank">{{ $product->name }}</a>
            </div>
            <div class="wish-item-control">
                <button class="btn product-wish-toggle
                                {{ (!is_null($user) && $product->isWish($user->id)) ? 'btn-warning' : 'btn-light'  }}"
                        data-product="{{ $product->id }}"><i
                        class="fa-light fa-heart"></i></button>
            </div>
        </div>
    @endforeach
    @if(count($products) == 0 )
        <div class="fs-5 m-3 mb-5">
            У вас нет товаров в избранном.
        </div>
    @endif
@endsection
