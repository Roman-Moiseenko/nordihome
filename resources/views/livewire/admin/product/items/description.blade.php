<div>
    <div wire:ignore>
        <textarea class="form-control" id="livewire-description"
                  wire:model.live.debounce.250ms="description" wire:change="save" wire:loading.attr="disabled">
            {!! $description !!}
        </textarea>
    </div>

    <div class="grid grid-cols-12 gap-x-6 mt-5">
        <div class="col-span-12 lg:col-span-8" wire:ignore>
            <x-base.form-label for="short-description">Краткое описание</x-base.form-label>
            <textarea class="form-control"  id="livewire-short"
                      wire:model.live.debounce.250ms="short" wire:change="save" wire:loading.attr="disabled">
                {!! $short !!}
            </textarea>

        </div>
        <div class="hidden lg:col-span-4 lg:block mt-6">
            Текст описания товара можно форматировать.<br>
            Исключите возможность добавления внешних ссылок, т.к. некачественные ссылки снизят ранжирование вашего сайта.<br>
            Текст должен быть описательным и написан "для людей", а не для SEO. Обилие ключевых слов и рекламного характера воспринимается поисковиками как спам.
        </div>


    </div>
    <!-- Выбрать метку -->
    <div wire:ignore>
    <x-base.form-label for="select-tag" class="mt-3">Метки</x-base.form-label>
    <x-base.tom-select id="select-tag"  class="w-full" data-placeholder="Выберите или напишите свои метки" multiple wire:model="_tags" wire:change="save">
        <option value="0"></option>
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}"
            @if(isset($product))
                {{ $product->isTag($tag->id) ? 'selected' : ''}}
                @endif
            >{{ $tag->name }}</option>
        @endforeach
    </x-base.tom-select>
    </div>


    @once
        @push('vendors')
            @vite('resources/js/vendor/ckeditor/classic/index.js')
        @endpush
    @endonce

    @once
        @push('scripts')
            @vite('resources/js/components/classic-editor/index.js')
        @endpush
    @endonce
</div>
@script
<script>
    ClassicEditor.create(document.querySelector('#livewire-description'), {
        htmlEmbed: {
            showPreviews: true
        }
    })
        .then(editor => {
            editor.model.document.on('change:data', () => {
            @this.set('description', editor.getData());
            });
            editor.model.document.on('', () => {

            });
        })
        .catch(error => {
            console.error(error);
        });
    ClassicEditor.create(document.querySelector('#livewire-short'), {
        htmlEmbed: {
            showPreviews: true
        }
    })
        .then(editor => {
            editor.model.document.on('change:data', () => {
            @this.set('short', editor.getData());
            });
            editor.model.document.on('', () => {

            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endscript
