@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $attribute->name }}
            </h1>
        </div>
    </div>

    <div class="intro-y box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                    <img class="rounded-full" src="{{ $attribute->getImage() }}">
                    <div class="absolute mb-1 mr-1 flex items-center justify-center bottom-0 right-0 bg-primary rounded-full p-2">
                        <x-base.lucide class="w-4 h-4 text-white" icon="camera"/>
                    </div>
                </div>
                <div class="ml-5">
                    <div class="truncate sm:whitespace-normal font-medium text-lg">{{ $attribute->name }}</div>
                    <div class="text-slate-500 text-primary">{{ $attribute->group->name }}</div>
                </div>
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Основные параметры</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="file-box" class="w-4 h-4 mr-2"/>
                        @foreach($attribute->categories as $category)
                            <span class="ml-1 rounded bg-secondary px-2 ">{{ $category->name }}</span>
                        @endforeach
                        </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="file-digit" class="w-4 h-4 mr-2"/>
                        <span class="ml-1 rounded bg-success/20 text-success px-2 ">{{ \App\Modules\Product\Entity\Attribute::ATTRIBUTES[$attribute->type] }}</span>
                        </div>
                    @if($attribute->isVariant())
                    <x-yesno title="Множественный выбор" status="{{$attribute->multiple}}" class="mt-3"/>
                    @endif
                    <x-yesno title="Фильтр" status="{{$attribute->filter}}" class="mt-3"/>
                    <x-yesno title="Показывать в поиске" status="{{$attribute->show_in}}" class="mt-3"/>
                </div>
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-5">Информация</div>
                <div class="flex items-center justify-center lg:justify-start mt-2">

                        <div class="h-[55px]">
                            Статистика
                            скольким товарам назначен<br>
                            Используется ли в модификации
                        </div>

                </div>
            </div>
        </div>
        <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center py-5">
            <li class="nav-item">
                <a class="btn btn-primary py-1 px-2 mr-2"
                   href="{{ route('admin.product.attribute.edit', $attribute) }}">Редактировать
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="btn btn-outline-secondary py-1 px-2"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.attribute.destroy', $attribute) }}>
                    <x-base.lucide icon="trash-2" class="w-4 h-4 mr-2"/> Удалить </a>
            </li>
        </ul>
    </div>

    @if($attribute->isVariant())
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y lg:col-span-4 md:col-span-6 col-span-12">
        <h2 class="font-medium mt-3">Варианты</h2>

        @foreach($attribute->variants as $variant)
            <div class="box w-full  p-2 mt-2 flex flex-column">
                <div class="w-40 my-auto">
                    <div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $variant->getImage() }}" alt="{{ $variant->name }}"></div>
                </div>
                <div class="w-full items-center my-auto">{{ $variant->name }}</div>
                <x-base.button
                    data-tw-toggle="modal"
                    data-tw-target="#update-image-variant"
                    href="#" as="a" variant="primary"
                    :data-route="route('admin.product.attribute.variant-image', $variant)"
                    class="w-56 variant-modal-upload">
                    Изображение
                </x-base.button>
            </div>
        @endforeach
            </div>
            <div class="intro-y col-span-4 hidden 2xl:block">
                <div class="mt-10 bg-warning/20 dark:bg-darkmode-600 border border-warning dark:border-0 rounded-md relative p-5">
                    <x-base.lucide icon="lightbulb" class="w-12 h-12 mr-2 text-warning/80 absolute top-0 right-0 mt-5 mr-3"/>
                    <h2 class="text-lg font-medium">
                        Информация
                    </h2>
                    <div class="mt-5 font-medium"></div>
                    <div class="leading-relaxed mt-2 text-slate-600 dark:text-slate-500">
                        <div>Разрешение <b>изображения</b> не должно быть более 200x200 пикселей.</div>
                        <div class="mt-2">Для <b>картинок</b> рекомендуем форматы с прозрачным фоном.
                            Рекомендуем использовать SVG-файлы</div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!--Модальное окно для загрузки фото (сделать компонентом, передача класса прослушивания) -->
    <x-base.dialog id="update-image-variant" staticBackdrop>
        <form id="form-image-variant" method="POST" action="/" enctype="multipart/form-data">
            @csrf
        <x-base.dialog.panel>
            <x-base.dialog.title>
                <h2 class="mx-auto text-base font-medium">
                    Загрузить новое изображение
                </h2>
            </x-base.dialog.title>
            <x-base.dialog.description class="p-2">
                {{ \App\Forms\Upload::create('file')->show() }}
            </x-base.dialog.description>
            <x-base.dialog.footer>
                <x-base.button class="mr-1 w-20" data-tw-dismiss="modal" type="button" variant="outline-secondary">Отмена</x-base.button>
                <x-base.button class="w-20" type="submit" variant="primary">Сохранить</x-base.button>
            </x-base.dialog.footer>
        </x-base.dialog.panel>
        </form>
    </x-base.dialog>
    <script>
        let elements = document.querySelectorAll(".variant-modal-upload");
        Array.from(elements).forEach(function (element) {
            element.addEventListener('click', function (e) {
                let route = e.target.getAttribute('data-route');
                let form = document.getElementById('form-image-variant');
                form.setAttribute('action', route);
            });
        });
    </script>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Атрибут?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
