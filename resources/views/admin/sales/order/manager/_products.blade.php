
<div class="mx-3 flex w-full mb-5">
    <input id="route-search" type="hidden" value="{{ route('admin.sales.order.get-to-order') }}">
    <x-searchProduct route="{{ route('admin.sales.order.search') }}"
                     input-data="order-product" hidden-id="product_id" class="w-1/3"/>
    {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->type('number')->min_max(1, null)->show() }}
    <x-base.button id="add-product" type="button" variant="primary" class="ml-3" data-route="">Добавить товар в документ</x-base.button>
</div>

<livewire:admin.sales.order.manager-items :order="$order" />


<livewire:admin.sales.order.manager-amount :order="$order"/>

<script>
    let addProductButton = document.getElementById('add-product');
    addProductButton.addEventListener('click', function () {
        let hiddenInput = document.getElementById('hidden-id');
        let quantityInput = document.getElementById('input-quantity');
        Livewire.dispatch(
            'add-product',
            {
                product_id: hiddenInput.value,
                quantity:  quantityInput.value,
            }
        );
        hiddenInput.value = '';
        quantityInput.value = 1;
        document.getElementById('order-product').value = '';
    });
</script>
