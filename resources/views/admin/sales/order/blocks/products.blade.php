<div class="grid grid-cols-12 gap-x-6">

    <div class="col-span-12">
        <div class="mx-3 flex w-full mb-5">
            <input id="route-search" type="hidden" value="{{ route('admin.sales.order.get-to-order') }}">
            <x-searchProduct route="{{ route('admin.sales.order.search') }}"
                             input-data="order-product" hidden-id="product_id" class="w-1/3"/>
            {{ \App\Forms\Input::create('quantity', ['placeholder' => 'Кол-во', 'value' => 1, 'class' => 'ml-2 w-20'])->type('number')->show() }}
            <x-base.button id="add-product" type="button" variant="primary" class="ml-3">Добавить товар в документ
            </x-base.button>
        </div>

        <h2 class=" mt-3 font-medium">Товар в наличии</h2>
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center">№ п/п</div>
            <div class="w-1/4 text-center">Товар</div>
            <div class="w-40 text-center">Цена</div>
            <div class="w-40 text-center">Скидочная цена</div>
            <div class="w-40 text-center">Кол-во</div>
            <div class="w-20 text-center">-</div>
        </div>

        <div id="free_products"></div>
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-1/4 text-center">ИТОГО</div>
            <div class="w-40 text-center">
                <div class="w-40 input-group">
                    <input id="free-amount" type="number" class="form-control text-right" value="" aria-describedby="input-free-amount" readonly>
                    <div id="input-free-amount" class="input-group-text">₽</div>
                </div>
            </div>
        </div>

        <h2 class=" mt-3 font-medium">Товар на заказ</h2>
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center">№ п/п</div>
            <div class="w-1/4 text-center">Товар</div>
            <div class="w-40 text-center">Цена</div>
            <div class="w-40 text-center">Кол-во</div>
            <div class="w-20 text-center">-</div>
        </div>
        <div id="preorder_products"></div>
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-1/4 text-center">ИТОГО</div>
            <div class="w-40 text-center">
                <div class="w-40 input-group">
                    <input id="preorder-amount" type="number" class="form-control text-right" value="" aria-describedby="input-preorder-amount" readonly>
                    <div id="input-preorder-amount" class="input-group-text">₽</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let buttonAddProduct = document.getElementById('add-product');

    buttonAddProduct.addEventListener('click', function () {
        let productId = document.getElementById('hidden-id').value;
        let quantity = document.getElementById('input-quantity').value;
        //Очищаем поля
        document.getElementById('order-product').value = '';
        document.getElementById('hidden-id').value = '';
        document.getElementById('input-quantity').value = 1;
        //Получаем кол-во свободного и на заказ
        //AJAX
        let route = document.getElementById('route-search').value;
        let userId = document.getElementById('input-user-id').value;
        let _params = '_token=' + '{{ csrf_token() }}' + '&product_id=' + productId + '&quantity=' + quantity + '&user_id=' + userId;
        let request = new XMLHttpRequest();
        request.open('POST', route);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let data = JSON.parse(request.responseText);
                if (data.free !== false) checkProducts(freeProducts, data.free); //Заполняем вналичии
                if (data.preorder !== false) checkProducts(preorderProducts, data.preorder);//Заполняем назаказ

                updateProducts();
                updateAmount();
            } else {
                //console.log(request.responseText);
            }
        };
    });

    /**
     * Проверяем есть ли товар уже в списке, если нет, то добавляем
     * @param _array {array}
     * @param _product
     */
    function checkProducts(_array, _product) {
        let _in_array = false;
        _array.forEach(function (element) {
            if (element.id === _product.id) _in_array = true;
        });
        if (_in_array === false) _array.push(_product);
    }

    function updateProducts() {
        let pointFreeProducts = document.getElementById('free_products');
        let pointPreorderProducts = document.getElementById('preorder_products');
        viewProducts(pointFreeProducts, freeProducts); //Вставить в таблицу в наличии
        viewProducts(pointPreorderProducts, preorderProducts); //Вставить в таблицу на заказ
        let buttonsRemoveProduct = document.querySelectorAll('.product-remove');
        let inputsQuantity = document.querySelectorAll('.quantity-input');

        Array.from(buttonsRemoveProduct).forEach(function (buttonRemoveProduct) {
            buttonRemoveProduct.addEventListener('click', function () {
                let id_product = buttonRemoveProduct.getAttribute('data-id');
                let _num = Number(buttonRemoveProduct.getAttribute('data-num'));
                let _array = buttonRemoveProduct.getAttribute('data-array');
                console.log(id_product);
                deleteProduct(_array, _num);
            })
        });
        Array.from(inputsQuantity).forEach(function (inputQuantity) {
            inputQuantity.addEventListener('change', function () {
                let _array = inputQuantity.getAttribute('data-array');
                let _num = Number(inputQuantity.getAttribute('data-num'));
                let _count = inputQuantity.value;
                setCountProduct(_array, _num, _count);
            });
        });
    }

    /**
     * Вставка списка товаров в блок В_наличии или На_заказ
     * @param _block
     * @param _array
     */
    function viewProducts(_block, _array) {
        _block.innerHTML = '';
        for (let i = 0; i < _array.length; i++) {
            let _line = getLineProduct(i, _array[i]);
            _block.insertAdjacentHTML('beforeend', _line);
        }
    }

    /**
     * Удалить товар из списка по типу массива
     * @param _array {string}
     * @param _num {number}
     */
    function deleteProduct(_array, _num) {
        if (_array === 'free') freeProducts.splice(_num, 1);
        if (_array === 'preorder') preorderProducts.splice(_num, 1);
        updateProducts();
        updateAmount();
    }

    /**
     * Установить кол-во товара в массиве
     * @param _array {string}
     * @param _num {number}
     * @param _count {number}
     */
    function setCountProduct(_array, _num, _count) {
        if (_array === 'free') freeProducts[_num].count = _count;
        if (_array === 'preorder') preorderProducts[_num].count = _count;
        updateAmount();
    }

    /**
     * Получить html-код строки с товаром в заказ
     * @param i {integer}
     * @param product
     * @returns {string}
     */
    function getLineProduct(i, product) {
        let promotion = '';
        if (product.promotion !== undefined) {
            promotion = ''+
                ' <div class="w-40 input-group">'+
                    '<input type="number" class="form-control text-right"'+
                    'value="' + product.promotion + '" aria-describedby="input-cost_ru" readonly>'+
                    '<div id="input-cost_ru" class="input-group-text">₽</div>'+
                ' </div>';

        }
        let result = '' +
        '<div class="box flex items-center px-2" data-id="">' +
            '<div class="w-20">' + (i + 1) +'</div>'+
            '<div class="w-1/4">' + product.name + '</div>'+
            '<div class="w-40 input-group">'+
                '<input id="" type="number" class="form-control text-right"'+
                       'value="' + product.cost + '" aria-describedby="input-currency" min="0" readonly>'+
                    '<div id="input-currency" class="input-group-text">₽</div>'+
            '</div>'+
            promotion +
            '<div class="w-40 input-group">'+
                '<input id="quantity" type="number" class="form-control text-right quantity-input"'+
                       'value="' + product.count + '" aria-describedby="input-quantity" min="0" ' +
                        'data-array="' + (product.promotion !== undefined ? 'free' : 'preorder') + '"' +
                        'data-num = "' + i + '"'+
                        (product.promotion !== undefined ? 'max="' + product.max + '"' : '') +' >'+
                    '<div id="input-quantity" class="input-group-text">шт.</div>'+
           ' </div>'+

            '<button class="btn btn-outline-danger ml-6 product-remove"'+
                    'data-num = "' + i + '"'+
                    ' data-id="' + product.id + '" data-array="' + (product.promotion !== undefined ? 'free' : 'preorder') + '" type="button">'+
            'X'+
            '</button>'+
       ' </div>';
        return result;
    }

</script>
