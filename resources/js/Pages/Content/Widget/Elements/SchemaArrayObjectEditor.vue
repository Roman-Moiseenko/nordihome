<template>
  <div class="array-object-editor">
    <div class="flex items-center justify-between mb-2">
      <span class="text-sm text-gray-600">{{ propConfig.title || propName }} ({{ modelValue?.length || 0 }})</span>
      <el-button size="small" type="primary" plain @click="addItem">
        <el-icon><Plus /></el-icon>
        Добавить элемент
      </el-button>
    </div>

    <!-- Аккордеон элементов -->
    <div v-if="items.length > 0">
      <div
        v-for="(item, index) in items"
        :key="index"
        class="border rounded-md mb-2"
      >
        <div
          class="flex items-center justify-between px-3 py-2 bg-gray-100 cursor-pointer"
          @click="toggleItem(index)"
        >
          <span class="text-sm font-medium">
            #{{ index + 1 }} {{ getItemTitle(item, index) }}
          </span>
          <div class="flex gap-1">
            <el-button size="small" type="primary" link @click.stop="moveUp(index)" :disabled="index === 0">
              <el-icon><ArrowUp /></el-icon>
            </el-button>
            <el-button size="small" type="primary" link @click.stop="moveDown(index)" :disabled="index === items.length - 1">
              <el-icon><ArrowDown /></el-icon>
            </el-button>
            <el-button size="small" type="danger" plain @click.stop="removeItem(index)">
              <el-icon><Delete /></el-icon>
            </el-button>
            <el-icon class="ml-1" :class="{ 'rotate-180': openItems[index] }">
              <ArrowDown />
            </el-icon>
          </div>
        </div>
        <div v-if="openItems[index]" class="p-3">
          <el-form label-position="top" size="small">
            <!-- Для массива изображений — компактный рендер -->
            <template v-if="props.propConfig?.items?.format === 'image'">
              <div class="image-object-field">
                <div class="flex items-center gap-2 mb-1">
                  <label class="text-xs text-gray-500 w-12 shrink-0">ID:</label>
                  <el-input v-model="item.id" size="small" disabled class="flex-1" />
                </div>
                <div class="flex items-center gap-2 mb-1">
                  <label class="text-xs text-gray-500 w-12 shrink-0">URL:</label>
                  <el-input v-model="item.src" size="small" class="flex-1" @input="emitUpdate" />
                </div>
                <div class="flex items-center gap-2 mb-1">
                  <label class="text-xs text-gray-500 w-12 shrink-0">Alt:</label>
                  <el-input v-model="item.alt" size="small" class="flex-1" @input="emitUpdate" />
                </div>
                <div class="flex items-center gap-2 mb-1">
                  <label class="text-xs text-gray-500 w-12 shrink-0">Title:</label>
                  <el-input v-model="item.title" size="small" class="flex-1" @input="emitUpdate" />
                </div>
              </div>
            </template>

            <!-- Обычные поля -->
            <template v-else>
              <div
                v-for="(subProp, subName) in subProperties"
                :key="subName"
                class="mb-2"
              >
                <label class="text-xs text-gray-600 block mb-1">
                  {{ subProp.title || subName }}
                  <span v-if="isSubRequired(subName)" class="text-red-500">*</span>
                </label>

                <!-- String -->
                <el-input
                  v-if="subProp.type === 'string' && subProp.format !== 'uuid' && subProp.format !== 'uri' && subProp.format !== 'html' && !subProp.enum"
                  v-model="item[subName]"
                  :placeholder="subProp.default?.toString() || ''"
                  size="small"
                  @input="emitUpdate"
                />

                <!-- HTML -->
                <el-input
                  v-else-if="subProp.type === 'string' && subProp.format === 'html'"
                  v-model="item[subName]"
                  type="textarea"
                  :rows="2"
                  size="small"
                  @input="emitUpdate"
                />

                <!-- Image object (id, src, alt, title) -->
                <div v-else-if="subProp.type === 'object' && subProp.format === 'image'" class="image-object-field border rounded p-2 bg-gray-50">
                  <div class="flex items-center gap-2 mb-1">
                    <label class="text-xs text-gray-500 w-8">ID:</label>
                    <el-input v-model="item[subName].id" size="small" disabled class="flex-1" />
                  </div>
                  <div class="flex items-center gap-2 mb-1">
                    <label class="text-xs text-gray-500 w-8">URL:</label>
                    <el-input v-model="item[subName].src" size="small" class="flex-1" @input="emitUpdate" />
                  </div>
                  <div class="flex items-center gap-2 mb-1">
                    <label class="text-xs text-gray-500 w-8">Alt:</label>
                    <el-input v-model="item[subName].alt" size="small" class="flex-1" @input="emitUpdate" />
                  </div>
                  <div class="flex items-center gap-2 mb-1">
                    <label class="text-xs text-gray-500 w-8">Title:</label>
                    <el-input v-model="item[subName].title" size="small" class="flex-1" @input="emitUpdate" />
                  </div>
                </div>

                <!-- URI -->
                <el-input
                  v-else-if="subProp.type === 'string' && subProp.format === 'uri'"
                  v-model="item[subName]"
                  size="small"
                  @input="emitUpdate"
                />

                <!-- Enum -->
                <el-select
                  v-else-if="subProp.type === 'string' && subProp.enum"
                  v-model="item[subName]"
                  size="small"
                  class="w-full"
                  @change="emitUpdate"
                >
                  <el-option v-for="opt in subProp.enum" :key="opt" :value="opt" :label="opt" />
                </el-select>

                <!-- Integer -->
                <el-input-number
                  v-else-if="subProp.type === 'integer'"
                  v-model="item[subName]"
                  size="small"
                  :min="subProp.minimum ?? 0"
                  :max="subProp.maximum ?? 99999"
                  @change="emitUpdate"
                />

                <!-- Number -->
                <el-input-number
                  v-else-if="subProp.type === 'number'"
                  v-model="item[subName]"
                  size="small"
                  :min="subProp.minimum ?? 0"
                  :max="subProp.maximum ?? 99999"
                  :step="0.1"
                  @change="emitUpdate"
                />

                <!-- Boolean -->
                <el-switch
                  v-else-if="subProp.type === 'boolean'"
                  v-model="item[subName]"
                  size="small"
                  @change="emitUpdate"
                />
              </div>
            </template>
          </el-form>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-4 text-gray-400 text-sm border border-dashed rounded-lg">
      Нет элементов. Нажмите "Добавить элемент" чтобы добавить.
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, computed, watch } from 'vue'
import { Plus, Delete, ArrowUp, ArrowDown } from '@element-plus/icons-vue'
import UploadImageFile from "@Comp/UploadImageFile.vue"

const props = defineProps({
  propName: { type: String, required: true },
  propConfig: { type: Object, required: true },
  modelValue: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue'])

const items = ref<any[]>([])
const openItems = ref<Record<number, boolean>>({})

const subProperties = computed(() => {
  return props.propConfig?.items?.properties || {}
})

const subRequired = computed(() => {
  return props.propConfig?.items?.required || []
})

watch(() => props.modelValue, (val) => {
  items.value = val?.length ? JSON.parse(JSON.stringify(val)) : []
}, { immediate: true, deep: true })

function getItemTitle(item: any, index: number): string {
  // Для массива изображений показываем src или alt
  if (props.propConfig?.items?.format === 'image') {
    return item?.src || item?.alt || `Изображение #${index + 1}`
  }
  // Ищем первое строковое поле для заголовка
  for (const [key, cfg] of Object.entries(subProperties.value)) {
    const c = cfg as any
    if (c.type === 'string' && item[key]) {
      return item[key].toString().substring(0, 30)
    }
  }
  return `Элемент #${index + 1}`
}

function isSubRequired(name: string): boolean {
  return subRequired.value.includes(name)
}

function toggleItem(index: number) {
  openItems.value[index] = !openItems.value[index]
}

function addItem() {
  const isImageArray = props.propConfig?.items?.format === 'image'

  if (isImageArray) {
    // Для массива изображений — сразу объект с id, src, alt, title
    items.value.push({ id: null, src: '', alt: '', title: '' })
  } else {
    const newItem: Record<string, any> = {}
    for (const [key, cfg] of Object.entries(subProperties.value)) {
      const c = cfg as any
      if (c.type === 'object' && c.format === 'image') {
        newItem[key] = { id: null, src: '', alt: '', title: '' }
      } else if (c.default !== undefined) {
        newItem[key] = c.default
      } else if (c.type === 'boolean') {
        newItem[key] = false
      } else if (c.type === 'array') {
        newItem[key] = []
      } else if (c.type === 'integer' || c.type === 'number') {
        newItem[key] = 0
      } else {
        newItem[key] = ''
      }
    }
    items.value.push(newItem)
  }

  openItems.value[items.value.length - 1] = true
  emitUpdate()
}

function removeItem(index: number) {
  items.value.splice(index, 1)
  emitUpdate()
}

function moveUp(index: number) {
  if (index === 0) return
  const temp = items.value[index]
  items.value[index] = items.value[index - 1]
  items.value[index - 1] = temp
  emitUpdate()
}

function moveDown(index: number) {
  if (index >= items.value.length - 1) return
  const temp = items.value[index]
  items.value[index] = items.value[index + 1]
  items.value[index + 1] = temp
  emitUpdate()
}

function onSubImageUuid(item: any, subName: string, val: any) {
  if (val.file) {
    item[subName] = val.file.name || 'new-uuid'
  } else if (val.clear_file) {
    item[subName] = ''
  }
  emitUpdate()
}

function emitUpdate() {
  emit('update:modelValue', [...items.value])
}
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.2s;
}
</style>
