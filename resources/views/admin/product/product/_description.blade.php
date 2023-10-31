<x-base.classic-editor name="description">
    @if(isset($product))
        {{ $product->description}}
    @endif
</x-base.classic-editor>

<div class="grid grid-cols-12 gap-x-6 mt-5">
    <div class="col-span-12 lg:col-span-8">
        <x-base.form-label for="short-description">Краткое описание</x-base.form-label>
        <x-base.classic-editor id="short-description" name="short">
            @if(isset($product))
                {{ $product->short}}
            @endif
        </x-base.classic-editor>
    </div>
    <div class="hidden lg:col-span-4 lg:block mt-6">
        Текст описания товара можно форматировать.<br>
        Исключите возможность добавления внешних ссылок, т.к. некачественные ссылки снизят ранжирование вашего сайта.<br>
        Текст должен быть описательным и написан "для людей", а не для SEO. Обилие ключевых слов и рекламного характера воспринимается поисковиками как спам.
    </div>


</div>
<!-- Выбрать метку -->
<x-base.form-label for="select-tag" class="mt-3">Метки</x-base.form-label>
<x-base.tom-select id="select-tag" name="tags[]" class="w-full" data-placeholder="Выберите или напишите свои метки" multiple>
    <option value="0"></option>
    @foreach($tags as $tag)
        <option value="{{ $tag->id }}"
        @if(isset($product))
            {{ $product->isTag($tag->id) ? 'selected' : ''}}
            @endif
        >{{ $tag->name }}</option>
    @endforeach
</x-base.tom-select>
