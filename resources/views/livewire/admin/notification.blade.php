
<div id="notify-component">
    <div class="mr-4 sm:mr-6">
        <button wire:click="toggle_visible"
            class="relative block text-white/70 outline-none
            before:absolute before:top-[-2px] before:right-0
            before:h-[8px] before:w-[8px] before:rounded-full  before:content-[''] @if($count > 0) before:bg-danger @endif"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="bell" class="lucide lucide-bell stroke-1.5 h-5 w-5 dark:text-slate-500"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
        </button>
        <div class="notification-dropdown {{ $visible ? 'show' : 'no-show' }}" @if($count == 0) style="display: none" @endif>
            <div class="dropdown-content rounded-md border-transparent bg-white shadow-[0px_3px_10px_#00000017] mt-2 w-[280px] p-5 sm:w-[460px]">
                <div class="mb-5 flex">
                    <button wire:click="close_dropdown" title="Закрыть" class="btn p-1 text-success items-center font-medium text-lg my-auto w-10 flex-none">
                        X
                    </button>
                    <div class="font-medium my-auto ml-1">
                        Уведомления
                    </div>
                    <div class="ml-auto my-auto">
                        <button wire:click="remove_all" title="Отметить все прочитанными" class="text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>

                @foreach($notifications as $notification)
                    <livewire:admin.notification.item :notification="$notification" :key="$notification->id"  />
                @endforeach
            </div>
        </div>
    </div>
</div>
@once
    @push('styles')
        <script src="https://unpkg.com/lucide@latest"></script>
    @endpush
@endonce



@once
    @push('styles')
        @vite('resources/css/livewire/notification.css')
    @endpush
@endonce
