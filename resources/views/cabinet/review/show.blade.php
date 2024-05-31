@extends('cabinet.cabinet')
@section('body')
    @parent
    review
@endsection

@section('title', 'Отзыв на ' . $review->product->name)

@section('h1', 'Отзыв на ' . $review->product->name)

@section('subcontent')
    <div class="box-card">
        <livewire:cabinet.review :review="$review" />
    </div>
@endsection
