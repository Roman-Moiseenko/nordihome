<div class="border-2 border-dashed shadow-sm border-slate-200/60 dark:border-darkmode-400 rounded-md p-5">
    <div class="h-40 relative image-fit cursor-pointer zoom-in mx-auto">
        <img id="preview-image" class="rounded-md {{isset($staff) ? (empty($staff->photo) ? 'hidden' : '') :'hidden' }}" alt="" src="">
        <div id="remove-photo" title="Remove this profile photo?"
             class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white
             bg-danger right-0 top-0 -mr-2 -mt-2 {{isset($staff) ? (empty($staff->photo) ? 'hidden' : '') :'hidden' }}">
            <i data-lucide="x" width="24" height="24"></i>
        </div>
    </div>
    <div class="mx-auto cursor-pointer relative mt-5">
        <button type="button" class="btn btn-primary w-full">Change Photo</button>
        <input id="image-upload" type="file" class="w-full h-full top-0 left-0 absolute opacity-0">
    </div>
</div>
<script type="text/javascript">
    let imgInp = document.getElementById('image-upload');
    let blah = document.getElementById('preview-image');
    let removeDiv = document.getElementById('remove-photo');
    imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
            blah.src = URL.createObjectURL(file)
            blah.classList.remove('hidden');
            removeDiv.classList.remove('hidden');
        }
    }
    removeDiv.onclick = evt => {
        blah.src = '';
        blah.classList.add('hidden');
        removeDiv.classList.add('hidden');
    }
</script>
