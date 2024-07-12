<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12">
            @if($order->isManager())
                <form wire:submit="add_addition">
                    @csrf
                    <div class="mx-3 flex w-full mb-5">

                        <select id="addition-purpose" name="purpose" class="form-select w-full lg:w-56"
                                wire:model="form_purpose">
                            <option value="0"></option>
                            @foreach(\App\Modules\Order\Entity\Order\OrderAddition::PAYS as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="input-group ml-2 w-40 ">
                            <input id="input-amount" type="number" name="amount" class="form-control"
                                   placeholder="Стоимость" value="0" required="" min="0"
                                   wire:model="form_amount">
                            <div class="input-group-text">₽</div>


                        </div>
                        <div class="input-form ml-2 w-1/4 ">
                            <input id="input-comment" type="text" name="comment" class="form-control "
                                   placeholder="Примечание" value=""
                                   wire:model="form_comment"
                            >
                        </div>
                        <button id="add-addition" type="submit"
                                class="transition duration-200 border shadow-sm inline-flex items-center justify-center py-2 px-3
                                 rounded-md font-medium cursor-pointer focus:ring-4 focus:ring-primary focus:ring-opacity-20 focus-visible:outline-none
                                 [&amp;:hover:not(:disabled)]:bg-opacity-90 [&amp;:hover:not(:disabled)]:border-opacity-90
                                 [&amp;:not(button)]:text-center disabled:opacity-70 disabled:cursor-not-allowed bg-primary border-primary text-white  ml-3">
                            Добавить услугу в документ
                        </button>

                    </div>
                </form>
            @endif

            <div class="box flex items-center font-semibold p-2">
                <div class="w-20 text-center">№ п/п</div>
                <div class="w-56 text-center">Услуга</div>
                <div class="w-40 text-center">Сумма</div>
                <div class="w-56 text-center">Примечание</div>
                <div class="w-20 text-center">-</div>
            </div>

            @foreach($order->additions as $i => $addition)
                <livewire:admin.order.manager.addition :addition="$addition" :key="$addition->id" :i="$i"/>
            @endforeach

            <div class="box flex items-center font-semibold p-2">
                <div class="w-20 text-center"></div>
                <div class="w-56 text-center">ИТОГО</div>
                <div class="w-40 text-center">
                    <div class="w-40 input-group">
                        <input id="additions-amount" type="number" class="form-control text-right"
                               aria-describedby="input-preorder-amount" readonly
                               wire:model="amount"
                        >
                        <div id="input-preorder-amount" class="input-group-text">₽</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
