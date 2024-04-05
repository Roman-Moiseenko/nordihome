<div class="flex flex-col lg:justify-start buttons-block items-start">
    <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="primary" class="w-100">Назначить
            ответственного
            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
        </x-base.popover.button>
        <x-base.popover.panel>
            <form action="{{ route('admin.sales.order.set-manager', $order) }}" METHOD="POST">
                @csrf
                <div class="p-2">
                    <x-base.tom-select id="select-staff" name="staff_id" class=""
                                       data-placeholder="Выберите Менеджера">
                        <option value="0"></option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}"
                            >{{ $staff->fullname->getShortName() }}</option>
                        @endforeach
                    </x-base.tom-select>
                    <div class="flex items-center mt-3">
                        <x-base.button id="close-add-group" class="w-32 ml-auto"
                                       data-tw-dismiss="dropdown" variant="secondary" type="button">
                            Отмена
                        </x-base.button>
                        <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                            Сохранить
                        </x-base.button>
                    </div>
                </div>
            </form>
        </x-base.popover.panel>
    </x-base.popover>
    <button class="btn btn-secondary mt-2">Удалить</button>
</div>
