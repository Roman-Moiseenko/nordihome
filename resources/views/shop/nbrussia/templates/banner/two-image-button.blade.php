<!--template:Два изображения с кнопкой-->
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
    /** @var \App\Modules\Page\Entity\BannerWidget $banner */
@endphp
<div class="banner-2-image-button my-5 py-3">
    @foreach($banner->items as $item)
        <div>
            <a href="{{ $item->url }}">
                <img src="{{ $item->getImage() }}" style="width: 100%;"/>
            </a>
            <div class="button-block">
                <div class="caption">{{ $item->caption }}</div>
                <div class="description">{{ $item->description }}</div>
                <div class="link-button">
                    <a href="{{ $item->url }}" class="btn-nb">Подробнее</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
