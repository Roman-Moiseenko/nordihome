<template>
    <div
        class="rich-text-editor"
        :class="{ dark: darkMode }"
    >
        <Editor
            :model-value="modelValue"
            :init="editorInit"
            :disabled="disabled"
            :aria-label="placeholder"
            @update:model-value="onUpdate"
        />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Editor } from '@erag/text-editor-vue'
import '@erag/text-editor-vue/style.css'
import type { EditorInit, EditorToolbarGroup } from '@erag/text-editor-vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    height: {
        type: Number,
        default: 300
    },
    disabled: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'HTML-код...'
    },
    darkMode: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

// Компактная панель инструментов WYSIWYG-редактора.
// Кнопка "code" открывает режим просмотра/правки исходного HTML-кода.
const toolbar: EditorToolbarGroup[] = [
    { items: ['undo', 'redo'] },
    { items: ['blocks', 'fontfamily', 'fontsize', 'lineheight'] },
    { items: ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript'] },
    { items: ['forecolor', 'backcolor'] },
    { items: ['alignment'] },
    { items: ['bullist', 'numlist', 'checklist', 'outdent', 'indent'] },
    { items: ['link', 'image', 'media', 'table', 'hr'] },
    { items: ['removeformat', 'code', 'preview', 'fullscreen'] },
]

const editorInit = computed<EditorInit>(() => ({
    height: props.height,
    minHeight: 160,
    placeholder: props.placeholder,
    menubar: false,
    toolbar,
    statusbar: false,
    branding: false,
    promotion: false,
    resize: false,
    plugins: [
        'history',
        'formatting',
        'lists',
        'link',
        'image',
        'media',
        'table',
        'code',
        'preview',
        'fullscreen',
        'find-replace',
        'special-character',
        'emoji',
        'horizontal-rule',
        'anchor',
        'date-time',
    ],
}))

function onUpdate(value: string) {
    emit('update:modelValue', value)
}
</script>

<style scoped>
.rich-text-editor {
    width: 100%;
}
</style>
