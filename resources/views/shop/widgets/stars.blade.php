
<span class="rating-info">{{ $rating }}</span>
<span class="rating-stars">
@for($i = 1; $i <= 5; $i++)
    @if($i <= $rating)
        <i class="fa-solid fa-star"></i>
    @elseif((0.1 < $i - $rating) && ($i - $rating < 0.9))
        <i class="fa-solid fa-star-half-stroke"></i>
    @else
        <i class="fa-regular fa-star"></i>
    @endif
@endfor
</span>
