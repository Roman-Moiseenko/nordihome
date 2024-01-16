@extends('layouts.side-menu')

@section('subcontent')
    <script>
        let controlTomSelect;

        function _callback() {
            controlTomSelect = document.getElementById('select-attributes').tomselect;

            let modificationInput = document.getElementById('modification-product');
            let nameModificationInput = document.getElementById('name-modification');
            let hiddenIdInput = document.getElementById('hidden-id');
            nameModificationInput.value = modificationInput.value;
            let _params = '_token=' + '{{ csrf_token() }}';// + '&ids=' + '&product_id=' + hiddenIdInput.value;
            let request = new XMLHttpRequest();
            request.open('POST', '/admin/product/' + hiddenIdInput.value + '/attr-modification');
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    controlTomSelect.clearOptions();
                    JSON.parse(request.responseText).forEach(function (item) {
                        controlTomSelect.addOption({value: item.id, text: item.name})
                    });
                }
            };
        }
    </script>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создать группу модификаций
        </h2>
    </div>

    <div class="box mt-3 p-5">
        <form method="POST" action="{{ route('admin.product.modification.store') }}">
            @csrf
            <div class="w-1/2 lg:w-1/3">
                <x-base.form-label for="modification-product" class="inline-block mb-2 mt-3">Выберите товар
                </x-base.form-label>
                <x-searchProduct route="{{ route('admin.product.modification.search', ['action' => 'create']) }}"
                                 input-data="modification-product" hidden-id="product_id"
                                 class="w-52" callback="_callback()"/>
                <div class="mt-5">
                    <x-base.form-label for="name-modification" class="inline-block mb-2 mt-3">Название модификации
                    </x-base.form-label>
                    <input id="name-modification" type="text" class="form-control mt-1" value="" name="name">
                </div>
                <x-base.form-label for="select-attributes" class="mt-3">Выберите атрибуты</x-base.form-label>
                <x-base.tom-select id="select-attributes" name="attribute_id[]" class="w-full"
                                   data-placeholder="" multiple>
                    <option value="0"></option>
                </x-base.tom-select>
                <button class="btn btn-primary shadow-md mt-6">Сохранить</button>
            </div>
        </form>
    </div>
@endsection
