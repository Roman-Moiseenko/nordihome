<!--template:Главный слайдер-->
@php
 /**
 * Banner::class
 *
 * BannerItem:class
 *
 *
 */
@endphp
<div>
    <span>{{ $banner->caption  }}</span>
    @foreach($banner->items as $item)
        {{ $item->caption }}
    @endforeach
</div>
