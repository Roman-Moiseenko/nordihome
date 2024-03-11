<form method="post" action="{{ route('admin.sales.order.set-payment', $order) }}">
    @csrf
    <x-base.table.th class="whitespace-nowrap"> {{ now()->format('d-M') }}</x-base.table.th>
    <x-base.table.th class="whitespace-nowrap">
        <x-base.form-input name="payment-amount" data-id="" class="" type="number" value=""
                           min="0" placeholder=""/>
    </x-base.table.th>
    <x-base.table.th class="whitespace-nowrap">

        <x-base.tom-select id="select-payment-class" name="payment-class" class=""
                           data-placeholder="Выберите Способ оплаты">
            <option value="0"></option>
            @foreach(\App\Modules\Order\Helpers\PaymentHelper::payments() as $payment)
                <option value="{{ $payment['class'] }}"
                >{{ $payment['name'] }}</option>
            @endforeach
        </x-base.tom-select>

    </x-base.table.th>
    <x-base.table.th class="whitespace-nowrap">
        <x-base.tom-select id="select-payment-purpose" name="payment-purpose" class=""
                           data-placeholder="Выберите Способ оплаты">
            <option value="0"></option>
            @foreach(\App\Modules\Order\Entity\Payment\PaymentOrder::PAYS as $code => $name)
                <option value="{{ $code }}"
                >{{ $name }}</option>
            @endforeach
        </x-base.tom-select>
    </x-base.table.th>

    <x-base.table.th class="text-center whitespace-nowrap">
        <x-base.form-input name="payment-comment" data-id="" class="" type="text" value=""
                           min="0" placeholder=""/>
    </x-base.table.th>
    <x-base.table.th class="text-center whitespace-nowrap"></x-base.table.th>
    <x-base.table.th class="text-center whitespace-nowrap">
        <button type="submit" class="btn btn-primary">Save</button>
    </x-base.table.th>
</form>
