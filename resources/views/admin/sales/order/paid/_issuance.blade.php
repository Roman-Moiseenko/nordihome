<input id="route" type="hidden" value="{{ route('admin.sales.order.expense-calculate', $order) }}">
<h2 class=" mt-3 font-medium">Товар</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Цена продажи</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-40 text-center">Наличие/Резерв/Склад</div>
    <div class="w-20 text-center"></div>
</div>
@foreach($order->items as $i => $item)
    @include('admin.sales.order.paid._issuance-item', ['i' => $i, 'item' => $item])
@endforeach

<h2 class=" mt-3 font-medium">Услуги</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-56 text-center">Услуга</div>
    <div class="w-40 text-center">Сумма</div>
    <div class="w-56 text-center">Примечание</div>
    <div class="w-20 text-center"></div>
</div>

@foreach($order->additions as $i => $addition)
    @include('admin.sales.order.paid._issuance-addition', ['i' => $i, 'addition' => $addition])
@endforeach

<div class="box flex items-center font-semibold mt-3 p-2">
    <div class="w-40 text-center">Сумма в распоряжении</div>
    <div id="expense-amount" class="w-40 text-center">{{ price($order->getTotalAmount() - $order->getExpenseAmount()) }}</div>
    <div class="w-40 text-center">Остаток по оплате</div>
    <div id="remains-amount" class="w-40 text-center">{{ price($order->getPaymentAmount() - $order->getExpenseAmount()) }}</div>
    <div class="w-56 text-center">
        <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="primary" class="w-100"
                id="button-expense" data-disabled="{{ ($order->getTotalAmount() > $order->getPaymentAmount()) }}">
                Создать распоряжение
                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
            </x-base.popover.button>
            <x-base.popover.panel>
                <div class="p-2">
                    <x-base.tom-select id="select-storage" name="storage" class=""
                                       data-placeholder="Выберите Склад">
                        <option value="0"></option>
                        @foreach($storages as $storage)
                            <option value="{{ $storage->id }}"
                            >{{ $storage->name }}</option>
                        @endforeach
                    </x-base.tom-select>

                    <div class="flex items-center mt-3">
                        <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">
                            Отмена
                        </x-base.button>
                        <button id="create-expense" class="w-32 ml-2 btn btn-primary" type="button" data-route="{{ route('admin.sales.expense.store') }}">
                            Создать
                        </button>
                    </div>
                </div>
            </x-base.popover.panel>
        </x-base.popover>
    </div>
</div>


<script>
    let route = document.getElementById('route').value;
    let inputUpdateData = document.querySelectorAll('.update-data-ajax');
    let checkboxUpdateData = document.querySelectorAll('input[type=checkbox].update-data-ajax');

    let expenseAmount = document.getElementById('expense-amount');
    let remainsAmount = document.getElementById('remains-amount');
    let createExpense = document.getElementById('create-expense');

    let buttonExpense = document.getElementById('button-expense');
    let buttonCreateExpense = document.getElementById('create-expense');

    setAjax(route, _check(), _updateData);
    Array.from(inputUpdateData).forEach(function (input) {
        input.addEventListener('change', function () {
            setAjax(route, _check(), _updateData)
        });
    });

    buttonCreateExpense.addEventListener('click', function () {
        let route = buttonCreateExpense.dataset.route;
        let selectStorage = document.getElementById('select-storage');
        let data = _check();
        data['storage_id'] = Number(selectStorage.value);
        if (data['storage_id'] === 0) {
            window.notification('Неполные данные', 'Не выбран склад выдачи заказа', 'info');
            return;
        }
        data['order_id'] = {{ $order->id }};
        console.log(data);
        setAjax(route, data , _expenseShow);
    });

    function _check() {
        let data = {items: [], additions: []};
        Array.from(checkboxUpdateData).forEach(function (checkbox) {
            if (checkbox.checked) {
                let id = checkbox.value;
                let value = document.getElementById(checkbox.dataset.input).value;
                let key = checkbox.getAttribute('name');
                data[key].push({
                    id: id,
                    value: value
                });
            }
        });

        return data;
    }

    function setAjax(route, data, callback) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&data=' + JSON.stringify(data);
        let request = new XMLHttpRequest();
        request.open('POST', route);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let data = JSON.parse(request.responseText);
                if (callback !== undefined) callback(data);
            }
        };
    }

    function _updateData(data) {
        expenseAmount.innerText = data.expense;
        remainsAmount.innerText = data.remains;
        buttonExpense.disabled = data.disable;
        //createExpense.disabled = data.disable;
    }

    function _expenseShow(data) {
        if (data.error !== undefined) {
            //Notification
            window.notification('Ошибка при создании распоряжения',data.error ,'danger');
            return;
        }

        window.location.href = data; //data - содержит УРЛ
    }
</script>

