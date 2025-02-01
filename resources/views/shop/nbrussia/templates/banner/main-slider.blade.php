<!--template:Главный слайдер-->
@php
    /**
    * Banner::class - string
    * $banner->caption - string
    * $banner->description - string
    * BannerItem:class
    * $banner->items - Arraible
    * $item->image - Photo::class
    * $item->url - string
    * $item->caption - string
    * $item->description - string
    */
    /** @var \App\Modules\Page\Entity\Banner $banner */
@endphp
<div>
    <div id="main-slider" class="owl-carousel owl-theme">
    @foreach($banner->items as $item)
        <div>
            <a href="{{ $item->url }}">
                <img src="{{ $item->getImage() }}" />
            </a>
        </div>
    @endforeach
    </div>
</div>
