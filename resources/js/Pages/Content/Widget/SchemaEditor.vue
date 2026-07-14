<template>
  <div class="schema-editor">
    <p class="text-gray-500 text-sm mb-3" v-if="!localSchema?.properties || Object.keys(localSchema.properties).length === 0">
      Схема пуста. Добавьте поля через кнопку ниже.
    </p>

    <div v-if="localSchema?.properties && Object.keys(localSchema.properties).length > 0">
      <div
        v-for="(propConfig, propName) in localSchema.properties"
        :key="propName"
        class="schema-field mb-4 p-4 border rounded-lg bg-gray-50"
      >
        <!-- Заголовок поля схемы -->
        <div class="flex items-center justify-between mb-2">
          <label class="font-medium text-sm">
            {{ propConfig.title || propName }}
            <span v-if="isRequired(propName)" class="text-red-500 ml-1">*</span>
          </label>
          <div class="flex gap-1">
            <el-button size="small" type="danger" plain @click="removeProperty(propName)" :disabled="isRequired(propName)">
              <el-icon><Delete /></el-icon>
            </el-button>
          </div>
        </div>

        <!-- Редактор атрибутов поля схемы -->
        <PropertyAttributesEditor
          :prop-name="propName"
          :prop-config="propConfig"
          :required="isRequired(propName)"
          @update:prop-config="(newConfig) => updatePropertyConfig(propName, newConfig)"
          @update:required="(val) => toggleRequired(propName, val)"
        />
      </div>
    </div>

    <!-- Кнопка добавить поле -->
    <div class="mt-4">
      <el-button type="primary" plain @click="showAddDialog = true">
        <el-icon><Plus /></el-icon>
        Добавить поле
      </el-button>
    </div>

    <!-- Диалог добавления поля -->
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
            <el-option label="Изображение (string + uuid)" value="uuid" />
            <el-option label="Ссылка (string + uri)" value="uri" />
            <el-option label="Число целое (integer)" value="integer" />
            <el-option label="Число дробное (number)" value="number" />
            <el-option label="Да/Нет (boolean)" value="boolean" />
            <el-option label="Массив объектов (array + object)" value="array_objects" />
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
        <el-form-item v-if="newPropType === 'integer' || newPropType === 'number'" label="Минимум (minimum)">
          <el-input-number v-model="newPropMin" :min="0" />
        </el-form-item>
        <el-form-item v-if="newPropType === 'integer' || newPropType === 'number'" label="Максимум (maximum)">
          <el-input-number v-model="newPropMax" :min="0" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="showAddDialog = false">Отмена</el-button>
        <el-button type="primary" @click="addProperty">Добавить</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, watch, reactive, computed } from 'vue'
import { Delete, Plus } from '@element-plus/icons-vue'
import PropertyAttributesEditor from './PropertyAttributesEditor.vue'

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

// Локальная копия схемы
const localSchema = reactive<Record<string, any>>({
  type: 'object',
  properties: {},
  required: [],
})

watch(() => props.schema, (val) => {
  localSchema.type = val?.type || 'object'
  localSchema.properties = val?.properties ? JSON.parse(JSON.stringify(val.properties)) : {}
  localSchema.required = val?.required ? [...val.required] : []
}, { immediate: true, deep: true })

// Состояние для диалога добавления
const showAddDialog = ref(false)
const newPropName = ref('')
const newPropTitle = ref('')
const newPropType = ref('string')
const newPropEnum = ref('')
const newPropRequired = ref(false)
const newPropDefault = ref('')
const newPropMin = ref(0)
const newPropMax = ref(100)

function isRequired(name: string): boolean {
  return localSchema.required?.includes(name) ?? false
}

function emitSchemaUpdate() {
  emit('update:schema', {
    type: 'object',
    properties: { ...localSchema.properties },
    required: [...(localSchema.required || [])],
  })
}

function removeProperty(name: string) {
  if (isRequired(name)) return
  delete localSchema.properties[name]
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
    case 'uuid':
      propConfig.type = 'string'
      propConfig.format = 'uuid'
      break
    case 'uri':
      propConfig.type = 'string'
      propConfig.format = 'uri'
      break
    case 'integer':
      propConfig.type = 'integer'
      propConfig.minimum = newPropMin.value
      propConfig.maximum = newPropMax.value
      if (newPropDefault.value) propConfig.default = parseInt(newPropDefault.value, 10)
      break
    case 'number':
      propConfig.type = 'number'
      propConfig.minimum = newPropMin.value
      propConfig.maximum = newPropMax.value
      if (newPropDefault.value) propConfig.default = parseFloat(newPropDefault.value)
      break
    case 'boolean':
      propConfig.type = 'boolean'
      if (newPropDefault.value) propConfig.default = newPropDefault.value === 'true' || newPropDefault.value === '1'
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
    case 'array_strings':
      propConfig.type = 'array'
      propConfig.items = { type: 'string' }
      break
    case 'array_integers':
      propConfig.type = 'array'
      propConfig.items = { type: 'integer' }
      break
    default:
      // string
      if (newPropEnum.value) {
        propConfig.enum = newPropEnum.value.split(',').map((s: string) => s.trim())
      }
      if (newPropDefault.value) propConfig.default = newPropDefault.value
      break
  }

  localSchema.properties[name] = propConfig

  if (newPropRequired.value) {
    if (!localSchema.required) localSchema.required = []
    if (!localSchema.required.includes(name)) {
      localSchema.required.push(name)
    }
  }

  // Сброс
  newPropName.value = ''
  newPropTitle.value = ''
  newPropType.value = 'string'
  newPropEnum.value = ''
  newPropRequired.value = false
  newPropDefault.value = ''
  newPropMin.value = 0
  newPropMax.value = 100
  showAddDialog.value = false
  emitSchemaUpdate()
}
</script>

<style scoped>
.schema-field {
  transition: all 0.2s;
}
.schema-field:hover {
  border-color: #409eff;
}
</style>
