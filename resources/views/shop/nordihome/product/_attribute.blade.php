@if(!empty($productAttributes))
    <div class="box-card view-attributes">
        <h3 id="specifications">Характеристики</h3>
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
    </div>
@endif
