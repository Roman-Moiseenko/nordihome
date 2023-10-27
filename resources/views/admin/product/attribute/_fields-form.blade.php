<script>
    let _counter = 0;
    function changeSelect() {
        let valueType = selectType.options[selectType.selectedIndex].value;
        let placeVariants = document.getElementById('place-variants');
        if (Number(valueType) === {{\App\Modules\Product\Entity\Attribute::TYPE_VARIANT}}) {
            placeVariants.innerHTML = '<p class="mt-3">Варианты</p>' + addVariantButton();
            let addVariant = createButton(); //document.getElementById('add-variant');
            addVariantBlock(addVariant, ++_counter);
        } else {
            placeVariants.innerHTML = '';
        }
    }

    function addVariantButton() {
        return '<button id="add-variant" class="btn px-2 shadow-sm btn-light w-full border-dashed mt-2" type="button">Добавить вариант</button>';
    }

    function createButton(){
        let _addVariant = document.getElementById('add-variant');
        _addVariant.addEventListener('click', function () {
            addVariantBlock(_addVariant, ++_counter);
        });
        return _addVariant;
    }

    //Добавляем INPUT перед elButton и навешиваем событие по удалению
    function addVariantBlock(elButton, number, value = '', _id = '') {
        //Код INPUT
        //TODO Вынести в компоненты или шаблоны
        let _el = '<div id="div-' + number +'" class="input-form mt-3 flex">' +
            '  <input id="input-id-' + number +'" type="hidden" name="variants.id[]" class="form-control " placeholder="Вариант" value="' + _id + '">'+
            '  <input id="input-val-' + number +'" type="text" name="variants.value[]" class="form-control " placeholder="Вариант" value="' + value + '">'+
            '  <button id="button-' + number +'" class="clear-variant btn px-2 box w-10 border-slate-200 ml-2" type="button" for="div-' + number +'">-</button>' +
            '</div>';

        elButton.insertAdjacentHTML('beforebegin', _el);
        let element = document.getElementById('button-' + number);
        element.addEventListener('click', function () {
            document.getElementById(element.getAttribute('for')).remove();
        });
    }
</script>
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Основные данные -->
    <div class="intro-y col-span-12 lg:col-span-8">
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-6">
                        <!-- Выбрать категорию -->
                        <x-base.form-label for="select-category">Категория</x-base.form-label>
                        <x-base.tom-select id="select-category" name="categories[]" class="w-full" data-placeholder="Выберите категории" multiple>
                            <option value="0"></option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    @if(!is_null($attribute)){{ $attribute->isCategory($category) ? 'selected' : ''}}@endif>
                                    @for($i = 0; $i<$category->depth; $i++) - @endfor
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>

                    <!-- Выбрать группу -->
                        <x-base.form-label for="select-group" class="mt-3">Группа</x-base.form-label>
                        <x-base.tom-select id="select-group" name="group_id" class="w-full" data-placeholder="Выберите группу">
                            <option value="0"></option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}"
                                @if(!empty(old('group_id')))
                                    {{ old('group_id')  == $group->id ? 'selected' : '' }}
                                    @else
                                        @if(!is_null($attribute)){{ $attribute->isGroup($group) ? 'selected' : ''}}@endif
                                    @endif
                                >
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Атрибут', 'value' => $attribute->name ?? '', 'class' => 'mt-3'])->show() }}
                        <!-- Флажки -->
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-multiple" type="checkbox" name="multiple"
                                    checked="{{ !is_null($attribute) ? ($attribute->multiple ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-multiple">Множественный выбор</x-base.form-switch.label>
                        </x-base.form-switch>
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-filter" type="checkbox" name="filter"
                                     checked="{{ !is_null($attribute) ? ($attribute->filter ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-filter">Используется для фильтрации</x-base.form-switch.label>
                        </x-base.form-switch>
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-show-in" type="checkbox" name="show-in"
                                     checked="{{ !is_null($attribute) ? ($attribute->show_in ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-show-in">Показывать в кратком описании и поиске</x-base.form-switch.label>
                        </x-base.form-switch>
                        {{ \App\Forms\Input::create('sameAs', ['placeholder' => 'Ссылка википедию', 'class' => 'mt-3'])->show() }}

                        <!-- ТИП АТРИБУТА -->
                        <x-base.form-label for="select-type" class="mt-3">Тип значения атрибута</x-base.form-label>
                        <x-base.form-select id="select-type" class="sm:mr-2" aria-label="Тип значения атрибута" name="type">
                            @foreach(\App\Modules\Product\Entity\Attribute::ATTRIBUTES as $_type => $_name)
                            <option value="{{ $_type }}"
                            @if(!empty(old('type')))
                                {{ old('type')  == $group->type ? 'selected' : '' }}
                                @else
                                @if(!is_null($attribute)){{ $attribute->type == $_type ? 'selected' : ''}}@endif
                                @endif
                            >{{ $_name }}</option>
                            @endforeach
                        </x-base.form-select>
                        <div id="place-variants">
                            @if(!is_null($attribute) && $attribute->isVariant())
                                <script>
                                    document.getElementById('place-variants').innerHTML = addVariantButton();
                                    let _post = createButton();
                                </script>
                                @foreach($attribute->variants as $counter => $variant)
                                    <script>addVariantBlock(_post, _counter++, "{{$variant->name}}", "{{$variant->id}}")</script>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        {{ \App\Forms\Upload::create('image', isset($attribute) ? $attribute->getUploadUrl() : '')->show() }}
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
    <div class="intro-y col-span-4 hidden 2xl:block">
        <div class="mt-10 bg-warning/20 dark:bg-darkmode-600 border border-warning dark:border-0 rounded-md relative p-5">
            <x-base.lucide icon="lightbulb" class="w-12 h-12 mr-2 text-warning/80 absolute top-0 right-0 mt-5 mr-3"/>
            <h2 class="text-lg font-medium">
                Информация
            </h2>
            <div class="mt-5 font-medium"></div>
            <div class="leading-relaxed mt-2 text-slate-600 dark:text-slate-500">
                <div>Поле <b>категория</b> привязывает атрибут к категории и его дочерним категориям.</div>
                <div class="mt-2">Поле <b>группа</b> позволяет сгруппировать характеристики на странице товара.</div>
                <div class="mt-2">Для <b>картинок</b> используйте форматы с прозрачным фоном и размером не более 200х200.
                    Рекомендуем использовать SVG-файлы</div>

                <div>Для типа <b>варианты</b> к каждому значению варианта атрибута предусмотрена возможность установления
                    изображения, например для цвета. Привязать изображение к варианту можно после сохранения атрибута в режиме просмотра.</div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectType = document.getElementById('select-type');
    selectType.addEventListener('change', function (){
        changeSelect();
    });
</script>


