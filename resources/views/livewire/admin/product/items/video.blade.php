<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-8">
            <div class="relative pl-5 pr-5 xl:pr-10 pt-10 pb-5 bg-slate-50 rounded-md mt-3">
                <div class="input-form">
                    <input type="text" class="form-control" placeholder="Ссылка на видео" wire:model="url" autocomplete="off">
                </div>
                <div class="input-form mt-3 ">
                    <input type="text" class="form-control" placeholder="Заголовок" wire:model="caption" autocomplete="off">
                </div>
                <textarea class="form-control sm:mr-2 mt-3" rows="2" placeholder="Краткое описание" wire:model="text"></textarea>
            </div>
            @foreach($product->videos as $video)
                <livewire:admin.product.items.video-item :video="$video" :key="$video->id"/>
            @endforeach
        </div>
        <div class="hidden lg:col-span-4 lg:block">
            <div>
                Размещайте видеоматериалы о товаре на сторонних хостингах <br>
                Например, Rutube или YouTube<br>
                Снимайте видео в хорошем качестве, но компактного размера для быстрой загрузке на мобильных
                телефонах.<br>
                Видео должно быть формата Short - до 3 минут, рекомендация 1 минута.
            </div>
            <div>
                <button class="btn btn-primary w-full mt-4" type="button" wire:click="add">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         data-lucide="file-video-2" class="lucide lucide-file-video-2 stroke-1.5 mr-2">
                        <path d="M4 8V4a2 2 0 0 1 2-2h8.5L20 7.5V20a2 2 0 0 1-2 2H4"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="m10 15.5 4 2.5v-6l-4 2.5"></path>
                        <rect width="8" height="6" x="2" y="12" rx="1"></rect>
                    </svg>
                    Добавить URL
                </button>
            </div>
        </div>
    </div>
</div>
