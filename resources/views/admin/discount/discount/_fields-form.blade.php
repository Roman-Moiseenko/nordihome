<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Основные данные -->
    <div class="intro-y col-span-12">
        <div class="intro-y box">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-4">
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Внутреннее имя', 'value' => !is_null($discount) ? $discount->name : ''])->show() }}
                        {{ \App\Forms\Input::create('title', ['placeholder' => 'Заголовок', 'value' => !is_null($discount) ? $discount->title : '', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\Input::create('discount', ['placeholder' => 'Скидка %%', 'value' => !is_null($discount) ? $discount->discount : '', 'class' => 'mt-3'])->show() }}

                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <x-base.tom-select id="select-discount" name="discount_class" class="w-full"
                                           data-placeholder="Выберите тип скидки">
                            <option value="0"></option>
                            @foreach(\App\Modules\Discount\Helpers\DiscountHelper::discounts() as $class => $name)
                                <option value="{{ $class }}" data-widget="{{ strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $class)) }}"
                                @if(isset($discount))
                                    {{ $class == $discount->type ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                        <div class="flex">
                            {{ \App\Forms\Input::create('_from', ['placeholder' => 'от', 'value' => !is_null($discount) ? $discount->_from : '', 'class' => 'mt-3 w-full'])->show() }}
                            {{ \App\Forms\Input::create('_to', ['placeholder' => 'до', 'value' => !is_null($discount) ? $discount->_to : '', 'class' => 'mt-3 ml-3 w-full'])->show() }}
                        </div>
                        <div class="mt-3">
                            Формат полей "от" и "до": <br>
                            Для точного периода - ДД ММ ГГГГ (02 11 2023) <br>
                            Для ежегодного периода ДД ММ (01 05) <br>
                            Для ежемесячного - день месяца <br>
                            Для еженедельного - порядковый день недели (0 - Вс, 1 - Пн, и т.д.)
                        </div>

                    </div>

                </div>
            </div>
            <div class="intro-y col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<script>
    let discountSelect = document.getElementById('select-discount');
    discountSelect.addEventListener('change', function (e) {
        let _class = discountSelect.options[discountSelect.selectedIndex].value;
        //TODO Отправляем post запрос для получения рендера виджета
        let _params = '_token=' + '{{ csrf_token() }}' + '&class=' + _class;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/discount/discount/widget');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let _html_widget = JSON.parse(request.responseText);
                discountSelect.parentNode.insertAdjacentHTML('beforeend', _html_widget);
                //Вставить код HTML
            } else {
                //console.log(request.responseText);
            }
        };

        //let widget = discountSelect.options[discountSelect.selectedIndex].getAttribute('data-widget');
        console.log('*');
    });
</script>
