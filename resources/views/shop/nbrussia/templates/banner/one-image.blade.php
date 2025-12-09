<!--template:Одно изображение-->
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
    /** @var \App\Modules\Page\Entity\Widgets\BannerWidget $banner */
    $item = $banner->items()->first();
@endphp
<div class="banner-one-image">
    <div class="my-4 py-3">
        <a href="{{ $item->url }}">
            <img src="{{ $item->getImage() }}" style="width: 100%;"/>
        </a>
    </div>
    <div class="show-mobile">
        <div class="button-block">
            <div class="caption">{{ $item->caption }}</div>
            <div class="description">{{ $item->description }}</div>
            <div class="link-button">
                <a href="{{ $item->url }}" class="btn-nb">Купить сейчас</a>
            </div>
        </div>
    </div>
</div>
