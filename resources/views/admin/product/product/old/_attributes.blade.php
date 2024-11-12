<script>

    let _selMainCat = document.getElementById('select-category-component');
    let _selCats = document.getElementById('select-categories-component');
    let _ListAttributes = [];

    const _name_point_attribute = 'point-insert-attribute';

    _selMainCat.addEventListener('change', function (e) {
        updateListAttribute(false);
        clearBlocksAttribute();
    });
    _selCats.addEventListener('change', function (e) {
        updateListAttribute(false);
        clearBlocksAttribute();
    });

    function clearBlocksAttribute() {
        window.$("div[id^='attribute-']").each( function () {
            window.$(this).remove();
        });
    }

    function updateListAttribute(_preload = true) { //Обновляет список атрибутов в SELECT при выборе категорий и 1 раз при запуске
        let _selAttr = document.getElementById('select-attribute');
        let listIdCat = []; //Список id Категория
        listIdCat.push(Number(_selMainCat.value));
        for (let i = 0; i < _selCats.options.length; i++) {
            if (_selCats.options[i].selected) {
                listIdCat.push(Number(_selCats.options[i].value));
            }
        }
        //AJAX
        let _params = '_token=' + '{{ csrf_token() }}' + '&ids=' + JSON.stringify(listIdCat) + '&product_id=' + {{ $product->id }};
        let request = new XMLHttpRequest();
        request.open('POST', '/admin/product/category/json_attributes');
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(_params);
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                _ListAttributes = JSON.parse(request.responseText);
                _selAttr.options.length = 0; //Очищаем и заполняем SELECT атрибутами
                _selAttr.appendChild(new Option('', '0'));
                for (let key in _ListAttributes) {
                    _selAttr.appendChild(new Option(_ListAttributes[key].attribute, _ListAttributes[key].id));
                }
            } else {
                //console.log(request.responseText);
            }
            if (_preload) {//Только при первом запуске, при обновлениях категорий, не загружаем из базы
                setTimeout(function () {
                    for (let key in _ListAttributes) {
                        if (_ListAttributes[key].complete === true) {
                            AddBlock_Attribute(_ListAttributes[key].id);
                        }
                    }
                    return false;
                }, 2000)
            }
        };
    }

    function _set_point_attribute() {
        let _html = '<div id="' + _name_point_attribute + '"></div>';
        document.write(_html);
    }

    function AddBlock_Attribute(_value = '') {
        let el_Insert = document.getElementById(_name_point_attribute);
        let _selAttr = document.getElementById('select-attribute');
        let _selectIndex = null;
        let preload_tom_select = true;
        if (_value === '') {
            _value = _selAttr.value;
            _selectIndex = _selAttr.selectedIndex;
        } else {
            //Загрузка с базы preload_tom_select = false;
            for (let i = 0; i < _selAttr.options.length; i++) {
                if (_selAttr.options[i].value == _value && !_selAttr.options[i].classList.contains('hidden')) {
                    _selectIndex = i;
                }
            }
        }
        if (_selectIndex === null) return;

        if (_value === '0') return;
        el_Insert.insertAdjacentHTML('beforebegin', _ListAttributes[_value].block); //Вставляем отрендеренный блок
        let new_el = document.getElementById('delete-attribute-' + _value); //Удалить блок
        new_el.addEventListener('click', function (e) {
            e.preventDefault();
            //Возвращаем возможность выбора аттрибута в список
            let value = new_el.getAttribute('data-id');
            for (let i = 0; i < _selAttr.options.length; i++) {
                if (_selAttr.options[i].value === value) {
                    _selAttr.options[i].classList.remove("hidden");
                }
            }
            document.getElementById(new_el.getAttribute('for')).remove();
        });
        _selAttr.options[_selectIndex].classList.add("hidden");
        _selAttr.selectedIndex = 0;

        if (preload_tom_select) {
            let id_tom_select = _ListAttributes[_value].id_tom_select;//Если параметр Вариант, то загружаем tom-select
            if (id_tom_select !== null && id_tom_select !== undefined) _updateTomSelect(id_tom_select);
        }
    }

    function _updateTomSelect(id_tom_select) {
        window.$("#" + id_tom_select).each(function () {
            let options = {
                plugins: {
                    dropdown_input: {},
                },
            };

            if (window.$(this).data("placeholder")) {
                options.placeholder = window.$(this).data("placeholder");
            }

            if (window.$(this).attr("multiple") !== undefined) {
                options = {
                    ...options,
                    plugins: {
                        ...options.plugins,
                        remove_button: {
                            title: "Удалить элемент",
                        },
                    },
                    persist: false,
                    create: true,
                    onDelete: function (values) {
                        return confirm(
                            values.length > 1
                                ? "Вы уверены, что хотите удалить эти " +
                                values.length +
                                " элементы?"
                                : 'Будет удален элемент под id="' +
                                values[0] +
                                '"?'
                        );
                    },
                };
            }

            if (window.$(this).data("header")) {
                options = {
                    ...options,
                    plugins: {
                        ...options.plugins,
                        dropdown_header: {
                            title: window.$(this).data("header"),
                        },
                    },
                };
            }
            new window.TomSelect(this, options);
        });
    }
</script>

<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12 lg:col-span-4">
        <select id="select-attribute" name="" class="w-full form-select mt-3" data-placeholder="Выберите атрибут">
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
    </div>
</div>



