@if(!is_null($reviews))
    <div class="box-card">
        <h3 id="reviews">Отзывы</h3>
        @foreach($reviews as $review)
            <div class="review-product-item">
                <div class="rating-data">
                    <div class="user">
                        <i class="fa-light fa-user-vneck fs-4"></i> <span class="fs-5 ms-2">{{ $review['user_name'] }}</span>
                    </div>
                    <div class="rating-date">
                        <div class="rating">
                            @include('shop.widgets.stars', ['rating' => $review['rating']])
                        </div>
                        <div class="date">
                            {{ $review['date'] }}
                        </div>
                    </div>
                </div>
                <div class="info">
                    {!! nl2br(e($review['text'])) !!}
                </div>
                @if(!is_null($review['src']))
                <div class="photo mt-2">
                    <img src="{{ $review['src'] }}">
                </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
