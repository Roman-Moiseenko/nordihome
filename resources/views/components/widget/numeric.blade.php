<div class="widget-numeric {{ $class }}">
    <label class="widget-name">{{ $slot }}</label>
    <div class="group-numeric-input">
        <div class="numeric-input">
            <input type="number" name="{{ $name }}[]" id="{{ $id }}-min" class="input-numeric"
                   min="{{ $minValue }}" max="{{ $maxValue }}" value="{{ $currentMin }}" placeholder="от {{ $minValue }}"
                   autocomplete="off"/>
            <span id="{{ $id }}-min-clear" class="clear-icon-input"></span>
        </div>
        <div class="numeric-input">
            <input class="input-numeric" type="number" name="{{ $name }}[]" id="{{ $id }}-max" min="{{ $minValue }}"
                   max="{{ $maxValue }}" value="{{ $currentMax }}" placeholder="до {{ $maxValue }}" autocomplete="off"/>
            <span id="{{ $id }}-max-clear" class="clear-icon-input"></span>
        </div>
    </div>
</div>
@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
