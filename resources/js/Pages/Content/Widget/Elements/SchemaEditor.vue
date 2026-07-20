<template>
  <div class="schema-editor">
    <p class="text-gray-500 text-sm mb-3" v-if="!localSchema?.properties || Object.keys(localSchema.properties).length === 0">
      Схема пуста. Добавьте поля через кнопку ниже.
    </p>

    <div v-if="localSchema?.properties && Object.keys(localSchema.properties).length > 0">
      <el-collapse v-model="expandedFields" class="schema-collapse">
        <el-collapse-item
          v-for="(propConfig, propName) in localSchema.properties"
          :key="propName"
          :name="propName"
          class="schema-field mb-2"
        >
          <template #title>
            <div class="flex items-center justify-between w-full pr-2">
              <span class="font-medium text-sm">
                {{ propConfig.title || propName }}
                <el-tag size="small" type="info" class="ml-2">{{ propConfig.type }}</el-tag>
                <span v-if="isRequired(propName)" class="text-red-500 ml-1">*</span>
              </span>
              <el-button
                size="small"
                type="danger"
                plain
                @click.stop="removeProperty(propName)"
                :disabled="isRequired(propName)"
              >
                <el-icon><Delete /></el-icon>
              </el-button>
            </div>
          </template>

          <PropertyAttributesEditor
            :prop-name="propName"
            :prop-config="propConfig"
            :required="isRequired(propName)"
            :show-key-editor="true"
            @update:prop-config="(newConfig) => updatePropertyConfig(propName, newConfig)"
            @update:required="(val) => toggleRequired(propName, val)"
            @update:prop-name="(newName) => renameProperty(propName, newName)"
          />
        </el-collapse-item>
      </el-collapse>
    </div>

    <div class="mt-4 flex items-center gap-2">
      <el-button type="primary" plain @click="showAddDialog = true">
        <el-icon><Plus /></el-icon>
        Добавить поле
      </el-button>
      <slot name="actions" />
    </div>

    <el-dialog v-model="showAddDialog" title="Новое поле схемы" width="500px">
      <el-form label-position="top" size="small">
        <el-form-item label="Имя поля (ключ)" required>
          <el-input v-model="newPropName" placeholder="my_field_name" />
        </el-form-item>
        <el-form-item label="Заголовок (title)" required>
          <el-input v-model="newPropTitle" placeholder="Название поля" />
        </el-form-item>
        <el-form-item label="Тип (type)" required>
          <el-select v-model="newPropType" class="w-full">
            <el-option label="Строка (string)" value="string" />
            <el-option label="Текст HTML (string + html)" value="html" />
            <el-option label="Изображение (image)" value="image" />
            <el-option label="Товар (product)" value="product" />
            <el-option label="Ссылка (string + uri)" value="uri" />
            <el-option label="Число целое (integer)" value="integer" />
            <el-option label="ID виджета (integer + widget)" value="widget" />
            <el-option label="Число дробное (number)" value="number" />
            <el-option label="Да/Нет (boolean)" value="boolean" />
            <el-option label="Объект (object)" value="object" />
            <el-option label="Массив объектов (array + object)" value="array_objects" />
            <el-option label="Массив изображений (array + image)" value="array_images" />
            <el-option label="Массив товаров (array + product)" value="array_products" />
            <el-option label="Массив строк (array + string)" value="array_strings" />
            <el-option label="Массив чисел (array + integer)" value="array_integers" />
          </el-select>
        </el-form-item>
        <el-form-item label="Значение по умолчанию">
          <el-input v-model="newPropDefault" placeholder="default" />
        </el-form-item>
        <el-form-item label="Enum (через запятую)">
          <el-input v-model="newPropEnum" placeholder="value1, value2, value3" />
        </el-form-item>
        <el-form-item label="Обязательное поле">
          <el-switch v-model="newPropRequired" />
        </el-form-item>
        <template v-if="newPropType === 'integer' || newPropType === 'number'">
          <el-form-item>
            <template #label>
              <span class="flex items-center gap-1">
                <el-switch v-model="newPropUseMinMax" size="small" />
                Минимум / Максимум
              </span>
            </template>
            <template v-if="newPropUseMinMax">
              <div class="flex gap-2">
                <el-input-number v-model="newPropMin" placeholder="min" class="flex-1" />
                <el-input-number v-model="newPropMax" placeholder="max" class="flex-1" />
              </div>
            </template>
            <span v-else class="text-gray-400 text-xs">Не ограничено</span>
          </el-form-item>
        </template>
      </el-form>
      <template #footer>
        <el-button @click="showAddDialog = false">Отмена</el-button>
        <el-button type="primary" @click="addProperty">Добавить</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, watch, reactive } from 'vue'
import { Delete, Plus } from '@element-plus/icons-vue'
import PropertyAttributesEditor from './PropertyAttributesEditor.vue'

import ItemPropertiesEditor from './ItemPropertiesEditor.vue'

const props = defineProps({
  schema: {
    type: Object,
    default: () => ({ type: 'object', properties: {} })
  },
  modelValue: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue', 'update:schema'])

const localSchema = reactive<Record<string, any>>({
  type: 'object',
  properties: {},
  required: [],
})

// Все поля свернуты по умолчанию
const expandedFields = ref<string[]>([])

// Инициализируем localSchema из пропсов только один раз (при создании)
// Не используем watch, чтобы избежать циклического сброса изменений,
// когда SchemaEditor сам отправляет обновление через emit('update:schema'),
// и Vue обновляет props.schema, что приводило бы к перезаписи localSchema
function initFromProps() {
  localSchema.type = props.schema?.type || 'object'
  localSchema.properties = props.schema?.properties ? JSON.parse(JSON.stringify(props.schema.properties)) : {}
  localSchema.required = props.schema?.required ? [...props.schema.required] : []
}
initFromProps()

const showAddDialog = ref(false)
const newPropName = ref('')
const newPropTitle = ref('')
const newPropType = ref('string')
const newPropEnum = ref('')
const newPropRequired = ref(false)
const newPropDefault = ref('')
const newPropUseMinMax = ref(false)
const newPropMin = ref(0)
const newPropMax = ref(100)

function isRequired(name: string): boolean {
  return localSchema.required?.includes(name) ?? false
}

function emitSchemaUpdate() {
  emit('update:schema', {
    type: 'object',
    properties: JSON.parse(JSON.stringify(localSchema.properties)),
    required: [...(localSchema.required || [])],
  })
}

function removeProperty(name: string) {
  if (isRequired(name)) return
  delete localSchema.properties[name]
  emitSchemaUpdate()
}

function renameProperty(oldName: string, newName: string) {
  if (oldName === newName || !newName) return
  if (localSchema.properties[newName]) return

  const newProperties: Record<string, any> = {}
  for (const key of Object.keys(localSchema.properties)) {
    if (key === oldName) {
      newProperties[newName] = localSchema.properties[oldName]
    } else {
      newProperties[key] = localSchema.properties[key]
    }
  }
  localSchema.properties = newProperties

  if (localSchema.required) {
    const idx = localSchema.required.indexOf(oldName)
    if (idx !== -1) {
      localSchema.required[idx] = newName
    }
  }
  emitSchemaUpdate()
}

function updatePropertyConfig(propName: string, newConfig: any) {
  localSchema.properties[propName] = newConfig
  emitSchemaUpdate()
}

function toggleRequired(propName: string, val: boolean) {
  if (!localSchema.required) localSchema.required = []
  if (val && !localSchema.required.includes(propName)) {
    localSchema.required.push(propName)
  } else if (!val) {
    localSchema.required = localSchema.required.filter((n: string) => n !== propName)
  }
  emitSchemaUpdate()
}

function addProperty() {
  if (!newPropName.value || !newPropTitle.value) return

  const name = newPropName.value
  const title = newPropTitle.value
  const propConfig: Record<string, any> = {
    type: 'string',
    title: title,
  }

  switch (newPropType.value) {
    case 'html':
      propConfig.type = 'string'
      propConfig.format = 'html'
      if (newPropDefault.value) propConfig.default = newPropDefault.value
      break
    case 'image':
      propConfig.type = 'object'
      propConfig.format = 'image'
      propConfig.properties = {
        id: { type: 'integer', title: 'ID изображения' },
        src: { type: 'string', title: 'URL (src)' },
        alt: { type: 'string', title: 'Alt текст' },
        title: { type: 'string', title: 'Title текст' },
        description: { type: 'string', title: 'Описание' },
      }
      break
    case 'product':
      propConfig.type = 'object'
      propConfig.format = 'product'
      propConfig.properties = {
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
      break
    case 'uri':
      propConfig.type = 'string'
      propConfig.format = 'uri'
      break
    case 'widget':
      propConfig.type = 'integer'
      propConfig.format = 'widget'
      break
    case 'integer':
      propConfig.type = 'integer'
      if (newPropUseMinMax.value) {
        propConfig.minimum = newPropMin.value
        propConfig.maximum = newPropMax.value
      }
      if (newPropDefault.value) propConfig.default = parseInt(newPropDefault.value, 10)
      break
    case 'number':
      propConfig.type = 'number'
      if (newPropUseMinMax.value) {
        propConfig.minimum = newPropMin.value
        propConfig.maximum = newPropMax.value
      }
      if (newPropDefault.value) propConfig.default = parseFloat(newPropDefault.value)
      break
    case 'boolean':
      propConfig.type = 'boolean'
      if (newPropDefault.value) propConfig.default = newPropDefault.value === 'true' || newPropDefault.value === '1'
      break
    case 'object':
      propConfig.type = 'object'
      propConfig.properties = {}
      break
    case 'array_objects':
      propConfig.type = 'array'
      propConfig.items = {
        type: 'object',
        properties: {
          title: { type: 'string', title: 'Заголовок' },
        },
      }
      break
    case 'array_images':
      propConfig.type = 'array'
      propConfig.items = {
        type: 'object',
        format: 'image',
        properties: {
          id: { type: 'integer', title: 'ID изображения' },
          src: { type: 'string', title: 'URL (src)' },
          alt: { type: 'string', title: 'Alt текст' },
          title: { type: 'string', title: 'Title текст' },
          description: { type: 'string', title: 'Описание' },
        },
      }
      break
    case 'array_products':
      propConfig.type = 'array'
      propConfig.items = {
        type: 'object',
        format: 'product',
        properties: {
          id: { type: 'integer', title: 'ID товара' },
          name: { type: 'string', title: 'Название' },
          url: { type: 'string', title: 'URL' },
          short: { type: 'string', title: 'Краткое описание' },
          price: { type: 'number', title: 'Цена' },
          image_src: { type: 'string', title: 'URL изображения' },
          image_alt: { type: 'string', title: 'Alt изображения' },
          image_next_src: { type: 'string', title: 'URL второго изображения' },
          image_next_alt: { type: 'string', title: 'Alt второго изображения' },
        },
      }
      break
    case 'array_strings':
      propConfig.type = 'array'
      propConfig.items = { type: 'string' }
      break
    case 'array_integers':
      propConfig.type = 'array'
      propConfig.items = { type: 'integer' }
      break
    default:
      if (newPropEnum.value) {
        propConfig.enum = newPropEnum.value.split(',').map((s: string) => s.trim())
      }
      if (newPropDefault.value) propConfig.default = newPropDefault.value
      break
  }

  localSchema.properties[name] = propConfig

  // Раскрываем новое поле
  expandedFields.value.push(name)

  if (newPropRequired.value) {
    if (!localSchema.required) localSchema.required = []
    if (!localSchema.required.includes(name)) {
      localSchema.required.push(name)
    }
  }

  newPropName.value = ''
  newPropTitle.value = ''
  newPropType.value = 'string'
  newPropEnum.value = ''
  newPropRequired.value = false
  newPropDefault.value = ''
  newPropUseMinMax.value = false
  newPropMin.value = 0
  newPropMax.value = 100
  showAddDialog.value = false
  emitSchemaUpdate()
}
</script>

<style scoped>
.schema-collapse :deep(.el-collapse-item__header) {
  padding-left: 12px;
  background: #f9fafb;
  border-radius: 8px;
}
.schema-collapse :deep(.el-collapse-item__wrap) {
  border-radius: 0 0 8px 8px;
}
.schema-collapse :deep(.el-collapse-item__content) {
  padding: 12px;
}
</style>
