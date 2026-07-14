<template>
  <div class="property-attributes">
    <el-collapse v-model="activeCollapse">
      <el-collapse-item title="Атрибуты поля" name="attrs">
        <el-form label-position="top" size="small" class="attrs-form">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="type">
                <el-select v-model="localConfig.type" class="w-full" @change="emitUpdate">
                  <el-option label="string" value="string" />
                  <el-option label="integer" value="integer" />
                  <el-option label="number" value="number" />
                  <el-option label="boolean" value="boolean" />
                  <el-option label="array" value="array" />
                  <el-option label="object" value="object" />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="title">
                <el-input v-model="localConfig.title" placeholder="Название поля" @input="emitUpdate" />
              </el-form-item>
            </el-col>
          </el-row>

          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="format">
                <el-select v-model="localConfig.format" class="w-full" clearable @change="emitUpdate">
                  <el-option label="Без формата" value="" />
                  <el-option label="html" value="html" />
                  <el-option label="uuid (изображение)" value="uuid" />
                  <el-option label="uri (ссылка)" value="uri" />
                  <el-option label="date" value="date" />
                  <el-option label="date-time" value="date-time" />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="default">
                <el-input v-model="defaultStr" :placeholder="stringDefaultHint" @input="onDefaultChange" />
              </el-form-item>
            </el-col>
          </el-row>

          <el-row :gutter="10" v-if="localConfig.type === 'integer' || localConfig.type === 'number'">
            <el-col :span="12">
              <el-form-item label="minimum">
                <el-input-number v-model="localConfig.minimum" :min="0" @change="emitUpdate" class="w-full" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="maximum">
                <el-input-number v-model="localConfig.maximum" :min="0" @change="emitUpdate" class="w-full" />
              </el-form-item>
            </el-col>
          </el-row>

          <el-form-item v-if="localConfig.type === 'string'" label="enum (через запятую)">
            <el-input v-model="enumStr" placeholder="value1, value2, value3" @input="onEnumChange" />
          </el-form-item>

          <el-form-item label="Обязательное">
            <el-switch :model-value="required" @change="(val) => $emit('update:required', val)" />
          </el-form-item>

          <!-- Если array — редактируем items -->
          <template v-if="localConfig.type === 'array'">
            <el-divider />
            <label class="text-sm font-medium mb-2">Настройка items (элементы массива)</label>

            <el-form-item label="Тип элементов">
              <el-select v-model="itemsType" class="w-full" @change="onItemsTypeChange">
                <el-option label="Строка (string)" value="string" />
                <el-option label="Целое число (integer)" value="integer" />
                <el-option label="Дробное число (number)" value="number" />
                <el-option label="Объект (object)" value="object" />
              </el-select>
            </el-form-item>

            <!-- Если items — объект, показываем его properties -->
            <template v-if="itemsType === 'object'">
              <div class="ml-4 p-3 border-l-2 border-blue-300">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-medium">Свойства объектов в массиве</span>
                  <el-button size="small" type="primary" plain @click="addItemProperty">
                    <el-icon><Plus /></el-icon>
                    Добавить поле объекта
                  </el-button>
                </div>
                <div
                  v-for="(subProp, subName) in itemProperties"
                  :key="subName"
                  class="mb-2 p-2 border rounded bg-white"
                >
                  <div class="flex items-center justify-between mb-1">
                    <code class="text-xs font-bold">{{ subName }}</code>
                    <el-button size="small" type="danger" plain @click="removeItemProperty(subName)">
                      <el-icon><Delete /></el-icon>
                    </el-button>
                  </div>
                  <el-row :gutter="8">
                    <el-col :span="8">
                      <el-input v-model="subProp.title" size="mini" placeholder="title" @input="emitUpdate" />
                    </el-col>
                    <el-col :span="6">
                      <el-select v-model="subProp.type" size="mini" class="w-full" @change="emitUpdate">
                        <el-option label="string" value="string" />
                        <el-option label="integer" value="integer" />
                        <el-option label="number" value="number" />
                        <el-option label="boolean" value="boolean" />
                      </el-select>
                    </el-col>
                    <el-col :span="5">
                      <el-select v-model="subProp.format" size="mini" class="w-full" clearable @change="emitUpdate">
                        <el-option label="—" value="" />
                        <el-option label="html" value="html" />
                        <el-option label="uuid" value="uuid" />
                        <el-option label="uri" value="uri" />
                      </el-select>
                    </el-col>
                    <el-col :span="5">
                      <el-input v-model="subProp.default" size="mini" placeholder="default" @input="emitUpdate" />
                    </el-col>
                  </el-row>
                  <!-- Добавляем minimum/maximum для integer -->
                  <el-row :gutter="8" v-if="subProp.type === 'integer' || subProp.type === 'number'" class="mt-1">
                    <el-col :span="12">
                      <el-input-number v-model="subProp.minimum" size="mini" :min="0" class="w-full" @change="emitUpdate" />
                    </el-col>
                    <el-col :span="12">
                      <el-input-number v-model="subProp.maximum" size="mini" :min="0" class="w-full" @change="emitUpdate" />
                    </el-col>
                  </el-row>
                </div>
                <p v-if="Object.keys(itemProperties).length === 0" class="text-gray-400 text-xs mt-1">
                  Нет полей. Нажмите "Добавить поле объекта".
                </p>
              </div>
            </template>
          </template>

          <!-- Если object — редактируем properties -->
          <template v-if="localConfig.type === 'object'">
            <el-divider />
            <p class="text-gray-400 text-xs">Для object properties настраиваются рекурсивно (в разработке). Пока используйте JSON.</p>
          </template>
        </el-form>
      </el-collapse-item>
    </el-collapse>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, reactive, computed, watch } from 'vue'
import { Plus, Delete } from '@element-plus/icons-vue'

const props = defineProps({
  propName: { type: String, required: true },
  propConfig: { type: Object, required: true },
  required: { type: Boolean, default: false },
})

const emit = defineEmits(['update:propConfig', 'update:required'])

// Локальная копия конфига для редактирования
const localConfig = reactive<Record<string, any>>({})

watch(() => props.propConfig, (val) => {
  Object.assign(localConfig, JSON.parse(JSON.stringify(val)))
}, { immediate: true, deep: true })

const activeCollapse = ref('attrs')
const enumStr = ref('')
const defaultStr = ref('')

const stringDefaultHint = computed(() => {
  if (localConfig.type === 'integer') return '0'
  if (localConfig.type === 'number') return '0.0'
  if (localConfig.type === 'boolean') return 'true/false'
  return 'текст'
})

watch(() => props.propConfig, () => {
  enumStr.value = props.propConfig?.enum?.join(', ') || ''
  if (props.propConfig?.default !== undefined) {
    defaultStr.value = String(props.propConfig.default)
  } else {
    defaultStr.value = ''
  }
}, { immediate: true })

function onEnumChange() {
  if (enumStr.value) {
    localConfig.enum = enumStr.value.split(',').map((s: string) => s.trim()).filter(Boolean)
  } else {
    delete localConfig.enum
  }
  emitUpdate()
}

function onDefaultChange() {
  if (defaultStr.value) {
    const raw = defaultStr.value
    if (localConfig.type === 'integer') localConfig.default = parseInt(raw, 10) || 0
    else if (localConfig.type === 'number') localConfig.default = parseFloat(raw) || 0
    else if (localConfig.type === 'boolean') localConfig.default = raw === 'true' || raw === '1'
    else localConfig.default = raw
  } else {
    delete localConfig.default
  }
  emitUpdate()
}

// Items type
const itemsType = ref('string')
const itemProperties = reactive<Record<string, any>>({})

watch(() => props.propConfig?.items, (val) => {
  if (val) {
    itemsType.value = val.type || 'string'
    if (val.properties) {
      Object.assign(itemProperties, JSON.parse(JSON.stringify(val.properties)))
    }
  }
}, { immediate: true, deep: true })

function onItemsTypeChange() {
  if (!localConfig.items) localConfig.items = {}
  localConfig.items.type = itemsType.value
  if (itemsType.value === 'object' && !localConfig.items.properties) {
    localConfig.items.properties = {}
  }
  if (itemsType.value !== 'object') {
    delete localConfig.items.properties
  }
  emitUpdate()
}

function addItemProperty() {
  const name = 'field_' + Date.now()
  if (!localConfig.items.properties) {
    localConfig.items.properties = {}
  }
  localConfig.items.properties[name] = { type: 'string', title: 'Новое поле' }
  itemProperties[name] = { type: 'string', title: 'Новое поле' }
  emitUpdate()
}

function removeItemProperty(name: string) {
  delete localConfig.items.properties[name]
  delete itemProperties[name]
  emitUpdate()
}

function emitUpdate() {
  // Убираем пустые format
  if (localConfig.format === '' || localConfig.format === undefined) {
    delete localConfig.format
  }
  if (localConfig.minimum === undefined) delete localConfig.minimum
  if (localConfig.maximum === undefined) delete localConfig.maximum
  emit('update:propConfig', { ...localConfig })
}
</script>

<style scoped>
.attrs-form :deep(.el-form-item) {
  margin-bottom: 12px;
}
.attrs-form :deep(.el-form-item__label) {
  font-size: 12px;
  padding-bottom: 2px;
}
</style>
