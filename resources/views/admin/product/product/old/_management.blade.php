<div class="grid grid-cols-12 gap-x-6 my-5">
    <div class="col-span-12 lg:col-span-4 lg:mr-20">
        <h3 class="font-medium">Статус товар</h3>
    </div>
    <div class="col-span-12 lg:col-span-8">
        {{ \App\Forms\CheckSwitch::create('published', [
         'placeholder' => 'Опубликован',
         'value' => $product->isPublished(),
         ])->show() }}
    </div>
</div>
<div class="grid grid-cols-12 gap-x-6 my-5">
    <div class="col-span-12 lg:col-span-4 lg:mr-20">
        <h3 class="font-medium">Товарный учет</h3>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <div class="flex">
            {{ \App\Forms\Input::create('last-price',
                ['placeholder' => 'Цена', 'class' => 'ml-0 w-full lg:w-40', 'value' => $product->getLastPrice()])
                ->group_text('₽', false)->disabled($options->shop->accounting)->show() }}
            {{ \App\Forms\Input::create('count-for-sell',
                ['placeholder' => 'Кол-во', 'class' => 'ml-0 w-full lg:ml-4 lg:w-40', 'value' => $product->getCountSell()])
                ->group_text('шт', false)->disabled($options->shop->accounting)->show() }}
        </div>
    </div>
</div>
<div class="grid grid-cols-12 gap-x-6 my-5">
    <div class="col-span-12 lg:col-span-4 lg:mr-20">
        <h3 class="font-medium">Продажа</h3>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <div class="flex">
            {{ \App\Forms\CheckSwitch::create('pre_order', [
             'placeholder' => 'Возможен предзаказ',
             'value' => $options->shop->pre_order ? $product->pre_order : false,
             ])->disabled(!$options->shop->pre_order)->show() }}

            {{ \App\Forms\CheckSwitch::create('offline', [
             'placeholder' => 'Продажа только офлайн',
             'value' => ($options->shop->only_offline) ? true : $product->only_offline,
             'class' => 'ml-3',
             ])->disabled($options->shop->only_offline)->show() }}

        </div>
    </div>
</div>
<div class="grid grid-cols-12 gap-x-6 my-5">
    <div class="col-span-12 lg:col-span-4 lg:mr-20">
        <h3 class="font-medium">Периодичность</h3>
        Используется для расчета частоты и периода показа товара и его аналогов, на основе ранее произведенных покупок
    </div>
    <div class="col-span-12 lg:col-span-8">
        <div>
            @foreach(App\Modules\Product\Entity\Product::FREQUENCIES as $value => $caption)
                <div class="form-check mt-2">
                    <input id="frequency-{{ $value }}" class="form-check-input" type="radio" name="frequency"
                           value="{{ $value }}" {{ $product->frequency == $value ? 'checked' : ''}}>
                    <label class="form-check-label" for="frequency-{{ $value }}">{{ $caption }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>
