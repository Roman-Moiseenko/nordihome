<div class="bank-detail">
    <x-infoBlock :title="$title" :route="$route">
        <div class="mt-3">
            <div class="grid grid-cols-4 gap-x-3">
                <div>
                    <div class="">
                       <span class="mr-2 p-1 text-primary">ИНН:</span>
                        <span class="font-medium">{{ $company->inn }}</span>
                    </div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary">КПП:</span>
                        <span class="font-medium">{{ $company->kpp }}</span>
                    </div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary">ОГРН:</span>
                        <span class="font-medium">{{ $company->ogrn }}</span>
                    </div>
                </div>
                <div>
                    <div class="">
                        <span class="font-medium">{{ $company->full_name }}</span>
                    </div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary">Регион:</span>
                        <span class="font-medium">{{ $company->legal_address->region }}</span>
                    </div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary"></span>
                        <span class="font-medium"></span>
                    </div>
                </div>
                <div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary">email:</span>
                        <span class="font-medium">{{ $company->email }}</span>
                    </div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary">Телефон:</span>
                        <span class="font-medium">{{ $company->phone }}</span>
                    </div>
                    <div class="">
                        <span class="mr-2 p-1 text-primary">Руководитель:</span>
                        <span class="font-medium">{{ $company->chief->getFullName() }}</span>
                    </div>
                </div>
                <div>
                    @if($company->isHolding())
                        <h2 class="font-medium">Холдинг "{{ $company->holding->name  }}"</h2>
                    <ul>
                        @foreach($company->holding->organizations as $organization)
                            <li>
                                {{ $organization->short_name . ' (' . $organization->inn . ')' }}
                            </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="">
            <span class="font-medium">Контакты</span>
            <div class="grid grid-cols-3 gap-x-3">
            @foreach($company->contacts as $contact)
                <div>
                    <span>{{ $contact->fullname->getFullName() }}</span>
                    <span class="text-primary">{{ phone($contact->phone) }}</span>
                    <br>
                    <span><em>{{ $contact->post }}</em></span>
                    <span>{{ $contact->email }}</span>
                    <button type="button" class="ml-1 btn-outline-primary edit-modal-contact"
                            data-tw-toggle="modal" data-tw-target="#modal-create-contact"
                            data-contact="{{ json_encode($contact) }}"
                            data-route="{{ route('admin.accounting.organization.set-contact', $contact) }}"
                    >
                        <x-base.lucide class="h-4 w-4" icon="pencil"/>
                    </button>
                    <button type="button" class="ml-1 btn-outline-danger" onclick="document.getElementById('del-contact-{{ $contact->id }}').submit();">
                        <x-base.lucide class="h-4 w-4" icon="trash"/>
                    </button>
                    <form id="del-contact-{{ $contact->id }}" action="{{ route('admin.accounting.organization.del-contact', $contact) }}" method="post">
                        @csrf
                    </form>
                </div>
            @endforeach
            </div>
            <button id="add-modal-contact" type="button"
                    class="btn btn-sm btn-outline-primary"
                    data-tw-toggle="modal" data-tw-target="#modal-create-contact"
                    data-route="">
                Добавить
            </button>
        </div>
    </x-infoBlock>

    <x-base.dialog id="modal-create-contact" staticBackdrop>
        <x-base.dialog.panel>
            <form id="modal-contact" action="/" method="POST">
                @csrf
                <x-base.dialog.title>
                    <h2 id="title-modal" class="mr-auto text-base font-medium">Добавить контакт</h2>
                </x-base.dialog.title>
                <x-base.dialog.description class="grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <x-base.form-input id="input-phone" class="input-search-user mask-phone" type="text" name="phone" placeholder="8 (___) ___-__-__" required />
                    </div>
                    <div class="col-span-12">
                        <x-base.form-input id="input-email" class="input-search-user mask-email" type="text" name="email" placeholder="example@gmail.com" />
                    </div>
                    <div class="col-span-12">
                        <x-base.form-input id="input-post" type="text" name="post" placeholder="Должность"/>
                    </div>
                    <div class="col-span-12 flex">
                        <x-base.form-input id="input-fullname[surname]" type="text" name="fullname[surname]" placeholder="Фамилия"/>
                        <x-base.form-input id="input-fullname[firstname]" type="text" name="fullname[firstname]" placeholder="Имя"/>
                        <x-base.form-input id="input-fullname[secondname]" type="text" name="fullname[secondname]" placeholder="Отчество"/>
                    </div>
                </x-base.dialog.description>

                <x-base.dialog.footer>
                    <x-base.button id="modal-cancel" class="mr-1 w-24" data-tw-dismiss="modal" type="button" variant="outline-secondary">Отмена</x-base.button>
                    <x-base.button class="w-24" type="submit" variant="primary">Сохранить</x-base.button>
                </x-base.dialog.footer>
            </form>
        </x-base.dialog.panel>
    </x-base.dialog>
    @once
        @push('scripts')
            @vite('resources/js/components/company/info.js')
        @endpush
    @endonce
</div>
