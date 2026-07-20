<template>
  <div class="property-attributes">
    <el-form :label-width="120" size="small" class="attrs-form">
      <!-- Имя ключа -->
      <el-form-item v-if="showKeyEditor" label="Ключ">
        <el-input v-model="editablePropName" size="small" @blur="onPropNameBlur" />
      </el-form-item>

      <!-- type + title в одной строке -->
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

      <el-form-item label="title">
        <el-input v-model="localConfig.title" placeholder="Название поля" @input="emitUpdate" />
      </el-form-item>

      <!-- format -->
      <el-form-item label="format">
        <el-select v-model="localConfig.format" class="w-full" clearable @change="emitUpdate">
          <el-option label="Без формата" value="" />
          <el-option label="html" value="html" />
          <el-option label="image (изображение)" value="image" />
          <el-option label="product (товар)" value="product" />
          <el-option label="uri (ссылка)" value="uri" />
          <el-option label="date" value="date" />
          <el-option label="date-time" value="date-time" />
          <el-option label="color (цвет)" value="color" />
          <el-option label="widget (ID виджета)" value="widget" />
        </el-select>
      </el-form-item>

      <!-- default -->
      <el-form-item label="default">
        <el-input v-model="defaultStr" :placeholder="stringDefaultHint" @input="onDefaultChange" />
      </el-form-item>

      <!-- minimum / maximum (опционально) -->
      <template v-if="localConfig.type === 'integer' || localConfig.type === 'number'">
        <el-form-item>
          <template #label>
            <span class="flex items-center gap-1">
              <el-switch :model-value="useMinMax" size="small" @change="toggleMinMax" />
              Min / Max
            </span>
          </template>
          <template v-if="useMinMax">
            <div class="flex gap-2">
              <el-input-number v-model="localConfig.minimum" @change="emitUpdate" class="flex-1" placeholder="min" />
              <el-input-number v-model="localConfig.maximum" @change="emitUpdate" class="flex-1" placeholder="max" />
            </div>
          </template>
          <span v-else class="text-gray-400 text-xs">Не ограничено</span>
        </el-form-item>
      </template>

      <!-- enum -->
      <el-form-item v-if="localConfig.type === 'string'" label="enum">
        <el-input v-model="enumStr" placeholder="value1, value2, value3" @input="onEnumChange" />
      </el-form-item>

      <!-- required -->
      <el-form-item label="Обязательное">
        <el-switch :model-value="required" @change="(val) => $emit('update:required', val)" />
      </el-form-item>

      <!-- Если array — настройка items -->
      <template v-if="localConfig.type === 'array'">
        <el-divider />
        <el-form-item label="Тип элементов">
          <el-select v-model="itemsType" class="w-full" @change="onItemsTypeChange">
            <el-option label="Строка (string)" value="string" />
            <el-option label="Целое число (integer)" value="integer" />
            <el-option label="Дробное число (number)" value="number" />
            <el-option label="Объект (object)" value="object" />
            <el-option label="Изображение (image)" value="image" />
            <el-option label="Товар (product)" value="product" />
          </el-select>
        </el-form-item>

        <!-- items.object properties со сворачиванием -->
        <template v-if="itemsType === 'object' || itemsType === 'product' || itemsType === 'image'">
          <el-form-item label="Поля объекта">
            <ItemPropertiesEditor
              :properties="itemProperties"
              :editable-keys="editableSubKeys"
              @add="addItemProperty"
              @remove="removeItemProperty"
              @update-key="onSubKeyBlur"
              @update-prop="onItemPropUpdate"
            />
          </el-form-item>
        </template>
      </template>

      <!-- Если object — properties со сворачиванием -->
      <template v-if="localConfig.type === 'object'">
        <el-divider />
        <el-form-item label="Вложенные поля">
          <ItemPropertiesEditor
            :properties="objectProperties"
            :editable-keys="objectEditableKeys"
            @add="addObjectProperty"
            @remove="removeObjectProperty"
            @update-key="onObjectKeyBlur"
            @update-prop="onObjectPropUpdate"
          />
        </el-form-item>
      </template>
    </el-form>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, reactive, computed, watch } from 'vue'
import { Plus, Delete } from '@element-plus/icons-vue'
import ItemPropertiesEditor from './ItemPropertiesEditor.vue'

const props = defineProps({
  propName: { type: String, required: true },
  propConfig: { type: Object, required: true },
  required: { type: Boolean, default: false },
  showKeyEditor: { type: Boolean, default: false },
})

const emit = defineEmits(['update:propConfig', 'update:required', 'update:propName'])

const editablePropName = ref(props.propName)

watch(() => props.propName, (val) => {
  editablePropName.value = val
})

const localConfig = reactive<Record<string, any>>({})

// Инициализируем localConfig из пропсов только один раз (при создании)
// Не используем watch, чтобы избежать циклического сброса изменений
function initLocalConfig() {
  Object.assign(localConfig, JSON.parse(JSON.stringify(props.propConfig)))
}
initLocalConfig()

const enumStr = ref('')
const defaultStr = ref('')

const stringDefaultHint = computed(() => {
  if (localConfig.type === 'integer') return '0'
  if (localConfig.type === 'number') return '0.0'
  if (localConfig.type === 'boolean') return 'true/false'
  return 'текст'
})

// Инициализируем enumStr и defaultStr из пропсов только один раз (при создании)
function initEnumAndDefault() {
  enumStr.value = props.propConfig?.enum?.join(', ') || ''
  if (props.propConfig?.default !== undefined) {
    defaultStr.value = String(props.propConfig.default)
  } else {
    defaultStr.value = ''
  }
}
initEnumAndDefault()

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

const useMinMax = computed(() => {
  return localConfig.minimum !== undefined || localConfig.maximum !== undefined
})

function toggleMinMax(val: boolean) {
  if (val) {
    localConfig.minimum = 0
    localConfig.maximum = 100
  } else {
    delete localConfig.minimum
    delete localConfig.maximum
  }
  emitUpdate()
}

function onPropNameBlur() {
  if (editablePropName.value && editablePropName.value !== props.propName) {
    emit('update:propName', editablePropName.value)
  }
}

// Items array
const itemsType = ref('string')
const itemProperties = reactive<Record<string, any>>({})
const editableSubKeys = reactive<Record<string, string>>({})

// Инициализируем items из пропсов только один раз (при создании)
function initItems() {
  const val = props.propConfig?.items
  if (val) {
    itemsType.value = val.format === 'image' ? 'image' : (val.format === 'product' ? 'product' : (val.type || 'string'))
    const keys = Object.keys(itemProperties)
    for (const k of keys) { delete itemProperties[k]; delete editableSubKeys[k] }
    if (val.properties) {
      for (const [key, cfg] of Object.entries(JSON.parse(JSON.stringify(val.properties)))) {
        itemProperties[key] = cfg
        editableSubKeys[key] = key
      }
    }
  }
}
initItems()

function onItemsTypeChange() {
  if (!localConfig.items) localConfig.items = {}
  localConfig.items.type = 'object'
  if (itemsType.value === 'image') {
    localConfig.items.format = 'image'
    localConfig.items.properties = {
      id: { type: 'integer', title: 'ID изображения' },
      src: { type: 'string', title: 'URL (src)' },
      alt: { type: 'string', title: 'Alt текст' },
      title: { type: 'string', title: 'Title текст' },
      description: { type: 'string', title: 'Описание' },
    }
  } else if (itemsType.value === 'product') {
    localConfig.items.format = 'product'
    localConfig.items.properties = {
      id: { type: 'integer', title: 'ID товара' },
      name: { type: 'string', title: 'Название' },
      url: { type: 'string', title: 'URL' },
      short: { type: 'string', title: 'Краткое описание' },
      price: { type: 'number', title: 'Цена' },
      image_src: { type: 'string', title: 'URL изображения' },
      image_alt: { type: 'string', title: 'Alt изображения' },
      image_next_src: { type: 'string', title: 'URL второго изображения' },
      image_next_alt: { type: 'string', title: 'Alt второго изображения' },
    }
  } else if (itemsType.value === 'object') {
    delete localConfig.items.format
    if (!localConfig.items.properties) localConfig.items.properties = {}
  } else {
    delete localConfig.items.format
    delete localConfig.items.properties
    localConfig.items.type = itemsType.value
  }
  emitUpdate()
}

function addItemProperty() {
  const name = 'field_' + Date.now()
  if (!localConfig.items.properties) localConfig.items.properties = {}
  localConfig.items.properties[name] = { type: 'string', title: 'Новое поле' }
  itemProperties[name] = { type: 'string', title: 'Новое поле' }
  editableSubKeys[name] = name
  emitUpdate()
}

function removeItemProperty(name: string) {
  delete localConfig.items.properties?.[name]
  delete itemProperties[name]
  delete editableSubKeys[name]
  emitUpdate()
}

function onSubKeyBlur(oldName: string, newName: string) {
  if (!newName || newName === oldName) return
  if (localConfig.items.properties[newName]) return
  const newProps: Record<string, any> = {}
  for (const key of Object.keys(localConfig.items.properties)) {
    if (key === oldName) newProps[newName] = localConfig.items.properties[oldName]
    else newProps[key] = localConfig.items.properties[key]
  }
  localConfig.items.properties = newProps
  editableSubKeys[newName] = newName; delete editableSubKeys[oldName]
  itemProperties[newName] = itemProperties[oldName]; delete itemProperties[oldName]
  emitUpdate()
}

function onItemPropUpdate(name: string, key: string, value: any) {
  if (localConfig.items.properties?.[name]) {
    localConfig.items.properties[name][key] = value
    itemProperties[name][key] = value
    emitUpdate()
  }
}

// Object properties
const objectProperties = reactive<Record<string, any>>({})
const objectEditableKeys = reactive<Record<string, string>>({})

// Инициализируем объектные properties из пропсов только один раз (при создании)
function initObjectProperties() {
  if (localConfig.type === 'object') {
    // Если format === 'image' — предзаполняем стандартные поля, если их ещё нет
    const isImage = localConfig.format === 'image'
    if (isImage) {
      if (!localConfig.properties) localConfig.properties = {}
      const defaults: Record<string, any> = {
        id: { type: 'integer', title: 'ID изображения' },
        src: { type: 'string', title: 'URL (src)' },
        alt: { type: 'string', title: 'Alt текст' },
        title: { type: 'string', title: 'Title текст' },
        description: { type: 'string', title: 'Описание' },
      }
      for (const [key, cfg] of Object.entries(defaults)) {
        if (!localConfig.properties[key]) {
          localConfig.properties[key] = cfg
        }
      }
    }

    // Если format === 'product' — предзаполняем поля товара
    const isProduct = localConfig.format === 'product'
    if (isProduct) {
      if (!localConfig.properties) localConfig.properties = {}
      const defaults: Record<string, any> = {
        id: { type: 'integer', title: 'ID товара' },
        name: { type: 'string', title: 'Название' },
        url: { type: 'string', title: 'URL' },
        short: { type: 'string', title: 'Краткое описание' },
        price: { type: 'number', title: 'Цена' },
        image_src: { type: 'string', title: 'URL изображения' },
        image_alt: { type: 'string', title: 'Alt изображения' },
        image_next_src: { type: 'string', title: 'URL второго изображения' },
        image_next_alt: { type: 'string', title: 'Alt второго изображения' },
      }
      for (const [key, cfg] of Object.entries(defaults)) {
        if (!localConfig.properties[key]) {
          localConfig.properties[key] = cfg
        }
      }
    }

    const propsData = props.propConfig?.properties || localConfig.properties || {}
    const keys = Object.keys(objectProperties)
    for (const k of keys) { delete objectProperties[k]; delete objectEditableKeys[k] }
    for (const [key, cfg] of Object.entries(JSON.parse(JSON.stringify(propsData)))) {
      objectProperties[key] = cfg
      objectEditableKeys[key] = key
    }
  }
}
initObjectProperties()

function addObjectProperty() {
  const name = 'field_' + Date.now()
  if (!localConfig.properties) localConfig.properties = {}
  localConfig.properties[name] = { type: 'string', title: 'Новое поле' }
  objectProperties[name] = { type: 'string', title: 'Новое поле' }
  objectEditableKeys[name] = name
  emitUpdate()
}

function removeObjectProperty(name: string) {
  delete localConfig.properties?.[name]
  delete objectProperties[name]
  delete objectEditableKeys[name]
  emitUpdate()
}

function onObjectKeyBlur(oldName: string, newName: string) {
  if (!newName || newName === oldName) return
  if (localConfig.properties[newName]) return
  const newProps: Record<string, any> = {}
  for (const key of Object.keys(localConfig.properties)) {
    if (key === oldName) newProps[newName] = localConfig.properties[oldName]
    else newProps[key] = localConfig.properties[key]
  }
  localConfig.properties = newProps
  objectEditableKeys[newName] = newName; delete objectEditableKeys[oldName]
  objectProperties[newName] = objectProperties[oldName]; delete objectProperties[oldName]
  emitUpdate()
}

function onObjectPropUpdate(name: string, key: string, value: any) {
  if (localConfig.properties?.[name]) {
    localConfig.properties[name][key] = value
    objectProperties[name][key] = value
    emitUpdate()
  }
}

function emitUpdate() {
  if (localConfig.format === '' || localConfig.format === undefined) delete localConfig.format
  if (localConfig.minimum === undefined) delete localConfig.minimum
  if (localConfig.maximum === undefined) delete localConfig.maximum
  // При переключении type на 'object' — инициализируем properties, если их нет
  if (localConfig.type === 'object' && !localConfig.properties) {
    localConfig.properties = {}
  }
  // При переключении type на 'array' — инициализируем items, если их нет
  if (localConfig.type === 'array' && !localConfig.items) {
    localConfig.items = { type: 'string' }
  }
  emit('update:propConfig', JSON.parse(JSON.stringify(localConfig)))
}
</script>

<style scoped>
.attrs-form :deep(.el-form-item) {
  margin-bottom: 0;
}
.attrs-form :deep(.el-form-item__label) {
  font-size: 12px;
}
.attrs-form :deep(.el-form-item__content) {
  flex: 1;
}
</style>
