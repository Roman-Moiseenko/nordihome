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
</div>
