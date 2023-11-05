<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        <x-base.form-label for="select-equivalent">Связанная группа аналогичных товаров</x-base.form-label>
        <x-base.tom-select id="select-equivalent" name="equivalent_id" class="w-full"
                           data-placeholder="Выберите группу аналогов товара">
            <option value="0"></option>
            @foreach($equivalents as $equivalent)
                <option value="{{ $equivalent->id }}"
                @if(!is_null($product->equivalent()))
                    {{ $equivalent->id == $product->equivalent->id ? 'selected' : ''}}
                    @endif
                >
                    {{ $equivalent->name }}
                </option>
            @endforeach
        </x-base.tom-select>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <h3 class="font-medium text-center">Товары из группы</h3>
        <div id="equivalent-products" class="mt-3 ml-3">
        @if(!is_null($product->equivalent()))
            @foreach($product->equivalent->products as $_product)
                <div class="mt-1 border-b text-center text-slate-400">
                    {{ $_product->name }}
                </div>
            @endforeach
        @endif
        </div>
    </div>
</div>
<script>

    const selectEquivalent = document.getElementById('select-equivalent');
    const EquivalentProducts = document.getElementById('equivalent-products');
    selectEquivalent.addEventListener('change', function () {
        let equivalent_id = selectEquivalent.options[selectEquivalent.selectedIndex].value;
        //AJAX
        if (equivalent_id === '0') {
            EquivalentProducts.innerHTML = '';
            return;
        }
        let _params = '_token=' + '{{ csrf_token() }}';
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/equivalent/'+ equivalent_id +'/json-products');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let _ListProducts = JSON.parse(request.responseText);
                EquivalentProducts.innerHTML = ''; //Очищаем список
                _ListProducts.forEach(function (item) {
                    let _t = '<div class="mt-1 border-b text-center text-slate-400">' + item + '</div>';
                    EquivalentProducts.insertAdjacentHTML('afterbegin', _t); //Заполняем полученными из ajax
                });
            } else {
                //console.log(request.responseText);
            }

        };
    });
</script>
