<div class="dimensions">
    <div class="sizes">
        <img src="/images/dimensions/sizes.png">
        <div class="height">{{ $dimensions->height }} см</div>
        <div class="width">{{ $dimensions->width }} см</div>
        <div class="depth">{{ $dimensions->depth }} см</div>
    </div>
    <div class="weight">
        <img src="/images/dimensions/weight.png">
        <div class="measure">{{ $dimensions->weight . ' ' . $dimensions->measure }}</div>
    </div>
    <div class="delivery">
        <ul>
            <li><i class="fa-light fa-person-dolly"></i> <a href="{{ route('shop.page.view', 'contact') }}" target="_blank">Самовывоз</a></li>
            @if($local)
            <li><i class="fa-light fa-truck"></i> <a href="{{ route('shop.page.view', 'tariff') }}" target="_blank">Доставка по области</a></li>
            @endif
            @if('region')
                <li><i class="fa-light fa-plane-tail"></i> <a href="{{ route('shop.page.view', 'tariff') }}" target="_blank">Доставка по России</a></li>
            @endif
        </ul>
    </div>
</div>
