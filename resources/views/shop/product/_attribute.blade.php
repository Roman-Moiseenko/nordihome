@php
    use App\Modules\Shop\Application\DTOs\Elements\DimensionsData;
    /** @var DimensionsData $dimensions */
    /** @var array $productAttributes */
@endphp

@if(!empty($productAttributes))
    <div class="box-card view-attributes">
        <h2 id="specifications">Характеристики</h2>
        @foreach($productAttributes as $group => $groupAttributes)
            <div class="group">{{ $group }}</div>
            @foreach($groupAttributes as $prod_attribute)
                <div class="attribute">
                    <div class="row">
                        <div class="col-6 col-lg-4">
                            <div class="name">{{ $prod_attribute['name'] }}</div>
                        </div>
                        <div class="col-6 col-lg-4">
                            <div class="values">{{ $prod_attribute['value'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach

        <div class="group">Габариты</div>
        <div class="attribute">
            <div class="row">
                <div class="col-6 col-lg-4">
                    <div class="name">{{ $dimensions->captions[0] }}</div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="values">{{ $dimensions->height }} см</div>
                </div>
            </div>
        </div>

        <div class="attribute">
            <div class="row">
                <div class="col-6 col-lg-4">
                    <div class="name">{{ $dimensions->captions[1] }}</div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="values">{{ $dimensions->width }} см</div>
                </div>
            </div>
        </div>
        <!-- -->
        @if(!empty($dimensions->captions[2]))
            <div class="attribute">
                <div class="row">
                    <div class="col-6 col-lg-4">
                        <div class="name">{{ $dimensions->captions[2] }}</div>
                    </div>
                    <div class="col-6 col-lg-4">
                        <div class="values">{{ $dimensions->depth }} см</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
