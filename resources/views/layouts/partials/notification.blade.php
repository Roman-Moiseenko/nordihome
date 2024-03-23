<!-- Либо компонент либо include -->
<div id="notification-block">
    <x-base.notification class="flex flex-row" id="notification-widget">
        <div id="success" class="hidden">
            <i class="text-success" data-lucide="check-circle"></i>
        </div>
        <div id="danger" class="hidden">
            <i class="text-danger" data-lucide="alert-circle"></i>
        </div>
        <div id="info" class="hidden">
            <i class="text-info" data-lucide="info"></i>
        </div>
        <div class="ml-2">
            <div id="title" class="font-medium" >
                Заголовок
            </div>
            <div id="body" class="text-slate-500 mt-1">
                Текст
            </div>
        </div>
    </x-base.notification>
</div>
@once
    @push('vendors')
        @vite('resources/js/vendor/toastify/index.js')
    @endpush
@endonce

@once
    @push('scripts')
        @vite('resources/js/pages/notification/index.js')
    @endpush
@endonce
