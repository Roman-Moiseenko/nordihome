<template>
  <div class="blade-editor" ref="editorContainer">
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
import { php } from '@codemirror/lang-php'
import { oneDark } from '@codemirror/theme-one-dark'
import { EditorView } from 'codemirror'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  height: {
    type: Number,
    default: 400
  },
  disabled: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: 'Blade-шаблон виджета...'
  },
  darkMode: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const extensions = computed(() => [
  // PHP поверх HTML — будет подсвечивать <?php ?> и весь HTML
  php({ plain: false }),
  // Подсветка HTML-тегов
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
.blade-editor {
  border: 1px solid #dcdfe6;
  border-radius: 4px;
  overflow: hidden;
}
.blade-editor:focus-within {
  border-color: #409eff;
}
</style>
