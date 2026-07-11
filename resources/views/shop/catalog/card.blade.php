@php
    /** @var \App\Modules\Shop\Application\DTOs\Parts\CategoryRoomData $item */
@endphp
<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="catalog-card">
        <a href="{{ route('shop.category.view', $item->slug) }}">
            <div>
                <img
                    src="{{ (empty($item->image)) ? '\images\no-image.jpg' : $item->image }}">
                <span>{{ $item->name }}</span>
            </div>
        </a>
    </div>
</div>
