@props(['layout' => 'side-menu'])

<!-- BEGIN: Top Bar -->
<div
    class="top-bar-boxed h-[70px] md:h-[65px] z-[51] border-b border-white/[0.08] mt-12 md:mt-0 -mx-3 sm:-mx-8 md:-mx-0 px-3 md:border-b-0 relative md:fixed md:inset-x-0 md:top-0 sm:px-8 md:px-10 md:pt-10 md:bg-gradient-to-b md:from-slate-100 md:to-transparent dark:md:from-darkmode-700">
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
        <div class="intro-x dropdown mr-4 sm:mr-6">
            <div class="dropdown-toggle notification notification--bullet cursor-pointer" role="button"
                 aria-expanded="false" data-tw-toggle="dropdown">
                <i data-lucide="Bell" width="24" height="24" class="h-5 w-5 dark:text-slate-500"></i>
            </div>
            <div class="notification-content pt-2 dropdown-menu">
                <div class="notification-content__box dropdown-content">
                    <div class="notification-content__title">Notifications</div>
                    @foreach($fakers as  $fakerKey => $faker)
                        <div class="cursor-pointer relative flex items-center {{ $fakerKey != 0 ? 'mt-5' : '' }}">
                            <div class="w-12 h-12 flex-none image-fit mr-1">
                                <img class="rounded-full" src="{{ Vite::asset($faker['photos'][0]) }}">
                                <div
                                    class="w-3 h-3 bg-success absolute right-0 bottom-0 rounded-full border-2 border-white"></div>
                            </div>
                            <div class="ml-2 overflow-hidden">
                                <div class="flex items-center">
                                    <a href="javascript:;"
                                       class="font-medium truncate mr-5">{{ $faker['users'][0]['name'] }}</a>
                                    <div
                                        class="text-xs text-slate-400 ml-auto whitespace-nowrap">{{ $faker['times'][0] }}</div>
                                </div>
                                <div
                                    class="w-full truncate text-slate-500 mt-0.5">{{ $faker['news'][0]['short_content'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- END: Notifications -->
        <!-- BEGIN: Account Menu -->
        <div class="intro-x dropdown w-8 h-8">
            <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110" role="button" aria-expanded="false" data-tw-toggle="dropdown">
                <img src="{{ $current_user->getPhoto() }}">
            </div>
            <div class="dropdown-menu w-56">
                <ul class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                    <li class="p-2">
                        <div class="font-medium">{{ $current_user->fullName->getShortName() }}</div>
                        <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">{{ $current_user->post }}</div>
                    </li>
                    <li>
                        <hr class="dropdown-divider border-white/[0.08]">
                    </li>

                    <li>
                        <a href="/" class="dropdown-item hover:bg-white/5">
                            <i data-lucide="mail" width="24" height="24" class="mr-2 h-4 w-4"></i> Сообщения</a>
                    </li>

                    <li>
                        <hr class="dropdown-divider border-white/[0.08]">
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" class="dropdown-item hover:bg-white/5"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i data-lucide="toggle-right" width="24" height="24" class="mr-2 h-4 w-4"></i> Logout </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <!-- END: Account Menu -->
    </div>
</div>
<!-- END: Top Bar -->

