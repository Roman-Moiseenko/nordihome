<script>
    let inputDataBonus, buttonAddBonus, listBonus;

    function initBonus() {
        inputDataBonus = document.getElementById('bonus-product');
        buttonAddBonus = document.getElementById('add-bonus');
        listBonus = document.getElementById('list-bonus');
        buttonAddBonus.addEventListener('click', function () {
            let inputBonus = document.querySelectorAll('.input-bonus');
            let isBonus = false;
            if (inputBonus.length !== 0) inputBonus.forEach(function (item) {
                if (inputDataBonus.getAttribute('data-id') === item.value) isBonus = true;
            });
            if (inputDataBonus.value.length > 0 && !isBonus) {
                _insertBlockBonus(inputDataBonus.getAttribute('data-id'), inputDataBonus.getAttribute('data-name'),
                    inputDataBonus.getAttribute('data-img'), inputDataBonus.getAttribute('data-code'), inputDataBonus.getAttribute('data-price'));
                inputDataBonus.value = '';
            }
        });
    }

    function _insertBlockBonus(_id, _name, _img, _code, _price, _discount = '') {
        listBonus.insertAdjacentHTML('afterbegin', _itemBonus(_id, _name, _img, _code, _price, _discount));
        let new_el = document.getElementById('delete-bonus-' + _id); //Удалить блок
        new_el.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById(new_el.getAttribute('for')).remove();
        });
    }
    function _itemBonus(_id, _name, _img, _code, _price, _discount) {
        return ''+
            '<div id="bonus-'+_id+'" class="relative pt-5 pb-1 py-3 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3">' +
            '<a id="delete-bonus-'+_id+'" for="bonus-'+_id+'" data-id="'+_id+'" href="" class="text-slate-300 absolute top-0 right-0 mr-4 mt-4">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x"class="lucide lucide-x stroke-1.5 h-4 w-4"> <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></a>' +
            '<div class="flex justify-between items-center m-3">' +
            '<div class="flex items-center">' +
            '<div class="image-fit w-10 h-10"><img class="rounded-full" src="' + _img + '" alt="Светильники"></div>'+
            '<div class="text-left ml-3">' + _name + '(' + _code + ')' + '</div>' +
            '</div>' +
            '<div class="text-right font-medium ml-auto mr-3 text-danger"><s>' + _price + ' ₽</s></div>' +

            '<div><input class="form-control form-input w-40 ml-3" type="text" placeholder="Новая цена" name=discount[] value="' + _discount + '"> ₽ </div>'+
            '</div>'+

            '<input class="input-bonus" type="hidden" name=bonus[] value="' + _id + '">'+
            '</div>';
    }

</script>
<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        <x-searchproduct route="{{ route('admin.product.search') }}" input-data="bonus-product"/>
        <x-base.button id="add-bonus" type="button" variant="primary" class="mt-3">Добавить Бонус</x-base.button>
        <div class="w-full text-slate-400 mt-6">

        </div>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <h3 class="font-medium text-center">Товары с бонусной продажей</h3>
        <div id="list-bonus">
            <script>initBonus();</script>
            @foreach($product->bonus as $bonus)
                <script>
                    _insertBlockBonus(
                        "{{ $bonus->id }}", "{{ $bonus->name }}",
                        "{{ $bonus->getImage() }}", "{{ $bonus->code }}",
                        "{{ $bonus->getlastPrice() }}", "{{ $bonus->pivot->discount }}"
                    );
                </script>
            @endforeach
        </div>
    </div>
</div>
