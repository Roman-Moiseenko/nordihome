@if(!is_null($reviews))
    <div class="box-card">
        <h3 id="reviews">Отзывы</h3>
        @foreach($reviews as $review)
            <div class="review-product-item">
                <div class="rating-data">
                    {{ $review->user->fullname->firstname }} {{ $review->rating }} {{ $review->htmlDate() }}
                </div>
                <div class="info">
                    {{ $review->text }}
                </div>
                <div class="photo">

                </div>
            </div>
        @endforeach
    </div>
@endif
