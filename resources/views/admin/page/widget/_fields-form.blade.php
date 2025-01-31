<script>
    let selectObject, selectId;

    function init(data_id = 0) {
        selectObject = document.getElementById('select-object');
        selectId = document.getElementById('select-id');
        selectObject.addEventListener('change', function (e) {
            updateSelectId();
        });
        if (selectObject.selectedIndex !== 0) updateSelectId(data_id);
    }
    function updateSelectId(data_id = 0) {
        let _class = selectObject.options[selectObject.selectedIndex].value;
        let _params = '_token=' + '{{ csrf_token() }}' + '&class=' + _class;
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/page/widget/ids');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let ids = JSON.parse(request.responseText);

                //Очистка selectId
                selectId.options.length = 0; //Очищаем и заполняем SELECT атрибутами
                selectId.appendChild(new Option('', '0'));
                selectId.tomselect.clear();
                selectId.tomselect.clearOptions();
                //Заполнение selectId
                for (let id in ids) {
                    if(Number(id) === Number(data_id)) {
                        selectId.appendChild(new Option(ids[id], id, true, true));
                    } else {
                        selectId.appendChild(new Option(ids[id], id));
                    }
                }
                selectId.tomselect.sync();
            } else {
                //console.log(request.responseText);
            }
        };
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
                        ['placeholder' => 'Имя виджета', 'value' => !is_null($widget) ? $widget->name : ''])
                        ->label('Имя')->show() }}
                        <x-base.form-label for="select-object" class="mt-3">Данные для виджета</x-base.form-label>


                        <x-base.form-label for="select-id" class="mt-3">Объект</x-base.form-label>
                        <select id="select-id" name="data_id" class="w-full new-tom-select bg-white tom-select"
                                data-placeholder="Выберите объект" >
                            <option value="0"></option>
                        </select>
                        <x-base.form-label for="select-template" class="mt-3">Шаблон</x-base.form-label>
                        <select id="select-template" name="template" class="w-full new-tom-select bg-white tom-select"
                                data-placeholder="Выберите шаблон" >
                            <option value="0"></option>
                            @foreach($templates as $template => $name)
                                <option value="{{ $template }}"
                                @if(isset($widget))
                                    {{ $template == $widget->template ? 'selected' : ''}}
                                    @endif
                                >{{ $name }}</option>
                            @endforeach
                        </select>
                        @if(isset($widget))
                            <script>init({{ $widget->data_id }});</script>
                        @else
                            <script>init();</script>
                        @endif

                    </div>
                    <div class="col-span-12 lg:col-span-4">

                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>

