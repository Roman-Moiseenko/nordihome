<input id="route" type="hidden" value="{{ route('admin.sales.order.expense-calculate', $order) }}">
<h2 class=" mt-3 font-medium">Товар</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Цена продажи</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="text-center flex">
        <div class="w-32">Склад</div>
        <div class="w-20">Доступно</div>
        <div class="w-32">Резерв</div>
    </div>

    <div class="w-20 text-center"></div>
</div>
@foreach($order->items as $i => $item)
    <livewire:admin.sales.order.paid-item :item="$item" :i="$i" :storages="$storages" />
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
    <livewire:admin.sales.order.paid-addition :addition="$addition" :i="$i" />
@endforeach

<div class="box flex items-center font-semibold mt-3 p-2">
    <div class="w-40 text-center">Сумма в распоряжении</div>
    <div id="expense-amount" class="w-40 text-center">{{ price($order->getTotalAmount() - $order->getExpenseAmount()) }}</div>
    <div class="w-40 text-center">Остаток на выдачу</div>
    <div class="w-40 text-center">
        <span id="remains-amount">{{ price($order->getPaymentAmount() - $order->getExpenseAmount() + $order->getCoupon() + $order->getDiscountOrder()) }}</span>&nbsp;
        (<span id="discount-amount" class="font-medium">{{ price($order->getCoupon() + $order->getDiscountOrder()) }}</span>)
    </div>
    <div>
        <button id="button-expense" class="btn btn-primary ml-2 mr-2" type="button"
                data-route="{{ route('admin.sales.expense.create') }}" data-storage="{{ $mainStorage->id }}"
            {{ ($order->getTotalAmount() > $order->getPaymentAmount()) ? 'disables' : ''  }}>Распоряжение на отгрузку</button>
    </div>

    <div class="w-56 text-center">
        <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="warning" class="w-100"
                                   id="button-issue-shop" data-disabled="{{ ($order->getTotalAmount() > $order->getPaymentAmount()) }}">
                Выдать с магазина
                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
            </x-base.popover.button>
            <x-base.popover.panel>
                <div class="p-2">
                    <x-base.tom-select id="select-storage-issue-shop" name="storage" class=""
                                       data-placeholder="Выберите Магазин">
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
                        <button id="create-issue-shop" class="w-32 ml-2 btn btn-primary" type="button" data-route="{{ route('admin.sales.expense.issue-shop') }}">
                            Выдать
                        </button>
                    </div>
                </div>
            </x-base.popover.panel>
        </x-base.popover>
    </div>
    <div class="w-56 text-center">
        <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="success" class="w-100"
                                   id="button-issue" data-disabled="{{ ($order->getTotalAmount() > $order->getPaymentAmount()) }}">
                Выдать со склада
                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
            </x-base.popover.button>
            <x-base.popover.panel>
                <div class="p-2">
                    <x-base.tom-select id="select-storage-issue" name="storage" class=""
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
                        <button id="create-issue" class="w-32 ml-2 btn btn-primary" type="button" data-route="{{ route('admin.sales.expense.issue-warehouse') }}">
                            Выдать
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
    let discountAmount = document.getElementById('discount-amount');
    let createExpense = document.getElementById('create-expense');

    let buttonExpense = document.getElementById('button-expense');
    //let buttonCreateExpense = document.getElementById('create-expense');
    let buttonCreateExpense = document.getElementById('button-expense');

    let buttonIssue = document.getElementById('button-issue');
    let buttonCreateIssue = document.getElementById('create-issue');
    let buttonIssueShop = document.getElementById('button-issue-shop');
    let buttonCreateIssueShop = document.getElementById('create-issue-shop');


    setAjax(route, _check(), _updateData);

    document.addEventListener('livewire:init', () => {
        Livewire.on('issuance-update', (event) => {
            console.log(event);
            setAjax(route, _check(), _updateData);
            //if(event.icon === undefined) event.icon = 'danger';
            //window.notification(event.title, event.message, event.icon);
        });
    });


 /*   Array.from(inputUpdateData).forEach(function (input) {
        input.addEventListener('change', function () {
            setAjax(route, _check(), _updateData)
        });
    });

*/

    buttonCreateExpense.addEventListener('click', function () {
        let route = buttonCreateExpense.dataset.route;
        //let selectStorage = document.getElementById('select-storage');
        let selectStorage = buttonCreateExpense.dataset.storage;
        let data = _check();
        data['storage_id'] = Number(selectStorage); //selectStorage.value
        /* if (data['storage_id'] === 0) {
            window.notification('Неполные данные', 'Не выбран склад выдачи заказа', 'info');
            return;
        } */
        data['order_id'] = {{ $order->id }};
        setAjax(route, data , _expenseShow);
    });

    buttonCreateIssue.addEventListener('click', function () {
        let route = buttonCreateIssue.dataset.route;
        let selectStorage = document.getElementById('select-storage-issue');
        let data = _check();
        data['storage_id'] = Number(selectStorage.value);
         if (data['storage_id'] === 0) {
            window.notification('Неполные данные', 'Не выбран склад выдачи заказа', 'info');
            return;
        }
        data['order_id'] = {{ $order->id }};
        setAjax(route, data , _expenseShow);
    });


    buttonCreateIssueShop.addEventListener('click', function () {
        let route = buttonCreateIssueShop.dataset.route;
        let selectStorage = document.getElementById('select-storage-issue-shop');
        let data = _check();
        data['storage_id'] = Number(selectStorage.value);
        if (data['storage_id'] === 0) {
            window.notification('Неполные данные', 'Не выбран магазин получения товара', 'info');
            return;
        }
        data['order_id'] = {{ $order->id }};
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
        discountAmount.innerText = data.discount;
        buttonExpense.disabled = data.disable;
        buttonIssue.disabled = data.disable;
        buttonIssueShop.disabled = data.disable;
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

