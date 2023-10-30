<textarea name="{{ $name }}" class="editor">
    {{ $slot }}
</textarea>
@once
    @push('vendors')
        @vite('resources/js/vendor/ckeditor/balloon/index.js')
    @endpush
@endonce

@once
    @push('scripts')
        @vite('resources/js/components/balloon-editor/index.js')
    @endpush
@endonce
