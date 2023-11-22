<div class="variant-image-container {{ $checked ? 'active' : '' }}">
    <input class="variant-image-input" type="checkbox" id="{{ $name . '-' . $id }}" name="{{ $name }}" value="{{ $id }}"  {{ $checked ? 'checked' : '' }}/>
    <label for="{{ $name . '-' . $id }}">
        <img src="{{ $image }}" /> <span>{{ $caption }}</span>
    </label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget/index.js')
    @endpush
@endonce
