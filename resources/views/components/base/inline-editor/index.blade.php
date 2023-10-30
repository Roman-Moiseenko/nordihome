<textarea name="{{ $name }}" class="editor">
    {{ $slot }}
</textarea>

@once
    @push('vendors')
        @vite('resources/js/vendor/ckeditor/inline/index.js')
    @endpush
@endonce

@once
    @push('scripts')
        @vite('resources/js/components/inline-editor/index.js')
    @endpush
@endonce
