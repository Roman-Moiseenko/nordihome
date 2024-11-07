@extends('layouts.side-menu')

@section('subcontent')
    <h2 class="text-lg font-medium mt-4">Клиенты</h2>
    <div class="grid grid-cols-12 gap-4">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
            <x-base.button
                data-tw-toggle="modal"
                data-tw-target="#modal-create-user"
                href="#"
                as="a"
                variant="primary"
            >
                Добавить клиента
            </x-base.button>

            {{ $users->links('admin.components.count-paginator') }}
            <!-- Фильтр -->
            <div class="ml-auto">
                <x-tableFilter :count="$filters['count'] ?? null">
                    <input class="form-control" name="name" placeholder="Клиент,Телефон,ИНН,Email"
                           value="{{ $filters['name'] ?? '' }}" autocomplete="off">
                    <input class="form-control mt-1" name="address" placeholder="Адрес"
                           value="{{ $filters['address'] ?? '' }}" autocomplete="off">
                </x-tableFilter>
            </div>
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap border-b-0">
                            Клиент
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center border-b-0">
                            Последний заказ
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">
                            Кол-во заказов
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-center">
                            Общая сумма
                        </x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap border-b-0 text-right">
                            Регион
                        </x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach ($users as $user)
                        @include('admin.user._list', ['item' => $user])

                    @endforeach
                </x-base.table.tbody>

            </x-base.table>
        </div>
    </div>

    {{ $users->links('admin.components.paginator') }}




    <x-base.dialog id="modal-create-user" staticBackdrop>
        <x-base.dialog.panel>
            <form id="modal-destroy-form" action="{{ route('admin.user.create') }}" method="POST">
                @csrf
                <x-base.dialog.title>
                    <h2 class="mr-auto text-base font-medium">Новый клиент</h2>
                </x-base.dialog.title>

                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <x-base.form-input id="input-id" type="hidden" name="user_id"/>
                    <div class="col-span-12">
                        <x-base.form-label for="input-phone">Телефон</x-base.form-label>
                        <x-base.form-input id="input-phone" class="input-search-user mask-phone" type="text" name="phone" placeholder="8 (___) ___-__-__" required />
                    </div>
                    <div class="col-span-12">
                        <x-base.form-label for="input-email">Почта</x-base.form-label>
                        <x-base.form-input id="input-email" class="input-search-user mask-email" type="text" name="email" placeholder="example@gmail.com" required />
                    </div>
                    <div class="col-span-12">
                        <x-base.form-label for="input-name">Клиент</x-base.form-label>
                        <div class="flex">
                            <x-base.form-input type="text" name="surname" placeholder="Фамилия"/>
                            <x-base.form-input type="text" name="firstname" placeholder="Имя"/>
                            <x-base.form-input type="text" name="secondname" placeholder="Отчество"/>
                        </div>
                    </div>
                </x-base.dialog.description>

                <x-base.dialog.footer>
                    <x-base.button id="modal-cancel" class="mr-1 w-24" data-tw-dismiss="modal" type="button" variant="outline-secondary">Отмена</x-base.button>
                    <x-base.button class="w-24" type="submit" variant="primary">Создать</x-base.button>
                </x-base.dialog.footer>
            </form>
        </x-base.dialog.panel>
    </x-base.dialog>
@endsection
