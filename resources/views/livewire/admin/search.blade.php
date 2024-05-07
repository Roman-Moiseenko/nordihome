<div>
    <input type="text" name="city" list="products" wire:model="search" wire:keyup="find_products" wire:change="select">
    <datalist id="products">
        @foreach($products as $product)
        <option value="{{ $product->name }}">
        @endforeach
    </datalist>



</div>
