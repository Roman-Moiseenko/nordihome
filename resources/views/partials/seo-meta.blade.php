@php
    use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
    /** @var SeoData $meta */
    $domain = route('shop.home');
@endphp
@if($meta->title)<title>{{ $meta->title }}</title>@endif{{-- ==================== Базовые ==================== --}}

@if($meta->description)<meta name="description" content="{{ $meta->description }}">@endif

@if($meta->canonical)<link rel="canonical" href="{{ $meta->canonical }}">@endif
{{-- ==================== OpenGraph ==================== --}}
<meta property="og:locale" content="{{ $meta->ogLocale }}">
<meta property="og:type" content="{{ $meta->ogType }}">
@if($meta->ogSiteName)
<meta property="og:site_name" content="{{ $meta->ogSiteName }}">
@endif
@if($meta->ogTitle ?? $meta->title)
<meta property="og:title" content="{{ $meta->ogTitle ?? $meta->title }}">
@endif
@if($meta->ogDescription ?? $meta->description)
<meta property="og:description" content="{{ $meta->ogDescription ?? $meta->description }}">
@endif
@if($meta->ogUrl ?? $meta->canonical)
<meta property="og:url" content="{{ $meta->ogUrl ?? $meta->canonical }}">
@endif
@foreach($meta->ogImages as $image){{-- ==================== OpenGraph Images ==================== --}}
<meta property="og:image" content="{{ $domain . $image->url }}">
@if($image->width)
<meta property="og:image:width" content="{{ $image->width }}">
@endif
@if($image->height)
<meta property="og:image:height" content="{{ $image->height }}">
@endif
@if($image->type)
<meta property="og:image:type" content="{{ $image->type }}">
@endif
@if($image->alt)
<meta property="og:image:alt" content="{{ $image->alt }}">
@endif
@endforeach
@if($meta->ogType === 'product' && $meta->productPrice !== null){{-- ==================== Товар ==================== --}}
<meta property="product:price:amount" content="{{ $meta->productPrice }}">
<meta property="product:price:currency" content="{{ $meta->productCurrency }}">
<meta property="product:condition" content="{{ $meta->productCondition }}">
@if($meta->productAvailability)<meta property="og:availability" content="{{ $meta->productAvailability }}">
<meta property="product:availability" content="{{ $meta->productAvailability }}">@endif
@if($meta->productRetailerId)<meta property="product:retailer_item_id" content="{{ $meta->productRetailerId }}">@endif
@endif
@if($meta->ogType === 'article'){{-- ==================== Статья ==================== --}}
@if($meta->articlePublishedTime)
<meta property="article:published_time" content="{{ $meta->articlePublishedTime }}">
@endif
@if($meta->articleModifiedTime)
<meta property="article:modified_time" content="{{ $meta->articleModifiedTime }}">
@endif
@if($meta->articleAuthor)<meta property="article:author" content="{{ $meta->articleAuthor }}">@endif
@if($meta->articleSection)<meta property="article:section" content="{{ $meta->articleSection }}">@endif
@endif
