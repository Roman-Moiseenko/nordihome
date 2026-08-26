<div class="checkbox-group">
    <input class="variant-input" type="checkbox" id="{{ $productName . '-' . $id }}" name="{{ $productName }}"
           value="{{ $id }}" {{ $checked ? 'checked' : '' }}/>
    <label for="{{ $productName . '-' . $id }}">{{ $caption }}</label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
