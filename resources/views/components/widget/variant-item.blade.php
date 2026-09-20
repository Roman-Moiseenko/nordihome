<div class="checkbox-group">
    <input class="variant-input" type="checkbox" id="{{ $code . '-' . $id }}" name="{{ $code }}"
           value="{{ $id }}" {{ $checked ? 'checked' : '' }}/>
    <label for="{{ $code . '-' . $id }}">{{ $caption }}</label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
