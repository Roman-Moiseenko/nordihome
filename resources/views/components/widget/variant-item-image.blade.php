<div class="variant-image-container">
    <input class="variant-image-input" type="checkbox" id="{{ $name . '-' . $id }}" name="{{ $name }}" value="{{ $id }}"/>
    <label for="{{ $name . '-' . $id }}">
        <img src="{{ $image }}" /> <span>{{ $caption }}</span>
    </label>
</div>

@once
    @push('scripts')
        @vite('resources/js/components/widget/index.js')
    @endpush
@endonce
