@once
    @push('vendors')
        @vite('resources/js/vendor/litepicker/index.js')
    @endpush
@endonce

<script>
    let discountSelect;
    function init() {
        discountSelect = document.getElementById('select-discount');
        discountSelect.addEventListener('change', function (e) {
            updateDiscountSelect();
        });
    }
    function updateDiscountSelect(__from = '', __to = '') {
        let _class = discountSelect.options[discountSelect.selectedIndex].value;
        let _params = '_token=' + '{{ csrf_token() }}' + '&class=' + _class;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/discount/discount/widget');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let _html_widget = JSON.parse(request.responseText);
                let old_discount = document.getElementById('discount-widget');
                if (old_discount !== null) old_discount.remove();
                discountSelect.parentNode.insertAdjacentHTML('beforeend', _html_widget);
                document.getElementById('input-_from').value = __from;
                document.getElementById('input-_to').value = __to;
                _update_datepicker();
            } else {
                //console.log(request.responseText);
            }
        };
    }
    function _update_datepicker() {
        window.$(".datepicker").each(function () {
            let options = {
                autoApply: true,
                singleMode: true,
                numberOfColumns: 1,
                numberOfMonths: 1,
                showWeekNumbers: true,
                format: "DD-MM-YYYY",
                lang: 'ru-RU',
                dropdowns: {
                    minYear: (new Date()).getFullYear(),
                    maxYear: (new Date()).getFullYear() + 10,
                    months: true,
                    years: true,
                },
            };

            if (window.$(this).data("format")) {
                options.format = window.$(this).data("format");
            }
            if (window.$(this).data("not-year")) {
                options.dropdowns.years = false;
                options.dropdowns.maxYear = (new Date()).getFullYear();
            }
            new window.Litepicker({
                element: this,
                ...options,
            });
        });
    }

</script>
<div class="grid grid-cols-12 gap-4 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12">
        <div class="box">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-4">
                        {{ \App\Forms\Input::create('name',
                                ['placeholder' => 'Внутреннее имя', 'value' => !is_null($discount) ? $discount->name : ''])
                                ->label('Имя')->show() }}
                        {{ \App\Forms\Input::create('title',
                                ['placeholder' => 'Заголовок для клиента', 'value' => !is_null($discount) ? $discount->title : '', 'class' => 'mt-3'])
                                ->label('Заголовок')->show() }}
                        {{ \App\Forms\Input::create('discount',
                                ['placeholder' => 'Скидка %%', 'value' => !is_null($discount) ? $discount->discount : '', 'class' => 'mt-3'])
                                ->label('Скидка')->show() }}

                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <x-base.form-label for="select-discount">Тип скидки</x-base.form-label>
                        <x-base.tom-select id="select-discount" name="class" class="w-full"
                                           data-placeholder="Выберите тип скидки">
                            <option value="0"></option>
                            @foreach(\App\Modules\Discount\Helpers\DiscountHelper::discounts() as $class => $name)
                                <option value="{{ $class }}"
                                        data-widget="{{ strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $class)) }}"
                                @if(isset($discount))
                                    {{ $class == $discount->class ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                        <script>init();</script>
                        @if(isset($discount))
                            <script>updateDiscountSelect("{{ $discount->_from }}", "{{ $discount->_to }}");</script>
                        @endif
                    </div>

                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<script>



</script>
