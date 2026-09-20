<div class="variant-image-container {{ $checked ? 'active' : '' }}">
    <input class="variant-image-input" type="checkbox" id="{{ $code . '-' . $id }}" name="{{ $code }}"
           value="{{ $id }}" {{ $checked ? 'checked' : '' }}/>
    <label for="{{ $code . '-' . $id }}">
        <img src="{{ $image }}" alt="{{ $alt }}"/> <span>{{ $caption }}</span>
    </label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
