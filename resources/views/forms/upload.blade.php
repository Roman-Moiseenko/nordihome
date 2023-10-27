<div class="border-2 border-dashed shadow-sm border-slate-200/60 dark:border-darkmode-400 rounded-md p-5">
    <div class="h-40 relative image-fit cursor-pointer zoom-in mx-auto">
        <img id="preview-{{$id_prefix}}" class="rounded-md {{ empty($src) ? 'hidden' : '' }}"
             alt="" src="{{ $src }}">
        <div id="remove-{{$id_prefix}}" title="Удалить фото?"
             class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white
             bg-danger right-0 top-0 -mr-2 -mt-2 {{ empty($src) ? 'hidden' : '' }}">
            <x-base.lucide icon="X"/>
        </div>
    </div>
    <div class="mx-auto cursor-pointer relative mt-5">
        @if(!empty($placeholder))
        <div class="flex relative">
            <x-base.lucide icon="image" class="w-4 h-4 mr-2"/>
            <span class="text-primary mr-1">{{ $placeholder }}</span>
        </div>
        @endif
        <button type="button" class="btn btn-primary w-full">{{ empty($src) ? 'Загрузить фото' : 'Заменить фото' }}</button>
        <input id="{{$id_prefix}}-upload" name="{{ $name }}" type="file" class="w-full h-full top-0 left-0 absolute opacity-0">
        <input id="{{$id_prefix}}-clear" name="{{$id_prefix}}-clear" type="hidden">
    </div>
</div>
<script type="text/javascript">
    let input_{{$id_prefix}} = document.getElementById('{{$id_prefix}}-upload');
    let _clear_{{$id_prefix}} = document.getElementById('{{$id_prefix}}-clear');
    let blah_{{$id_prefix}} = document.getElementById('preview-{{$id_prefix}}');
    let removeDiv_{{$id_prefix}} = document.getElementById('remove-{{$id_prefix}}');
    input_{{$id_prefix}}.onchange = evt => {
        const [file] = input_{{$id_prefix}}.files
        if (file) {
            blah_{{$id_prefix}}.src = URL.createObjectURL(file)
            blah_{{$id_prefix}}.classList.remove('hidden');
            removeDiv_{{$id_prefix}}.classList.remove('hidden');
            _clear_{{$id_prefix}}.value = '';
        }
    }
    removeDiv_{{$id_prefix}}.onclick = evt => {
        blah_{{$id_prefix}}.src = '';
        blah_{{$id_prefix}}.classList.add('hidden');
        removeDiv_{{$id_prefix}}.classList.add('hidden');
        input_{{$id_prefix}}.value = '';
        _clear_{{$id_prefix}}.value = 'delete';
    }
</script>
