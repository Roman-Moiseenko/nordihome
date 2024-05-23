
<div id="notify-component" wire:poll.10s="refresh_fields">
    <div class="intro-x mr-4 sm:mr-6">
        <button wire:click="toggle_visible"
            class="relative block text-white/70 outline-none
            before:absolute before:top-[-2px] before:right-0
            before:h-[8px] before:w-[8px] before:rounded-full  before:content-[''] @if($count > 0) before:bg-danger @endif"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="bell" class="lucide lucide-bell stroke-1.5 h-5 w-5 dark:text-slate-500"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
        </button>
        <div class="notification-dropdown {{ $visible ? 'show' : 'no-show' }}" @if($count == 0) style="display: none" @endif>
            <div class="dropdown-content rounded-md border-transparent bg-white shadow-[0px_3px_10px_#00000017] mt-2 w-[280px] p-5 sm:w-[460px]">
                <div class="mb-5 font-medium">Уведомления</div>
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

<script>
    window.addEventListener('click', function(e){
        if (!document.getElementById('notify-component').contains(e.target)){
            Livewire.dispatch('close-notifications');
        }
    });
</script>

@once
    @push('styles')
        @vite('resources/css/livewire/notification.css')
    @endpush
@endonce
