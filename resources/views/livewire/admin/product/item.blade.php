<div>
    <div id="{{ $item }}" data-is-top="0" class="box p-5 mt-5 block-menus-product">
        <div class="rounded-md border border-slate-200/60 p-5 dark:border-darkmode-400">
            <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium dark:border-darkmode-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="chevron-down" class="lucide lucide-chevron-down stroke-1.5 mr-2 h-4 w-4"><path d="m6 9 6 6 6-6"></path></svg>
                {{ $caption }}
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="hard-drive-download" class="lucide lucide-hard-drive-download stroke-1.5 ml-auto h-4 w-4"><path d="M12 2v8"></path><path d="m16 6-4 4-4-4"></path><rect width="20" height="8" x="2" y="14" rx="2"></rect><path d="M6 18h.01"></path><path d="M10 18h.01"></path></svg>
            </div>
            <div class="mt-5">
                @livewire($element, ['product' => $product])
            </div>
        </div>
    </div>
</div>
