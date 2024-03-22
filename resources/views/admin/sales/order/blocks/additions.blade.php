<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12">
        <div class="mx-3 flex w-full mb-5">
            <select id="addition-purpose" name="" class="form-select w-full lg:w-56">
                <option value="0"></option>
                @foreach(\App\Modules\Order\Entity\Order\OrderAddition::PAYS as $code => $name)
                    <option value="{{ $code }}">{{ $name }}</option>
                @endforeach
            </select>
            {{ \App\Forms\Input::create('addition-amount', ['placeholder' => 'Стоимость', 'value' => 0, 'class' => 'ml-2 w-40'])->type('number')->group_text('₽', false)->show() }}
            {{ \App\Forms\Input::create('addition-comment', ['placeholder' => 'Примечание', 'class' => 'ml-2 w-1/3'])->show() }}
            <x-base.button id="add-addition" type="button" variant="primary" class="ml-3">Добавить услугу в документ
            </x-base.button>
        </div>

        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center">№ п/п</div>
            <div class="w-56 text-center">Услуга</div>
            <div class="w-40 text-center">Сумма</div>
            <div class="w-56 text-center">Примечание</div>
            <div class="w-20 text-center">-</div>
        </div>
        <div id="additions_list"></div>
        <div class="box flex items-center font-semibold p-2">
            <div class="w-20 text-center"></div>
            <div class="w-56 text-center">ИТОГО</div>
            <div class="w-40 text-center">
                <div class="w-40 input-group">
                    <input id="additions-amount" type="number" class="form-control text-right" value="" aria-describedby="input-preorder-amount" readonly>
                    <div id="input-preorder-amount" class="input-group-text">₽</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let buttonAddAddition = document.getElementById('add-addition');

    buttonAddAddition.addEventListener('click', function () {
        let selectPurpose = document.getElementById('addition-purpose');
        let purpose = selectPurpose.value;
        let amount = document.getElementById('input-addition-amount').value;
        let comment = document.getElementById('input-addition-comment').value;
        let name = selectPurpose.options[selectPurpose.selectedIndex].text;
        //Очищаем поля
        selectPurpose.selectedIndex = 0;
        document.getElementById('input-addition-amount').value = 0;
        document.getElementById('input-addition-comment').value = '';
        orderAdditions.push({
            purpose: purpose,
            name: name,
            amount: Number(amount),
            comment: comment
        });

        updateAdditons();
        updateAmount();
    });

    function updateAdditons() {
        let pointAdditions = document.getElementById('additions_list');
        pointAdditions.innerHTML = '';
        for (let i = 0; i < orderAdditions.length; i++) {
            let _line = getLineAddition(i, orderAdditions[i]);
            pointAdditions.insertAdjacentHTML('beforeend', _line);
        }
        let buttonsRemoveAddition = document.querySelectorAll('.addition-remove');
        let inputsAmountAddition = document.querySelectorAll('.addition-amount');

        Array.from(buttonsRemoveAddition).forEach(function (buttonRemoveAddition) {
            buttonRemoveAddition.addEventListener('click', function () {
                let _num = Number(buttonRemoveAddition.getAttribute('data-num'));
                orderAdditions.splice(_num, 1);
                updateAdditons();
                updateAmount();
            })
        });
        Array.from(inputsAmountAddition).forEach(function (inputAmountAddition) {
            inputAmountAddition.addEventListener('change', function () {
                let _num = Number(inputAmountAddition.getAttribute('data-num'));
                orderAdditions[_num].amount = Number(inputAmountAddition.value);
                updateAmount();
            })
        });
    }

    function getLineAddition(i, addition) {
        let result = '' +
            '<div class="box flex items-center px-2" data-id="">' +
            '<div class="w-20">' + (i + 1) +'</div>'+
            '<div class="w-56">' + addition.name + '</div>'+
            '<div class="w-40 input-group">'+
            '<input id="" type="number" class="form-control text-right addition-amount"'+
            'value="' + addition.amount + '" aria-describedby="input-currency" min="0" data-num = "' + i + '">'+
            '<div id="input-currency" class="input-group-text">₽</div>'+
            '</div>'+
            '<div class="w-56 pl-2">' + addition.comment + '</div>'+

            '<button class="btn btn-outline-danger ml-6 addition-remove"'+
            'data-num = "' + i + '" type="button">'+
            'X'+
            '</button>'+
            ' </div>';
        return result;
    }
</script>
