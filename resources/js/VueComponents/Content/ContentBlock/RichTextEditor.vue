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

// SVG-теги разрешаем дополнительно, чтобы в Source Code можно было вставлять иконки <svg>.
const svgTags = [
    'svg', 'path', 'g', 'defs', 'use', 'symbol', 'circle', 'rect',
    'line', 'polyline', 'polygon', 'ellipse',
]

// Дефолтный allowlist редактора + SVG-теги (иначе <svg> вырезается встроенным санитайзером).
const allowedTags = [
    'p', 'br', 'div', 'span', 'strong', 'b', 'em', 'i', 'u', 's', 'strike',
    'sup', 'sub', 'code', 'pre', 'blockquote',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
    'ul', 'ol', 'li', 'input', 'a', 'img', 'figure', 'figcaption',
    'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'hr',
    'video', 'audio', 'source', 'iframe',
    ...svgTags,
]

const allowedAttributes: Record<string, string[]> = {
    '*': ['class', 'style', 'title', 'dir', 'id'],
    span: ['data-erag-mention', 'data-erag-mention-id', 'data-erag-mention-label', 'data-erag-mention-value', 'data-erag-merge-tag', 'data-erag-merge-tag-value', 'data-erag-merge-tag-name', 'contenteditable'],
    ul: ['data-erag-checklist'],
    li: ['data-erag-checklist-item'],
    input: ['type', 'checked', 'contenteditable', 'data-erag-checklist-checkbox'],
    a: ['href', 'target', 'rel', 'name'],
    img: ['src', 'alt', 'width', 'height'],
    table: ['width', 'cellpadding', 'cellspacing', 'border'],
    td: ['colspan', 'rowspan', 'width', 'height'],
    th: ['colspan', 'rowspan', 'scope', 'width', 'height'],
    video: ['src', 'controls', 'width', 'height', 'poster'],
    audio: ['src', 'controls'],
    source: ['src', 'type'],
    iframe: ['src', 'width', 'height', 'allow', 'allowfullscreen', 'title'],
    svg: ['viewBox', 'xmlns', 'width', 'height', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin'],
    path: ['d', 'fill', 'stroke', 'stroke-width'],
    g: ['fill', 'stroke', 'stroke-width'],
    circle: ['cx', 'cy', 'r', 'fill', 'stroke', 'stroke-width'],
    rect: ['x', 'y', 'width', 'height', 'rx', 'ry', 'fill', 'stroke', 'stroke-width'],
    line: ['x1', 'y1', 'x2', 'y2', 'stroke', 'stroke-width'],
    polyline: ['points', 'fill', 'stroke', 'stroke-width'],
    polygon: ['points', 'fill', 'stroke', 'stroke-width'],
    ellipse: ['cx', 'cy', 'rx', 'ry', 'fill', 'stroke', 'stroke-width'],
    use: ['href', 'xlink:href'],
}

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
    allowedTags,
    allowedAttributes,
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
