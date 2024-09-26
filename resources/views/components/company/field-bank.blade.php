<div class="bank-detail">
    <x-infoBlock title="Банковские реквизиты">
        <div class="mt-3">
            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('bik', ['placeholder' => 'БИК', 'class' => '',
                        'value' => !is_null($company) ? $company->bik : ''])
                        ->label('БИК')->show() }}
                    {{ \App\Forms\Input::create('bank_name', ['placeholder' => 'Название банка', 'class' => 'mt-3',
                        'value' => !is_null($company) ? $company->bank_name : ''])
                        ->label('Название банка')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('corr_account', ['placeholder' => 'Корр/счет', 'class' => '',
                        'value' => !is_null($company) ? $company->corr_account : ''])
                        ->label('Корр/счет')->show() }}
                    {{ \App\Forms\Input::create('pay_account', ['placeholder' => 'Расчетный счет', 'class' => 'mt-3',
                        'value' => !is_null($company) ? $company->pay_account : ''])
                        ->label('Расчетный счет')->show() }}
                </div>
            </div>
        </div>
    </x-infoBlock>
</div>
