<div class="variant-image-container {{ $checked ? 'active' : '' }}">
    <input class="variant-image-input" type="checkbox" id="{{ $name . '-' . $id }}" name="{{ $name }}" value="{{ $id }}"  {{ $checked ? 'checked' : '' }}/>
    <label for="{{ $name . '-' . $id }}">
        <img src="{{ $image }}" alt="{{ $alt }}"/> <span>{{ $caption }}</span>
    </label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget.js')
    @endpush
@endonce
