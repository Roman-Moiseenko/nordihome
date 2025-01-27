@extends('cabinet.cabinet')
@section('body')
    @parent
    review
@endsection

@section('title', 'Мои отзывы')

@section('h1', 'Отзывы')

@section('subcontent')
    @foreach($user->reviews as $review)
        <div class="box-card d-flex review-item">
            <div class="product-img">
                <a href="{{ route('shop.product.view', $review->product->slug) }}" target="_blank"><img
                        src="{{ $review->product->getImage('mini') }}"/></a>
            </div>
            <div class="product-info">
                <a href="{{ route('shop.product.view', $review->product->slug) }}" target="_blank">{{ $review->product->name }}</a>
            </div>

            <div class="review-info">
                <span class="status badge
                    @if($review->isDraft()) text-bg-secondary @endif
                    @if($review->isModerated()) text-bg-warning @endif
                    @if($review->isPublished()) text-bg-success @endif
                    @if($review->isBlocked()) text-bg-danger @endif
                    ">{{ $review->statusHtml() }}</span>
                <span class="rating-stars">{{ $review->rating }} <i class="fa-solid fa-star"></i></span>
                <span class="date">{{ $review->htmlDate() }}</span>
            </div>
            <div class="review-button">
                <a href="{{ route('cabinet.review.show', $review) }}" class="btn btn-primary">
                    <i class="fa-light fa-right-to-bracket"></i>
                </a>
            </div>
        </div>
    @endforeach
    @if($user->reviews()->count() == 0)
        <div class="fs-5 m-3 mb-5">
            У вас еще нет отзывов на товары.
        </div>
    @endif
@endsection
