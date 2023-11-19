<div class="widget-variants {{ $class }}">
    <span class="fs-6">{{ $caption }}</span>
    {{ $slot }}
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget/index.js')
    @endpush
@endonce
