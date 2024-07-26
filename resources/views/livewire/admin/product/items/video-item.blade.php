<div>
    <div class="relative pl-5 pr-5 xl:pr-10 pt-10 pb-5 bg-slate-200 rounded-md mt-3">
        <button type="button" class="text-slate-800 absolute top-0 right-0 mr-4 mt-4" wire:click="remove" title="Удалить ссылку">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 data-lucide="x" class="lucide lucide-x stroke-1.5 h-4 w-4">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
        <div class="input-form">
            <input type="text" class="form-control" placeholder="Ссылка на видео" autocomplete="off"
                   wire:model="url" wire:change="save" wire:loading.attr="disabled">
        </div>
        <div class="input-form mt-3 ">
            <input type="text" class="form-control" placeholder="Заголовок" autocomplete="off"
                   wire:model="caption" wire:change="save" wire:loading.attr="disabled">
        </div>
        <textarea class="form-control sm:mr-2 mt-3" rows="2"
                  placeholder="Краткое описание" wire:model="text" wire:change="save" wire:loading.attr="disabled"></textarea>
    </div>

</div>
