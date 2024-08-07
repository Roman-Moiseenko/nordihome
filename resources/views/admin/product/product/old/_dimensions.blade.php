<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4 lg:mr-20">Габариты и вес товара в собранном виде, без упаковочного материала
    </div>
    <div class="col-span-12 lg:col-span-8">
        <div class="flex">
            <div>
                <select id="input-dimensions-measure" name="dimensions-measure" class="form-select w-full lg:w-40">
                    <option value="{{ App\Modules\Base\Entity\Dimensions::MEASURE_G }}" {{ ($product->dimensions->measure == App\Modules\Base\Entity\Dimensions::MEASURE_G ? 'selected' : '')  }}>{{ App\Modules\Base\Entity\Dimensions::MEASURE_G }}</option>
                    <option value="{{ App\Modules\Base\Entity\Dimensions::MEASURE_KG }}" {{ ($product->dimensions->measure == App\Modules\Base\Entity\Dimensions::MEASURE_KG ? 'selected' : '')  }}>{{ App\Modules\Base\Entity\Dimensions::MEASURE_KG }}</option>
                </select>
            </div>
            {{ \App\Forms\Input::create('dimensions-weight',
                ['placeholder' => 'Вес', 'class' => 'ml-0 w-full lg:ml-4 lg:w-40', 'value' => $product->dimensions->weight])
                ->help('Вес')->show() }}

        </div>
        <div class="flex mt-3">
            <div>
            <select id="input-dimensions-type" name="dimensions-type" class="form-select w-full lg:w-40">
                @foreach(App\Modules\Base\Entity\Dimensions::TYPES as $type => $name)
                <option value="{{ $type }}" {{ ($product->dimensions->type == $type ? 'selected' : '')  }}>{{ $name }}</option>
                @endforeach
            </select>
            </div>
            {{ \App\Forms\Input::create('dimensions-height',
                ['placeholder' => 'Высота', 'class' => 'ml-0 w-full lg:ml-4 lg:w-40', 'value' => $product->dimensions->height])
                ->help($product->dimensions->nameZ() . ' (см)')->show() }}

            {{ \App\Forms\Input::create('dimensions-width',
                ['placeholder' => 'Ширина', 'class' => 'ml-0 w-full lg:ml-4 lg:w-40', 'value' => $product->dimensions->width])
                ->help($product->dimensions->nameX() . ' (см)')->show() }}
            @if($product->dimensions->notDiameter())
            {{ \App\Forms\Input::create('dimensions-depth',
                ['placeholder' => 'Глубина', 'class' => 'ml-0 w-full lg:ml-4 lg:w-40', 'value' => $product->dimensions->depth])
                ->help($product->dimensions->nameY() . ' (см)')->show() }}
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-12 gap-x-6 mt-5">
    <div class="col-span-12 lg:col-span-4 lg:mr-20">Возможность доставки настраивается в модуле Доставка. <br>
        Для текущего товара можно только ограничить доставку, если габариты не позволяют это сделать.
    </div>
    <div class="col-span-12 lg:col-span-8">

        {{ \App\Forms\CheckSwitch::create('local', [
         'placeholder' => 'В пределах региона',
         'value' => $options->shop->delivery_local ? $product->isLocal() : false,
         ])->disabled(!$options->shop->delivery_local)->show() }}

        {{ \App\Forms\CheckSwitch::create('delivery', [
         'placeholder' => 'Транспортной компанией',
         'value' => $product->isDelivery(),
         'class' => 'mt-3',
         ])->disabled(!$options->shop->delivery_all)->show() }}


    </div>
</div>
