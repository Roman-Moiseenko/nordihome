<template>
  <div class="array-primitive-editor">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm text-gray-600">{{ propConfig.title || propName }} ({{ localItems.length }})</span>
      <el-button size="small" type="primary" plain @click="addItem">
        <el-icon><Plus /></el-icon>
        Добавить
      </el-button>
    </div>

    <div v-if="localItems.length > 0">
      <div
        v-for="(item, index) in localItems"
        :key="index"
        class="flex items-center gap-2 mb-1"
      >
        <el-input
          v-if="itemType === 'string'"
          v-model="localItems[index]"
          size="small"
          placeholder="Значение"
          @input="emitUpdate"
        />
        <el-input-number
          v-else
          v-model="localItems[index]"
          size="small"
          :min="0"
          @change="emitUpdate"
        />
        <el-button size="small" type="danger" plain @click="removeItem(index)">
          <el-icon><Delete /></el-icon>
        </el-button>
      </div>
    </div>
    <div v-else class="text-center py-2 text-gray-400 text-sm border border-dashed rounded-lg">
      Пусто
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, computed, watch } from 'vue'
import { Plus, Delete } from '@element-plus/icons-vue'

const props = defineProps({
  propName: { type: String, required: true },
  propConfig: { type: Object, required: true },
  modelValue: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue'])

const localItems = ref<any[]>([])

const itemType = computed(() => {
  return props.propConfig?.items?.type || 'string'
})

watch(() => props.modelValue, (val) => {
  localItems.value = val?.length ? [...val] : []
}, { immediate: true, deep: true })

function addItem() {
  localItems.push(itemType.value === 'integer' ? 0 : '')
  emitUpdate()
}

function removeItem(index: number) {
  localItems.value.splice(index, 1)
  emitUpdate()
}

function emitUpdate() {
  emit('update:modelValue', [...localItems.value])
}
</script>
