<div class="flex search-add-product @if($column) flex-col @endif">
    <span id="data" data-route="{{ $routeSearch }}" data-parser="{{ $parser }}" data-published="{{ $published }}"
          data-show-stock="{{ $showStock }}" data-show-count="{{ $showCount }}" data-token="{{ csrf_token() }}" data-quantity="{{ $quantity }}"></span>
    <select id="search-product-component" class="tom-select search-product form-control
    @if($column) w-full @else w-{{ $width }} @endif">
        <option id="0"></option>
    </select>
    @if($quantity)
        <input id="input-quantity-component" class="form-control w-20 ml-2" type="number" value="1" min="1" autocomplete="off">
    @endif
    <button id="button-send-component" class="btn btn-primary @if($column) mt-2 @else ml-2 @endif" type="button" data-route="{{ $route }}"
            data-event="{{ $event }}">{{ $caption }}
    </button>

    <script>
    </script>
    @once
        @push('scripts')
            @vite('resources/js/components/search-add-product.js')
        @endpush
    @endonce
</div>

