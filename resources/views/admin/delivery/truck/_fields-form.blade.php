<div class="grid grid-cols-12 gap-4 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12 lg:col-span-8">
        <div class="box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-6">
                        <!-- Выбрать категорию -->
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Название/Гос.номер', 'value' => $truck->name ?? '', 'class' => 'mt-3'])
                        ->help('Марка автомобиля, гос.номер')->show() }}
                        {{ \App\Forms\Input::create('weight', ['placeholder' => 'Грузоподъемность', 'value' =>$truck->weight ?? '', 'class' => 'mt-3'])
                        ->help('Грузоподъемность в кг')->show() }}
                        {{ \App\Forms\Input::create('volume', ['placeholder' => 'Объем', 'value' => $truck->volume ?? '', 'class' => 'mt-3'])
                        ->help('Объем кузова в м3')->show() }}

                        <x-base.form-label for="select-worker" class="mt-3">Водитель</x-base.form-label>
                        <x-base.tom-select id="select-worker" name="worker_id" class="w-full" data-placeholder="Выберите водителя">
                            <option value="0"></option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    @if(!is_null($truck)){{ $truck->worker_id == $driver->id ? 'selected' : ''}}@endif>
                                    {{ $driver->fullname->getShortName() }}
                                </option>
                            @endforeach
                        </x-base.tom-select>


                        {{ \App\Forms\CheckSwitch::create('cargo', [
                             'placeholder' => 'Перевозка груза',
                             'value' => (!is_null($truck) ? $truck->cargo : true),
                             'class' => 'mt-3'
                             ])->show() }}
                        {{ \App\Forms\CheckSwitch::create('service', [
                             'placeholder' => 'Услуги',
                             'value' => (!is_null($truck) ? $truck->service : true),
                             'class' => 'mt-3'
                             ])->show() }}
                    </div>

                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
    <div class="col-span-4 hidden 2xl:block">
        <div class="mt-10 bg-warning/20 dark:bg-darkmode-600 border border-warning dark:border-0 rounded-md relative p-5">
            <x-base.lucide icon="lightbulb" class="w-12 h-12 mr-2 text-warning/80 absolute top-0 right-0 mt-5 mr-3"/>
            <h2 class="text-lg font-medium">
                Информация
            </h2>
            <div class="mt-5 font-medium"></div>
            <div class="leading-relaxed mt-2 text-slate-600 dark:text-slate-500">
                <div>*</div>

            </div>
        </div>
    </div>
</div>


