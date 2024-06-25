<div>
    <div>
        {{ $comment }}
    </div>
    <div wire:ignore class="flex">
        <select id="select" class="tom-select form-control w-100" >
            <option value="0"></option>
        </select>
        {{ $routeAdd }}
        @if($quantity)
            <input class="form-control w-20" type="number" wire:model="_quantity">
        @endif
    </div>

    <script>
        let settings = {
            onChange:function (value) {
                //Выбор элемента
                console.log(value);
            },
            onType:function (str) {
                //Ввод с клавиатуры
                //console.log(str);

                Livewire.dispatch(
                    'search-product',
                    {search: str}
                );
            }
        };
        let select = new TomSelect('#select',settings);
        select.addOption({value:'test1', text: 'Тест'});
        select.addOption({value:'test2', text: 'Поиск'});

    </script>
    @script
    <script>
        $wire.on('update-tom-select', (el) => {
            //settings.refreshOptions();
            //settings.clearOptions();
            let items = JSON.parse(el.data);
            items.forEach(function (item) {
                select.addOption({value: item.id, text: item.code + ' ' + item.name});
            });
        });
    </script>
    @endscript
</div>


