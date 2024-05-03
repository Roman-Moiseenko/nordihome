<div class="box-card">
    <div>Способы оплаты</div>
    <div id="slider-payment" class="owl-carousel owl-theme">
        @foreach($payments as $sort => $payment)
            <div class="card-payment">
                <label class="radio-img">
                    <input type="radio" name="payment" data-state="change" value="{{ $payment['class'] }}"
                           data-sort="{{ $sort }}"
                        {{ ($payment['class'] == $user->payment->class_payment) ? 'checked' : '' }}>
                    <img src="{{ $payment['image'] }}" alt="{{ $payment['name'] }}" title="{{ $payment['name'] }}">
                </label>
            </div>
        @endforeach
    </div>
    <div id="invoice-data" {!! $user->payment->isInvoice() ? '' : ' style="display: none"' !!}>
        <div {!! $user->payment->invoice() != '' ? '' : ' style="display: none"' !!}>
            <span class="address-delivery--title"></span>
            <span class="address-delivery--info"> {{ $user->payment->invoice() }} </span>
            <span class="address-delivery--change" for="d---0">Изменить</span>
            <input type="hidden" name="inn" id="input-inn-hidden">
        </div>
        <div class="input-group" id="d---0" {!! $user->payment->invoice() == '' ? '' : ' style="display: none"' !!}>
            <input type="text" class="form-control" id="input-inn" aria-describedby="emailHelp" placeholder="Введите ИНН">
            <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-inn" to="input-inn-hidden">Сохранить</button>
        </div>
    </div>
</div>
