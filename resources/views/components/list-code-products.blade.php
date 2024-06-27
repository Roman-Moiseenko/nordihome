<div {{ $attributes->merge(['class' => "list-code-products"]) }}>
    <form method="POST" action="{{ $route }}">
        @csrf
        <div class="flex">
            <div class="relative items-center flex" style="width: 200px;">
                <textarea class="form-control area-code-products" rows="1" name="products"></textarea>
            </div>
            <div class="ml-3 items-center">
                <button id="add-products" type="submit" class="btn btn-primary-soft">{{ $captionButton }}</button>
            </div>
        </div>
    </form>
</div>
@once
    @push('scripts')
        @vite('resources/js/components/list-code-products.js')
    @endpush

    @push('scripts')
        @vite('resources/css/components/_list-code-products.css')
    @endpush
@endonce
