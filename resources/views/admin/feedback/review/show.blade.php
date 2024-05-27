@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Отзыв на товар {{ $review->product->name }}
        </h2>
    </div>
<div class="box p-3">
    <div class="mt-1 font-medium text-lg">
        <a href="{{ route('admin.product.show', $review->product) }}">{{ $review->product->name }}</a>
    </div>
    <div class="mt-1 font-medium text-lg"><a href="{{ route('admin.users.show', $review->user) }}">{{ $review->user->fullname->getFullName() }}</a></div>
    <hr/>
    <div class="mt-2">Рейтинг: {{ $review->rating }} Дата публикации {{ $review->htmlDate() }} Текущий статус: {{ $review->statusHtml() }}</div>
    <hr/>
    <div class="mt-3 mb-3">
        <div class="mt-1 font-medium text-base">Отзыв: </div>
        <div class="ml-3">{{ $review->text }}</div>
        @if(!is_null($review->photo))
            <img src="{{ $review->photo->getThumbUrl('original') }}" />
        @endif
    </div>
    <hr/>
    <div class="flex">
        @if($review->isModerated())
            <button class="btn btn-success"
               onclick="event.preventDefault();document.getElementById('review-published-{{ $review->id }}').submit();">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Published </button>
            <form id="review-published-{{ $review->id }}" method="post"
                  action="{{ route('admin.feedback.review.published', $review) }}">
                @csrf
            </form>

            <button class="btn btn-danger ml-2"
               onclick="event.preventDefault();document.getElementById('review-blocked-{{ $review->id }}').submit();">
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Blocked </button>
            <form id="review-blocked-{{ $review->id }}" method="post"
                  action="{{ route('admin.feedback.review.blocked', $review) }}">
                @csrf
            </form>
        @endif
    </div>
</div>
@endsection
