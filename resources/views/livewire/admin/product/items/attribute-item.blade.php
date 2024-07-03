<div>
    <div class="relative pl-5 pr-5 xl:pr-10 py-10 bg-slate-50 rounded-md mt-3 attributes-component" wire:ignore>
        <button type="button" wire:click="remove" title="Удалить атрибут"
           class="text-slate-300 absolute top-0 right-0 mr-4 mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x"
                 class="lucide lucide-x stroke-1.5 h-4 w-4">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
        <h2>{{ $attribute->group->name . ' > ' . $attribute->name}}</h2>
        @if($attribute->isVariant())
            <select id="select-variant-{{ $attribute->id }}" class="w-full variant-tom-select new-tom-select bg-white tom-select"
                    data-placeholder="Выберите значение(я)" {{ $attribute->multiple ? 'multiple' : ''}}
                    wire:model="_value" wire:change="save" wire:loading.attr="disabled"
            >
                <option value="0"></option>
                @foreach($attribute->variants as $variant)
                    @if(is_null($value))
                        <option value="{{ $variant->id }}">{{ $variant->name }}</option>
                    @else
                        <option value="{{ $variant->id }}" {{ in_array($variant->id, $value) ? 'selected' : '' }}>{{ $variant->name }}</option>
                    @endif
                @endforeach
            </select>
        @else
            <div class="input-form">
                @if ($attribute->isBool())
                    <div class="form-check form-switch mt-3">
                        <input id="checkbox_{{ $attribute->id }}" class="form-check-input" type="checkbox"
                               wire:model="_value" wire:change="save" wire:loading.attr="disabled">
                        <label class="form-check-label" for="checkbox_{{ $attribute->id }}">{{ $attribute->name }}</label>
                    </div>
                @else
                    <input type="text" class="form-control" placeholder="Значение"
                           wire:model="_value" wire:change="save" wire:loading.attr="disabled">
                @endif
            </div>
        @endif
    </div>
</div>
@script
<script>
    let _select = document.getElementById('select-variant-{{ $attribute->id }}');

    if (_select !== null) {
        let options = {
            plugins: {
                dropdown_input: {},
            },
        };
        if (_select.getAttribute("placeholder")) {
            options.placeholder = _select.getAttribute("placeholder");
        }
        if (_select.getAttribute("multiple") !== null) {
            options = {
                ...options,
                plugins: {
                    ...options.plugins,
                    remove_button: {
                        title: "Удалить элемент",
                    },
                },
                persist: false,
                create: false,
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
        if (_select.tomselect === undefined) new TomSelect(_select, options);
    }
</script>
@endscript
