<div class="checkbox-group">
    <input class="variant-input" type="checkbox" id="{{ $name . '-' . $id }}" name="{{ $name }}" value="{{ $id }}"/>
    <label for="{{ $name . '-' . $id }}">{{ $caption }}</label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget/index.js')
    @endpush
@endonce
