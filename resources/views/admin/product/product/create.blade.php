@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создать новый товары
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-x-6 pb-20">
        <!-- PRODUCT -->
        <div class="intro-y col-span-11 2xl:col-span-9">
            @foreach($menus as $menu)
                <div id="{{ $menu['anchor'] }}" class="intro-y box p-5 mt-5 block-menus-product">
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

        <div class="intro-y col-span-2 hidden 2xl:block">
            <div class="sticky top-0 pt-10">
                <ul
                    class="relative text-slate-500 before:absolute before:left-0 before:z-[-1] before:h-full before:w-[2px] before:bg-slate-200 before:content-[''] before:dark:bg-darkmode-600">
                    @foreach($menus as $menu)
                    <li id="li-{{ $menu['anchor'] }}" class="mb-4 border-l-2 border-primary pl-5 border-transparent dark:border-transparent">
                        <a href="#{{ $menu['anchor'] }}">{{ $menu['caption'] }}</a>
                    </li>
                    @endforeach
                </ul>
                <div
                    class="relative mt-10 rounded-md border border-warning bg-warning/20 p-5 dark:border-0 dark:bg-darkmode-600">
                    <x-base.lucide
                        class="absolute top-0 right-0 mt-5 mr-3 h-12 w-12 text-warning/80"
                        icon="Lightbulb"
                    />
                    <h2 class="text-lg font-medium">Tips</h2>
                    <div class="mt-5 font-medium">Price</div>
                    <div class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-500">
                        <div>
                            The image format is .jpg .jpeg .png and a minimum size of 300 x
                            300 pixels (For optimal images use a minimum size of 700 x 700
                            pixels).
                        </div>
                        <div class="mt-2">
                            Select product photos or drag and drop up to 5 photos at once
                            here. Include min. 3 attractive photos to make the product more
                            attractive to buyers.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        //Scrolling and ActiveMenu
        window.scrollTo()
        let blocksScroll = document.querySelectorAll('.block-menus-product');
        Array.from(blocksScroll).forEach(function (blockScroll) {

        });
        //Button To Up

        //Sticky fix
    </script>
@endsection
