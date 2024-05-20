<x-base.table.tr>
    <x-base.table.td class="w-52">

        <a href="{{ route('admin.sales.expense.show', $expense) }}" class="font-medium text-success"> {{ $expense->htmlNum() . ' от ' . $expense->htmlDate() }}  </a>

    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $expense->address->address }}</x-base.table.td>
    <x-base.table.td class="text-center"> {{ $expense->statusHtml() }} </x-base.table.td>

    <x-base.table.td class="table-report__action">
        <div class="flex justify-center items-center">
            @if($expense->isAssembly())

                <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
                    <x-base.popover.button as="x-base.button" variant="warning" class="w-100">
                        Назначить сборщика
                        <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                    </x-base.popover.button>
                    <x-base.popover.panel>
                        <form method="post" action="{{ route('admin.delivery.assembling', $expense) }}">
                            @csrf
                            <div class="p-2">
                                <x-base.tom-select name="worker_id" class=""
                                                   data-placeholder="Выберите работника">
                                    <option value="0"></option>
                                    @foreach($workers as $worker)
                                        @if($worker->storage_id == $expense->storage_id)
                                            <option
                                                value="{{ $worker->id }}">{{ $worker->fullname->getShortName() }}</option>
                                        @endif
                                    @endforeach
                                </x-base.tom-select>

                                <div class="flex items-center mt-3">
                                    <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown"
                                                   variant="secondary" type="button">
                                        Отмена
                                    </x-base.button>
                                    <button class="w-32 ml-2 btn btn-primary" type="submit">
                                        Назначить
                                    </button>
                                </div>
                            </div>
                        </form>
                    </x-base.popover.panel>
                </x-base.popover>


            @endif
            @if($expense->isAssembling())
                @if($expense->isLocal())
                    <form method="post" action="{{ route('admin.delivery.delivery', $expense) }}">
                        @csrf
                        <button class="btn btn-success" type="submit">На доставке</button>
                    </form>
                @endif
                @if($expense->isRegion())
                    <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
                        <x-base.popover.button as="x-base.button" type="button" variant="success" class="w-100">
                            На доставке
                            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                        </x-base.popover.button>
                        <x-base.popover.panel>
                            <form method="post" action="{{ route('admin.delivery.delivery', $expense) }}">
                                @csrf
                                <div class="p-2">

                                    <input class="form-control" name="track" placeholder="Трек-номер">

                                    <div class="flex items-center mt-3">
                                        <x-base.button id="close-add-group" class="w-32 ml-auto"
                                                       data-tw-dismiss="dropdown" variant="secondary" type="button">
                                            Отмена
                                        </x-base.button>
                                        <button class="w-32 ml-2 btn btn-primary" type="submit">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </x-base.popover.panel>
                    </x-base.popover>
                    @endif
                    <button class="btn btn-primary-soft ml-4"
                            onclick="document.getElementById('expense-completed-{{ $expense->id }}').submit();">Завершен
                    </button>
                @endif

                @if($expense->isDelivery())
                    <button class="btn btn-primary-soft"
                            onclick="document.getElementById('expense-completed-{{ $expense->id }}').submit();">Завершен
                    </button>
                @endif

                <form id="expense-completed-{{ $expense->id }}" method="post"
                      action="{{ route('admin.delivery.completed', $expense) }}">
                    @csrf
                </form>

        </div>
    </x-base.table.td>
</x-base.table.tr>
