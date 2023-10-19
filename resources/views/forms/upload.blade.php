<div class="border-2 border-dashed shadow-sm border-slate-200/60 dark:border-darkmode-400 rounded-md p-5">
    <div class="h-40 relative image-fit cursor-pointer zoom-in mx-auto">
        <img id="preview-image" class="rounded-md {{ empty($src) ? 'hidden' : '' }}"
             alt="" src="{{ $src }}">
        <div id="remove-photo" title="Удалить фото?"
             class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white
             bg-danger right-0 top-0 -mr-2 -mt-2 {{ empty($src) ? 'hidden' : '' }}">
            <i data-lucide="x" width="24" height="24"></i>
        </div>
    </div>
    <div class="mx-auto cursor-pointer relative mt-5">
        @if(!empty($placeholder))
        <div class="flex relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="image" data-lucide="image" class="lucide lucide-image w-4 h-4 mr-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span class="text-primary mr-1">{{ $placeholder }}</span>
        </div>
        @endif
        <button type="button" class="btn btn-primary w-full">{{ empty($src) ? 'Загрузить фото' : 'Заменить фото' }}</button>
        <input id="{{$id_prefix}}-upload" name="{{ $name }}" type="file" class="w-full h-full top-0 left-0 absolute opacity-0">
        <input id="{{$id_prefix}}-clear" name="image-clear" type="hidden">
    </div>
</div>
<script type="text/javascript">
    let input = document.getElementById('{{$id_prefix}}-upload');
    let _clear = document.getElementById('{{$id_prefix}}-clear');
    let blah = document.getElementById('preview-image');
    let removeDiv = document.getElementById('remove-photo');
    input.onchange = evt => {
        const [file] = input.files
        if (file) {
            blah.src = URL.createObjectURL(file)
            blah.classList.remove('hidden');
            removeDiv.classList.remove('hidden');
            _clear.value = '';
        }
    }
    removeDiv.onclick = evt => {
        blah.src = '';
        blah.classList.add('hidden');
        removeDiv.classList.add('hidden');
        input.value = '';
        _clear.value = 'delete';
    }
</script>
