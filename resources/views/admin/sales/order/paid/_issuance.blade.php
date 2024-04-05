<input id="route" type="hidden" value="{{ route('admin.sales.order.expense-calculate', $order) }}">
<h2 class=" mt-3 font-medium">Товар</h2>
<div class="box flex items-center font-semibold p-2">
    <div class="w-20 text-center">№ п/п</div>
    <div class="w-1/4 text-center">Товар/Габариты</div>
    <div class="w-32 text-center">Цена продажи</div>
    <div class="w-20 text-center">Кол-во</div>
    <div class="w-20 text-center"></div>
</div>
@foreach($order->items as $i => $item)
    @include('admin.sales.order.paid._product-item', ['i' => $i, 'item' => $item])
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
    <div class="box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-56 text-center">{{ $addition->purposeHTML() }}</div>
        <div class="w-40 input-group">
            <input id="addition-amount-{{ $addition->id }}" type="number" class="form-control text-right update-data-ajax"
                   value="{{ $addition->getRemains() }}" aria-describedby="addition-amount"
                   min="0" max="{{ $addition->getRemains() }}"
            >
            <div id="addition->amount" class="input-group-text">₽</div>
        </div>

        <div class="w-56 text-center">{{ $addition->comment }}</div>
        <div class="w-20 text-center">
            <div class="form-check form-switch justify-center mt-3">
                <input id="addition-check-{{ $addition->id }}" class="form-check-input update-data-ajax" type="checkbox"
                       data-input="addition-amount-{{ $addition->id }}" name="additions" value="{{ $addition->id }}" checked>
                <label class="form-check-label" for="additions-check-{{ $addition->id }}"></label>
            </div>
        </div>
    </div>
@endforeach

<div class="box flex items-center font-semibold mt-3 p-2">
    <div class="w-40 text-center">Сумма в распоряжении</div>
    <div id="expense-amount" class="w-40 text-center">{{ price($order->getTotalAmount() - $order->getExpenseAmount()) }}</div>
    <div class="w-40 text-center">Остаток по оплате</div>
    <div id="remains-amount" class="w-40 text-center">{{ price($order->getPaymentAmount() - $order->getExpenseAmount()) }}</div>
    <div class="w-56 text-center">
        <button id="create-expanse" type="button" class="btn btn-primary"
                @if($order->getTotalAmount() > $order->getPaymentAmount()) disabled @endif
            data-route=""
            onclick="document.getElementById('form-create-expance').submit()"
        >Создать распоряжение</button>
    </div>
</div>

<script>
    let route = document.getElementById('route').value;
    let inputUpdateData = document.querySelectorAll('.update-data-ajax');
    let checkboxUpdateData = document.querySelectorAll('input[type=checkbox].update-data-ajax');

    let expenseAmount = document.getElementById('expense-amount');
    let remainsAmount = document.getElementById('remains-amount');
    let createExpanse = document.getElementById('create-expanse');

    Array.from(inputUpdateData).forEach(function (input) {
        input.addEventListener('change', function () {
            setAjax(route, _check())
        });
    });

    function _check() {
        let data = {products: [], additions: []};
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

    function setAjax(route, data) {
        let _params = '_token=' + '{{ csrf_token() }}' + '&data=' + JSON.stringify(data);
        let request = new XMLHttpRequest();
        request.open('POST', route);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let data = JSON.parse(request.responseText);
                expenseAmount.innerText = data.expense;
                remainsAmount.innerText = data.remains;
                createExpanse.disabled = data.disable;
            }
        };
    }
</script>

