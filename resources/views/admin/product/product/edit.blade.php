@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $product->name }}
        </h2>
    </div>

    <form action="{{ route('admin.product.update', $product) }}" METHOD="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-11 gap-x-6 pb-20">
            <!-- PRODUCT -->
            <div class="col-span-11 lg:col-span-9">

                @foreach($menus as $n => $menu)
                    <div id="{{ $menu['anchor'] }}" data-is-top="{{ $n == 'common' ? 1 : 0 }}" class="intro-y box p-5 mt-5 block-menus-product">
                        <div class="rounded-md border border-slate-200/60 p-5 dark:border-darkmode-400">
                            <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium dark:border-darkmode-400">
                                <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/> {{ $menu['caption'] }}
                            </div>
                            <div class="mt-5">
                                @include('admin.product.product.' . $menu['include'])
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>

            <div class="col-span-2 hidden lg:block">
                <div class="fixed fixed-top pt-5">
                    <ul
                        class="relative text-slate-500 before:absolute before:left-0 before:z-[-1] before:h-full before:w-[2px] before:bg-slate-200 before:content-[''] before:dark:bg-darkmode-600">
                        @foreach($menus as $n => $menu)
                            <li id="li-{{ $menu['anchor'] }}" class="li-menus-product mb-4 border-l-2 border-primary pl-5
        {{ ($n == 'common') ? 'border-primary dark:border-primary text-primary font-medium' : 'border-transparent dark:border-transparent' }}">
                                <a href="#{{ $menu['anchor'] }}">{{ $menu['caption'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-5 flex flex-col justify-end gap-2 md:flex-row">
                        <x-base.button class="w-full py-3" type="submit" variant="primary">Обновить</x-base.button>
                    </div>
                    <div class="box relative mt-10 rounded-md border p-5 dark:border-0 dark:bg-darkmode-600">
                        Блок Изображения <br>

                        <x-base.lucide class="absolute top-0 right-0 mt-5 mr-3 h-12 w-12 text-warning/80" icon="Lightbulb"/>
                        <h2 class="text-lg font-medium">Блок Изображения </h2>

                        <div class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-500">
                            <div>
                                Краткая инструкция по заполнению карточки товара
                            </div>
                            <div class="mt-2">
                                Дополнительный текст
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>




    <script>
        //Scrolling and ActiveMenu
        let blocksScroll = document.querySelectorAll('.block-menus-product');
        let menusScroll = document.querySelectorAll('.li-menus-product');
        const classesSelect = ['border-primary', 'dark:border-primary', 'text-primary', 'font-medium'];
        const classesUnSelect = ['border-transparent', 'dark:border-transparent'];

        window.addEventListener('scroll', function () {
            Array.from(blocksScroll).forEach(function (blockScroll) {
                const _t = blockScroll.getBoundingClientRect().top;
                if(_t > 20 && _t < 120 && blockScroll.getAttribute('data-is-top') === '0') {
                    updateMenus(blockScroll.getAttribute('id'))
                }
            });
        });

        function updateMenus(idBlockScroll) {
            Array.from(menusScroll).forEach(function (menuScroll) {
                if (menuScroll.getAttribute('id') === 'li-'+idBlockScroll) {
                    menuScroll.classList.remove(...classesUnSelect);
                    menuScroll.classList.add(...classesSelect);
                } else {
                    if(menuScroll.classList.contains('border-primary')) {
                        menuScroll.classList.remove(...classesSelect);
                        menuScroll.classList.add(...classesUnSelect);
                    }
                }
            });
            Array.from(blocksScroll).forEach(function (blockScroll) {
                if (blockScroll.getAttribute('id') === idBlockScroll) {
                    blockScroll.setAttribute('data-is-top', '1');
                } else {
                    blockScroll.setAttribute('data-is-top', '0');
                }
            });
        }


    </script>
    <!--Модальное окно для загрузки изображений в товар-->
    <x-base.dialog id="static-backdrop-modal-preview" staticBackdrop>
        <x-base.dialog.panel class="px-5 pt-10 pb-5" size="xl">
            <div class="text-center">
                <x-base.preview>
                    <x-base.dropzone
                        id="myDropzoneElementID"
                        class="dropzone"
                        action="{{ route('admin.product.file-upload', $product) }}"
                        multiple
                    >
                        @csrf
                        <div class="text-lg font-medium">
                            Кликните или перетащите файлы для загрузки
                        </div>
                        <div class="text-gray-600">
                            Изображения автоматически загрузятся в товар.
                            <div>
                                Размер изображения 700х700 и не более 500кБ
                            </div>
                            <div class="mt-2">
                                После загрузки изображений, добавьте каждому ALT
                            </div>
                        </div>
                    </x-base.dropzone>
                </x-base.preview>
                <x-base.button id="close-modal-upload" class="w-24 mt-3" data-tw-dismiss="modal" type="button" variant="primary">
                    Закрыть
                </x-base.button>
            </div>
        </x-base.dialog.panel>
    </x-base.dialog>
    <script>
        let closeUpload = document.getElementById('close-modal-upload');
        closeUpload.addEventListener('click', function () {
            LoadImages();
            window.Dropzone.forElement('#myDropzoneElementID').removeAllFiles(true);
        });
    </script>
@endsection
