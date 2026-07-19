<template>
  <div class="html-editor" ref="editorContainer">
    <codemirror
      :model-value="modelValue"
      :extensions="extensions"
      :style="{ height: height + 'px' }"
      @update:model-value="$emit('update:modelValue', $event)"
      :disabled="disabled"
      :placeholder="placeholder"
    />
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue'
import { Codemirror } from 'vue-codemirror'
import { html } from '@codemirror/lang-html'
import { oneDark } from '@codemirror/theme-one-dark'
import { EditorView } from 'codemirror'

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

const extensions = computed(() => [
  // Подсветка только HTML (без PHP)
  html(),
  // Если darkMode включён — тёмная тема
  ...(props.darkMode ? [oneDark] : []),
  // Настройки редактора
  EditorView.theme({
    '&': { fontSize: '13px' },
    '.cm-scroller': { fontFamily: "'Courier New', Courier, monospace" },
    '.cm-content': { caretColor: '#6366f1' },
    '&.cm-focused .cm-cursor': { borderLeftColor: '#6366f1' },
  }),
])
</script>

<style scoped>
.html-editor {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  overflow: hidden;
}
.html-editor:focus-within {
  border-color: #409eff;
}
</style>
