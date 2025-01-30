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
    $item = $banner->items()->first();
@endphp
<div>
    <a href="{{ $item->url }}">
        <img src="{{ $item->getImage('banner') }}" style="width: 100%;"/>
    </a>
</div>
