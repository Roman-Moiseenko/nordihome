<div class="variant-image-container {{ $checked ? 'active' : '' }}">
    <input class="variant-image-input" type="checkbox" id="{{ $productName . '-' . $id }}" name="{{ $productName }}"
           value="{{ $id }}" {{ $checked ? 'checked' : '' }}/>
    <label for="{{ $productName . '-' . $id }}">
        <img src="{{ $image }}" alt="{{ $alt }}"/> <span>{{ $caption }}</span>
    </label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
