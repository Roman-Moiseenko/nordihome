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
    /** @var \App\Modules\Page\Entity\Banner $banner */
    $item = $banner->items()->first();
@endphp
<div class="my-4 py-3">
    <a href="{{ $item->url }}">
        <img src="{{ $item->getImage() }}" style="width: 100%;"/>
    </a>
</div>
