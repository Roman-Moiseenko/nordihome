<div class="widget-check {{ $class }}">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="{{ $id }}" name="{{ $name }}" {{ $checked ? 'checked' : ''}}
       @if(!empty($route)) data-route="{{ $route }}" @endif
        >
        <label class="form-check-label" for="{{ $id }}">{{ $slot }}</label>
    </div>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
