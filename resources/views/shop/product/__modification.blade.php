@php
    /** @var \App\Modules\Shop\Application\DTOs\Entities\ProductModificationData[] $modification */
@endphp
@if(!empty($modification))
    <div class="modification">
        @foreach($modification as $modificationProduct)
            @if($current_id === $modificationProduct->id)
                <span class="size active"
                      data-id="{{ $modificationProduct->id }}"
                      title="{{ $modificationProduct->name }}">
                @if(!empty($modificationProduct->variantImage))
                        <img src="{{ $modificationProduct->variantImage }}"
                             alt="{{ $modificationProduct->variantName }}"
                             title="{{ $modificationProduct->variantName }}">
                    @else
                        {{ $modificationProduct->variantName ?: $modificationProduct->name }}
                    @endif
            </span>
            @else
                <a class="size"
                   href="{{ route('shop.product.view', $modificationProduct->slug) }}"
                   title="{{ $modificationProduct->name }}">
                    @if(!empty($modificationProduct->variantImage))
                        <img src="{{ $modificationProduct->variantImage }}"
                             alt="{{ $modificationProduct->variantName }}"
                             title="{{ $modificationProduct->variantName }}">
                    @else
                        {{ $modificationProduct->variantName ?: $modificationProduct->name }}
                    @endif
                </a>
            @endif
        @endforeach
    </div>
@endif
