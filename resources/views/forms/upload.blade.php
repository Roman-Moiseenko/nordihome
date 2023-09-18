<div class="border-2 border-dashed shadow-sm border-slate-200/60 dark:border-darkmode-400 rounded-md p-5">
    <div class="h-40 relative image-fit cursor-pointer zoom-in mx-auto">
        <img id="preview-image" class="rounded-md {{ empty($src) ? 'hidden' : '' }}"
             alt="" src="{{ $src }}">
        <div id="remove-photo" title="Удалить фото из профиля?"
             class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white
             bg-danger right-0 top-0 -mr-2 -mt-2 {{ empty($src) ? 'hidden' : '' }}">
            <i data-lucide="x" width="24" height="24"></i>
        </div>
    </div>
    <div class="mx-auto cursor-pointer relative mt-5">
        <button type="button" class="btn btn-primary w-full">{{ empty($src) ? 'Загрузить фото' : 'Заменить фото' }}</button>
        <input id="image-upload" name="{{ $name }}" type="file" class="w-full h-full top-0 left-0 absolute opacity-0">
        <input id="image-clear" name="image-clear" type="hidden">
    </div>
</div>
<script type="text/javascript">
    let input = document.getElementById('image-upload');
    let _clear = document.getElementById('image-clear');
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
