<div class="intro-y col-span-12 md:col-span-6 lg:col-span-4">
    <div class="box">
        <div class="flex items-start px-5 pt-5">
            <div class="w-full flex flex-col lg:flex-row items-center">
                <div class="w-16 h-16 image-fit">
                    <img class="rounded-full" src="{{ $staff->getPhoto() }}">
                </div>
                <div class="lg:ml-4 text-center lg:text-left mt-3 lg:mt-0">
                    <a href="{{ route('admin.staff.show', $staff) }}" class="font-medium {{ ($staff->isBlocked() ? 'text-danger' : 'text-info') }}">{{ $staff->fullname->getFullName() }}</a>
                    <div class="text-slate-500 text-center text-xs mt-0.5 rounded-full text-white {{ \App\Modules\Admin\Entity\Admin::ROLE_COLORS[$staff->role] }}">{{ $staff->post }}</div>
                </div>
            </div>
            <div class="absolute right-0 top-0 mr-5 mt-3 dropdown">
                <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                    <x-base.lucide icon="more-horizontal" class="w-5 h-5 text-slate-500"/>
                </a>
                <div class="dropdown-menu w-40">
                    <div class="dropdown-content">
                        <a href="{{ $staff->isBlocked() ? '' : route('admin.staff.edit', $staff) }}" class="dropdown-item {{ $staff->isBlocked() ? 'disabled' : '' }}">
                            <x-base.lucide icon="pen" class="w-4 h-4 mr-2"/>Редактировать </a>
                        <a href="{{ $staff->isBlocked() ? '' : 'javascript:;' }}"
                           data-staff="{{ route('admin.staff.password', $staff) }}" data-fullname="{{ $staff->fullname->getShortName() }}"
                           data-tw-toggle="modal" data-tw-target="#modal-change-password"
                           class="dropdown-item {{ $staff->isBlocked() ? 'disabled' : '' }} password-modal">
                            <x-base.lucide icon="key-round" class="w-4 h-4 mr-2"/>
                            Пароль </a>
                        @if($staff->isBlocked())
                            <a href="{{ route('admin.staff.activate', $staff) }}" class="dropdown-item"
                               onclick="event.preventDefault(); document.getElementById('activate-form-{{ $staff->id }}').submit();"
                            >
                                <x-base.lucide icon="trash" class="w-4 h-4 mr-2"/>
                                Активировать </a>
                            <form id="activate-form-{{ $staff->id }}" action="{{ route('admin.staff.activate', $staff) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('admin.staff.destroy', $staff) }}" class="dropdown-item"
                               onclick="event.preventDefault(); document.getElementById('destroy-form-{{ $staff->id }}').submit();"
                            >
                                <x-base.lucide icon="trash" class="w-4 h-4 mr-2"/>
                                Заблокировать </a>
                            <form id="destroy-form-{{ $staff->id }}" action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="text-center lg:text-left p-5">
            <div></div>
            <div class="flex items-center justify-center lg:justify-start text-slate-500 mt-5">
                <x-base.lucide icon="mail" class="w-3 h-3 mr-2"/>

                {{ $staff->email }}</div>
            <div class="flex items-center justify-center lg:justify-start text-slate-500 mt-1">
                <x-base.lucide icon="phone" class="w-3 h-3 mr-2"/> {{ $staff->phone }}</div>
        </div>
        <div class="text-center lg:text-right p-5 border-t border-slate-200/60 dark:border-darkmode-400">
            <button class="btn btn-primary py-1 px-2 mr-2 {{ $staff->isBlocked() ? 'disabled' : '' }}" onclick="window.location.href=' {{ $staff->isBlocked() ? '' : route('home') }}'">Сообщение</button>
            <button onclick="window.location.href='{{ route('admin.staff.show', $staff) }}'" class="btn btn-outline-secondary py-1 px-2">Профиль</button>
        </div>
    </div>
</div>
