<x-base.classic-editor name="description">
    <p>Content of the editor.</p>
</x-base.classic-editor>

<div class="grid grid-cols-12 gap-x-6 mt-5">
    <div class="col-span-12 lg:col-span-8">
        <x-base.inline-editor name="short">
            <p>Content of the editor.</p>
        </x-base.inline-editor>
    </div>
    <div class="hidden lg:col-span-4 lg:block">
        Текст помощи
    </div>


</div>
<!-- Выбрать метку -->
<x-base.form-label for="select-tag" class="mt-3">Метки</x-base.form-label>
<x-base.tom-select id="select-tag" name="tags[]" class="w-full" data-placeholder="Выберите или напишите свои метки" multiple>
    <option value="0"></option>
    @foreach($tags as $tag)
        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
    @endforeach
</x-base.tom-select>
