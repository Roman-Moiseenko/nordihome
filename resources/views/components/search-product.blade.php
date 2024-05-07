<div class="search-product relative {{ $class }}" id="search-{{ $inputData }}"
    {{ !is_null($callback) ? 'data-callback="' . $callback . '"' : '' }}
    {{ !empty($hiddenId) ? 'data-hidden="' . $hiddenId . '"' : '' }}

>

    <input id="{{ $inputData }}" type="text" name="search" value="" class="form-control" placeholder="Поиск ..."
           data-route="{{ $route }}" data-url="" data-id="" data-name="" data-img="" data-code="" data-price="">
    @if(!empty($hiddenId))
        <input type="hidden" id="hidden-id" name="{{ $hiddenId }}" value="" {{ !empty($wireModel) ? 'wire:model=' . $wireModel . ' ' : '' }}>
    @endif
    <x-base.transition
        class="search-product-result absolute right-0 z-10 mt-[3px] hidden w-full"
        selector=".show"
        enter="transition-all ease-linear duration-150"
        enterFrom="mt-5 invisible opacity-0 translate-y-1"
        enterTo="mt-[3px] visible opacity-100 translate-y-0"
        leave="transition-all ease-linear duration-150"
        leaveFrom="mt-[3px] visible opacity-100 translate-y-0"
        leaveTo="mt-5 invisible opacity-0 translate-y-1"
    >
        <div class="box w-full p-0" role="listbox">
            <div class="text-slate-400">Вводите наименование или артикул товара</div>
        </div>
    </x-base.transition>
</div>

<script>
  /*  let _hiddenInput = document.getElementById('hidden-id');
    _hiddenInput.addEventListener('change', function (element) {
        console.log(_hiddenInput.value);
    });*/
</script>

@once
    @push('scripts')
        @vite('resources/js/components/search-product/index.js')
    @endpush
@endonce
