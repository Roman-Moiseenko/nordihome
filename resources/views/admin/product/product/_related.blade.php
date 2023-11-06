<script>
    function _itemRelated(_id, _name, _img, _code) {
        return ''+
            '<div id="related-'+_id+'" class="relative pt-5 pb-1 py-3 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3">' +
            '<a id="delete-related-'+_id+'" for="related-'+_id+'" data-id="'+_id+'" href="" class="text-slate-300 absolute top-0 right-0 mr-4 mt-4">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x"class="lucide lucide-x stroke-1.5 h-4 w-4"> <path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></a>' +
            '<div class="flex justify-between items-center m-3">' +
            '<div class="flex items-center">' +
            '<div class="image-fit w-10 h-10"><img class="rounded-full" src="' + _img + '" alt="Светильники"></div>'+
            '<div class="text-left ml-3">' + _name + '</div>' +
            '</div>' +
            '<div class="text-right font-medium">' + _code + '</div>' +
            '</div>'+
            '<input class="input-related" type="hidden" name=related[] value="' + _id + '">'+
            '</div>';
    }
</script>
<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        <x-searchproduct route="{{ route('admin.product.search') }}" input-data="related-product"/>
        <x-base.button id="add-related" type="button" variant="primary" class="mt-3">Добавить аксессуар</x-base.button>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <h3 class="font-medium text-center">Список сопутствующих</h3>
        <div id="list-related">
            @foreach($product->related as $related)
                <script>document.write(_itemRelated("{{ $related->id }}", "{{ $related->name }}", "{{ $related->getImage() }}", "{{ $related->code }}"));</script>
            @endforeach
        </div>
    </div>
</div>
<script>
    let inputData = document.getElementById('related-product');
    let buttonAddRelated = document.getElementById('add-related');
    let listRelated = document.getElementById('list-related');
    buttonAddRelated.addEventListener('click', function () {
        let inputRelated = document.querySelectorAll('.input-related');
        let isRelated = false;
        if (inputRelated.length !== 0) inputRelated.forEach(function (item) {
            if (inputData.getAttribute('data-id') === item.value) isRelated = true;
        });
        if (inputData.value.length > 0 && !isRelated) {
            listRelated.insertAdjacentHTML('afterbegin', _itemRelated(inputData.getAttribute('data-id'), inputData.getAttribute('data-name'),
                inputData.getAttribute('data-img'), inputData.getAttribute('data-code')));
            let new_el = document.getElementById('delete-related-' + inputData.getAttribute('data-id')); //Удалить блок
            new_el.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById(new_el.getAttribute('for')).remove();
            });
            inputData.value = '';
        }
    });


</script>
