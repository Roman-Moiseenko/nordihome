<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-6">
        <input id="route-search-user" type="hidden" value="{{ route('admin.sales.order.search-user') }}">
        <input id="input-user-id" type="hidden" value="">
        {{ \App\Forms\Input::create('user-phone', ['placeholder' => 'Телефон', 'class' => 'mt-6 search-user', 'value' => (isset($order) ? $order->user->phone : '')])
            ->help('Цифры, без разделительных символов.')->disabled(isset($order))
            ->show() }}
        {{ \App\Forms\Input::create('user-email', ['placeholder' => 'Почта', 'class' => 'mt-3 search-user', 'value' => (isset($order) ? $order->user->email : '')])
            ->help('Обязательное поле, для отправки счета')->disabled(isset($order))
            ->show() }}
        {{ \App\Forms\Input::create('user-name', ['placeholder' => 'Имя', 'class' => 'mt-3', 'value' => (isset($order) ? $order->user->delivery->fullname->firstname : '')])
            ->help('')->disabled(isset($order))
            ->show() }}
    </div>
    <div class="col-span-12 lg:col-span-6">
        <div class="delivery">
            <div class="">Способ доставки по умолчанию</div>
            <div class="form-check mt-2">
                <input id="delivery-storage" class="form-check-input delivery-input" data-block="storages" type="radio" name="delivery"
                       value="{{ App\Modules\Delivery\Entity\DeliveryOrder::STORAGE }}" {{ (isset($order) && $order->user->delivery->isStorage()) ? 'checked' : ''}}>
                <label class="form-check-label" for="delivery-storage">Самовывоз</label>
            </div>
            <div id="storages" class="delivery-hide-block ml-5" {!! (isset($order) && $order->user->delivery->isStorage()) ? '' : 'style="display: none"' !!}>
                @foreach($storages as $storage)
                    <div class="form-check mt-2">
                        <input id="storage-{{ $storage->id }}" class="form-check-input" type="radio" name="storage"
                               value="{{ $storage->id }}" {{ (isset($order) && $order->user->delivery->storage == $storage->id) ? 'checked' : ''}}>
                        <label class="form-check-label" for="storage-{{ $storage->id }}">{{ $storage->address }}</label>
                    </div>
                @endforeach
            </div>

            <div class="form-check mt-2">
                <input id="delivery-local" class="form-check-input delivery-input" data-block="local" type="radio" name="delivery"
                       value="{{ App\Modules\Delivery\Entity\DeliveryOrder::LOCAL }}" {{ (isset($order) && $order->user->delivery->isLocal()) ? 'checked' : ''}}>
                <label class="form-check-label" for="delivery-local">Доставка по региону</label>
            </div>
            <div id="local" class="delivery-hide-block" {!! (isset($order) && $order->user->delivery->isLocal()) ? '' : 'style="display: none"' !!}>
                {{ \App\Forms\Input::create('delivery-local', ['placeholder' => 'Адрес доставки', 'class' => 'mt-3', 'value' => (isset($order) ? $order->user->delivery->local->address : '')])
                    ->help('')->disabled(isset($order))
                    ->show() }}
            </div>

            <div class="form-check mt-2">
                <input id="delivery-region" class="form-check-input delivery-input" data-block="region" type="radio" name="delivery"
                       value="{{ App\Modules\Delivery\Entity\DeliveryOrder::REGION }}" {{ (isset($order) && $order->user->delivery->isRegion()) ? 'checked' : ''}}>
                <label class="form-check-label" for="delivery-region">Доставка по России</label>
            </div>
            <div id="region" class="delivery-hide-block" {!! (isset($order) && $order->user->delivery->isRegion()) ? '' : 'style="display: none"' !!}>
                {{ \App\Forms\Input::create('delivery-region', ['placeholder' => 'Адрес доставки', 'class' => 'mt-3', 'value' => (isset($order) ? $order->user->delivery->region->address : '')])
                    ->help('')->disabled(isset($order))
                    ->show() }}
            </div>


        </div>
        <div class="mt-4">Способ оплаты по умолчанию</div>
        <select id="user-payment" name="" class="form-select w-full lg:w-1/2">
            <option value="0"></option>
            @foreach(\App\Modules\Order\Entity\Payment\PaymentHelper::payments() as $class => $payment)
                <option value="{{ $class }}">{{ $payment['name'] }}</option>
            @endforeach
        </select>

    </div>
</div>

<script>

    let inputSearchUser = document.querySelectorAll('.search-user>input');
    let deliveryInputs = document.querySelectorAll('.delivery-input');
    let hideBlocks = document.querySelectorAll('.delivery-hide-block');

    //Поиск user по телефону *** по email
    Array.from(inputSearchUser).forEach(function (input) {
        input.addEventListener('change', function () {
            let data = input.value;
            if (data !== '') findUser(input.value);
        })
    });

    //Заполняем данные из input в userData

    function findUser(data) {
        //AJAX
        let route = document.getElementById('route-search-user').value;
        let _params = '_token=' + '{{ csrf_token() }}' + '&data=' + data;
        let request = new XMLHttpRequest();
        request.open('POST', route);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let data = JSON.parse(request.responseText);
                if (data !== false) {
                    //Заполняем поля
                    userData = data;
                    document.getElementById('input-user-phone').value = data.phone;
                    document.getElementById('input-user-email').value = data.email;
                    document.getElementById('input-user-name').value = data.name;
                    //data.delivery.type
                    document.getElementById('storage-' + data.storage).checked = true;
                    document.getElementById('input-delivery-local').value = data.local;
                    document.getElementById('input-delivery-region').value = data.region;
                    //data.payment.class_payment
                    let selectPayment = document.getElementById('user-payment');
                    for (let i = 0; i < selectPayment.options.length; i++) {
                        if (selectPayment.options[i].value === data.payment) {
                            selectPayment.selectedIndex = i;
                            break;
                        }
                    }
                    hideDeliveryBlocks();
                    Array.from(deliveryInputs).forEach(function (input) {
                        if (input.value == data.delivery) {
                            input.checked = true;
                            document.getElementById(input.getAttribute('data-block')).style.display = 'block';
                        }
                    });
                }
            } else {
                //console.log(request.responseText);
            }
        };
    }

    //Обработка выбора способа доставки
    Array.from(deliveryInputs).forEach(function (deliveryInput) {
        deliveryInput.addEventListener('click', function () {
            let _target = deliveryInput.getAttribute('data-block');
            hideDeliveryBlocks();
            document.getElementById(_target).style.display = 'block';
        })
    });

    function hideDeliveryBlocks() {
        Array.from(hideBlocks).forEach(function (hideBlock) {
            hideBlock.style.display = 'none';
        });
    }
</script>
