<div>
    <div class="relative pt-5 pb-1 py-3 bg-slate-50 dark:bg-transparent dark:border rounded-md mt-3">
        <button type="button"
                class="text-slate-600 absolute top-0 right-0 mr-4 mt-4"
                wire:click="remove" title="Удалить составной товар">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="x"
                 class="lucide lucide-x stroke-1.5 h-4 w-4">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
        <div class="flex justify-between items-center m-3">
            <div class="flex items-center">
                <div class="image-fit w-10 h-10"><img class="rounded-full" src="{{ $composite->getImage('thumb') }}" alt=""></div>
                <div class="text-left ml-3">{{ $composite->name . ' (' . $composite->code . ')' }}</div>
            </div>

            <div>
                <input class="form-control form-input w-40 ml-3" type="text" placeholder="Кол-во" title="Кол-во"
                       wire:model="quantity"  wire:change="save" wire:loading.attr="disabled" autocomplete="off"> шт
            </div>
        </div>
    </div>
</div>
