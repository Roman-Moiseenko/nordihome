<div class="widget-variants {{ $class }}">
    <span class="widget-name">{{ $caption }}</span>
    {{ $slot }}
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
