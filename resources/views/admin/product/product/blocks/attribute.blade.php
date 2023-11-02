<div id="attribute-{{ $attribute->id }}"
     class="relative pl-5 pr-5 xl:pr-10 py-10 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3">
    <a id="delete-attribute-{{ $attribute->id }}" for="attribute-{{ $attribute->id }}" data-id="{{ $attribute->id }}" href=""
       class="text-slate-300 absolute top-0 right-0 mr-4 mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x"
             class="lucide lucide-x stroke-1.5 h-4 w-4">
            <path d="M18 6 6 18"></path>
            <path d="m6 6 12 12"></path>
        </svg>
    </a>
    <h2>{{ $attribute->group->name . ' > ' . $attribute->name}}</h2>
    <input type="hidden" name="attribute.id[]" value=" {{ $attribute->id }}">
    @if($attribute->isVariant())
        <select id="select-variant-{{ $attribute->id }}" name="attribute_{{ $attribute->id }}[]" class="w-full new-tom-select bg-white tom-select"
                           data-placeholder="Выберите вторичные категории" {{ $attribute->multiple ? 'multiple' : ''}}>
            @foreach($attribute->variants as $variant)
                <option value="{{ $variant->id }}">{{ $variant->name }}</option>
            @endforeach
        </select>
    @else
        <div class="input-form">
            @if ($attribute->isBool())
                <div class="flex items-center mt-3">
                    <input type="checkbox" class="transition-all duration-100 ease-in-out shadow-sm border-slate-200 cursor-pointer focus:ring-4 focus:ring-offset-0 focus:ring-primary focus:ring-opacity-20 dark:bg-darkmode-800 dark:border-transparent dark:focus:ring-slate-700 dark:focus:ring-opacity-50 [&amp;[type='radio']]:checked:bg-primary [&amp;[type='radio']]:checked:border-primary [&amp;[type='radio']]:checked:border-opacity-10 [&amp;[type='checkbox']]:checked:bg-primary [&amp;[type='checkbox']]:checked:border-primary [&amp;[type='checkbox']]:checked:border-opacity-10 [&amp;:disabled:not(:checked)]:bg-slate-100 [&amp;:disabled:not(:checked)]:cursor-not-allowed [&amp;:disabled:not(:checked)]:dark:bg-darkmode-800/50 [&amp;:disabled:checked]:opacity-70 [&amp;:disabled:checked]:cursor-not-allowed [&amp;:disabled:checked]:dark:bg-darkmode-800/50 w-[38px] h-[24px] p-px rounded-full relative before:w-[20px] before:h-[20px] before:shadow-[1px_1px_3px_rgba(0,0,0,0.25)] before:transition-[margin-left] before:duration-200 before:ease-in-out before:absolute before:inset-y-0 before:my-auto before:rounded-full before:dark:bg-darkmode-600 checked:bg-primary checked:border-primary checked:bg-none before:checked:ml-[14px] before:checked:bg-white"
                           id="checkbox_{{ $attribute->id }}" name="attribute_{{ $attribute->id }}">
                    <label for="checkbox_{{ $attribute->id }}" class="cursor-pointer ml-2">{{ $attribute->name }}</label></div>
            @else
                <input type="text" name="attribute_{{ $attribute->id }}" class="form-control " placeholder="Значение" value="">
            @endif
        </div>
    @endif
</div>

