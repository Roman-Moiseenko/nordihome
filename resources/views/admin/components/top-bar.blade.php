@props(['layout' => 'side-menu'])

<!-- BEGIN: Top Bar -->
<div
    @class([
    'h-[70px] md:h-[65px] z-[51] border-b border-white/[0.08] mt-12 md:mt-0 -mx-3 sm:-mx-8 md:-mx-0 px-3 md:border-b-0 relative md:fixed md:inset-x-0 md:top-0 sm:px-8 md:px-10 md:pt-10 md:bg-gradient-to-b md:from-slate-100 md:to-transparent dark:md:from-darkmode-700',
    'dark:md:from-darkmode-800' => $layout == 'top-menu',
    "before:content-[''] before:absolute before:h-[65px] before:inset-0 before:top-0 before:mx-7 before:bg-primary/30 before:mt-3 before:rounded-xl before:hidden before:md:block before:dark:bg-darkmode-600/30",
    "after:content-[''] after:absolute after:inset-0 after:h-[65px] after:mx-3 after:bg-primary after:mt-5 after:rounded-xl after:shadow-md after:hidden after:md:block after:dark:bg-darkmode-600",
])>
    <div class="flex h-full items-center">
        <!-- BEGIN: Logo -->
        <a href="{{ route('admin.home') }}" class="logo -intro-x hidden md:flex xl:w-[180px] block">
            <img class="w-6" src="{{ Vite::asset('resources/images/logo.svg') }}"/>
            <span class="logo__text text-white text-lg ml-3">NORDI HOME</span>
        </a>
        <!-- END: Logo -->
        <!-- BEGIN: Breadcrumb -->
        <nav aria-label="breadcrumb" class="-intro-x h-[45px] mr-auto">
            @section('breadcrumbs')
                {{\Diglactic\Breadcrumbs\Breadcrumbs::render()}}
            @show
        </nav>
        <!-- END: Breadcrumb -->
        <!-- BEGIN: Search -->
        <div class="intro-x relative mr-3 sm:mr-6">
            <div class="search relative hidden sm:block">
                <input type="text" class="search__input form-control border-transparent" placeholder="Search...">
                <i data-lucide="Search" width="24" height="24"
                   class="absolute inset-y-0 right-0 my-auto mr-3 h-5 w-5 text-slate-600 dark:text-slate-500"></i>
            </div>
            <a
                class="relative text-white/70 sm:hidden"
                href=""
            >
                <i data-lucide="Search" width="24" height="24" class="h-5 w-5 dark:text-slate-500"></i>
            </a>

        </div>
        <!-- END: Search -->
        <!-- BEGIN: Notifications -->
        <x-base.popover class="intro-x mr-4 sm:mr-6">
            <x-base.popover.button
                class="relative block text-white/70 outline-none before:absolute before:top-[-2px] before:right-0 before:h-[8px] before:w-[8px] before:rounded-full before:bg-danger before:content-['']"
            >
                <x-base.lucide
                    class="h-5 w-5 dark:text-slate-500"
                    icon="Bell"
                />
            </x-base.popover.button>
            <x-base.popover.panel class="mt-2 w-[280px] p-5 sm:w-[350px]">
                <div class="mb-5 font-medium">Notifications</div>
                @foreach (array_slice($fakers, 0, 5) as $fakerKey => $faker)
                    <div @class([
                        'cursor-pointer relative flex items-center',
                        'mt-5' => $fakerKey,
                    ])>
                        <div class="image-fit relative mr-1 h-12 w-12 flex-none">
                            <img
                                class="rounded-full"
                                src="{{ Vite::asset($faker['photos'][0]) }}"
                                alt="Midone Tailwind HTML Admin Template"
                            />
                            <div
                                class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-success dark:border-darkmode-600">
                            </div>
                        </div>
                        <div class="ml-2 overflow-hidden">
                            <div class="flex items-center">
                                <a
                                    class="mr-5 truncate font-medium"
                                    href=""
                                >
                                    {{ $faker['users'][0]['name'] }}
                                </a>
                                <div class="ml-auto whitespace-nowrap text-xs text-slate-400">
                                    {{ $faker['times'][0] }}
                                </div>
                            </div>
                            <div class="mt-0.5 w-full truncate text-slate-500">
                                {{ $faker['news'][0]['short_content'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </x-base.popover.panel>
        </x-base.popover>

        <!-- END: Notifications -->
        <!-- BEGIN: Account Menu -->
        <x-base.menu>
            <x-base.menu.button
                class="image-fit zoom-in intro-x block h-8 w-8 scale-110 overflow-hidden rounded-full shadow-lg"
            >
                <img src="{{ $current_user->getPhoto() }}">
            </x-base.menu.button>
            <x-base.menu.items
                class="relative mt-px w-56 bg-primary/80 text-white before:absolute before:inset-0 before:z-[-1] before:block before:rounded-md before:bg-black"
            >
                <x-base.menu.header class="font-normal">
                    <div class="font-medium">{{ $current_user->fullName->getShortName() }}</div>
                    <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">{{ $current_user->post }}</div>
                </x-base.menu.header>
                <x-base.menu.divider class="bg-white/[0.08]" />
                <x-base.menu.item class="hover:bg-white/5">

                    <x-base.lucide
                        class="mr-2 h-4 w-4"
                        icon="mail"
                    /> Сообщения
                </x-base.menu.item>
                <x-base.menu.divider class="bg-white/[0.08]" />
                <x-base.menu.item class="hover:bg-white/5">
                    <x-base.lucide
                        class="mr-2 h-4 w-4"
                        icon="ToggleRight"
                    /> Logout
                </x-base.menu.item>
            </x-base.menu.items>
        </x-base.menu>



        <!-- END: Account Menu -->
    </div>
</div>
<!-- END: Top Bar -->

