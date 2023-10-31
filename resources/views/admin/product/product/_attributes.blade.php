<script>
    //Находим Главную категорию
    let _selMainCat = document.getElementById('select-category');
    let _selCats = document.getElementById('select-categories');

    let counter_attribute = 0;
    const _name_point_attribute = 'point-insert-attribute';


    _selMainCat.addEventListener('change', function (e) {
        updateListAttribute();
        document.getElementById('block-list-attributes').innerHTML = '';
    });
    _selCats.addEventListener('change', function (e) {
        updateListAttribute();
        document.getElementById('block-list-attributes').innerHTML = '';
    });


    function updateListAttribute() { //Обновляет список атрибутов в SELECT при выборе категорий и 1 раз при запуске
        let _selAttr = document.getElementById('select-attribute');
        let listIdCat = []; //Список id Категория
        listIdCat.push(Number(_selMainCat.value));
        for (let i = 0; i < _selCats.options.length; i++) {
            if (_selCats.options[i].selected) {
                listIdCat.push(Number(_selCats.options[i].value));
            }
        }
        //AJAX
        let _params = '_token=' + '{{ csrf_token() }}' + '&ids=' + JSON.stringify(listIdCat);
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/category/json_attributes');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            // Проверяем, был ли запрос успешным
            if (this.readyState === 4 && this.status === 200) {
                let _response = JSON.parse(request.responseText);
                _selAttr.options.length = 0; //Очищаем и заполняем SELECT атрибутами
                _selAttr.appendChild(new Option('', 0));
                for (let key in _response) {
                    let option = new Option(_response[key].attribute, _response[key].id);
                    option.setAttribute('group', _response[key].group);
                    _selAttr.appendChild(option);
                    //_selAttr.options[_selAttr.options.length] = new Option(_response[key].attribute, _response[key].id);
                }
            }
        };
    }

    function _set_point_attribute() {
        let _html = '<div id="' + _name_point_attribute + '"></div>';
        document.write(_html);
    }


    function AddBlock_Attribute() {

        let el_Insert = document.getElementById(_name_point_attribute);
        let _selAttr = document.getElementById('select-attribute');
        if (_selAttr.value === '0') return;
        //Получаем ID и Name из select, и добавляем в  _block_HTML


       // let _block_HTML = '<div>' + _selAttr.value + _selAttr.options[_selAttr.selectedIndex].text + '</div>';

        let _block_HTML = '<div id="attribute-' + counter_attribute + '" class="relative pl-5 pr-5 xl:pr-10 py-10 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3">' +
            '<a id="delete-attribute-' + counter_attribute + '" for="attribute-' + counter_attribute + '" href="" class="text-slate-300 absolute top-0 right-0 mr-4 mt-4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x" class="lucide lucide-x stroke-1.5 h-4 w-4"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></a>' +
            '<h2>' + _selAttr.options[_selAttr.selectedIndex].getAttribute('group') + ' > ' + _selAttr.options[_selAttr.selectedIndex].text +'</h2>' +
            '<input type="hidden" name="attribute.id[]" value="' + _selAttr.value + '">' +
            '<div class="input-form">' +
            '<input type="text" name="attribute.value[]" class="form-control " placeholder="Значение" value="' +  '">' +
            '</div>' +
            '</div>';
        _selAttr.options[_selAttr.selectedIndex] = null;
        el_Insert.insertAdjacentHTML('beforebegin', _block_HTML);
        let new_el = document.getElementById('delete-attribute-' + counter_attribute);
        counter_attribute++;
        new_el.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById(new_el.getAttribute('for')).remove();
        });
    }
</script>

<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        <select id="select-attribute" name="" class="w-full form-select" data-placeholder="Выберите атрибут">
            <option value="0"></option>
        </select>
        <x-base.button class="w-full mt-4" variant="primary" type="button" onclick="AddBlock_Attribute()">
            <x-base.lucide class="mr-2" icon="blocks"/>
            Добавить Атрибут
        </x-base.button>
        <div class="w-full text-slate-400 mt-6">
            При выборе главной и вторичных категорий список атрибутов будет очищаться.
        </div>
    </div>
    <div class="col-span-12 lg:col-span-8">
        <script>
            _set_point_attribute(); //Устанавливаем точку добавления блоков
            updateListAttribute();  //Обновляем атрибуты
        </script>
        <div class="block-list-attributes">
        @foreach($product->prod_attributes as $prod_attribute)

        @endforeach
        </div>
    </div>
</div>
<script>


</script>


