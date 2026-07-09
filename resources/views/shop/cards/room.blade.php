<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="catalog-card">
        <a href="{{ route('shop.room.view', $room['slug']) }}">
            <div>
                <img
                    src="{{ (is_null($room['image'])) ? '\images\no-image.jpg' : $room['image'] }}">
                <span>{{ $room['name'] }}</span>
            </div>
        </a>
    </div>
</div>
