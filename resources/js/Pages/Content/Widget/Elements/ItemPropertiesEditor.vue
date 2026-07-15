<template>
  <div class="item-props-editor">
    <el-button size="small" type="primary" plain @click="$emit('add')" class="mb-2">
      <el-icon><Plus /></el-icon>
      Добавить поле
    </el-button>

    <el-collapse v-model="expandedItems" class="item-props-collapse">
      <el-collapse-item
        v-for="(subProp, subName) in properties"
        :key="subName"
        :name="subName"
      >
        <template #title>
          <div class="flex items-center justify-between w-full pr-2">
            <span class="text-xs font-medium">{{ subProp.title || subName }}</span>
            <el-tag size="small" type="info">{{ subProp.type }}</el-tag>
          </div>
        </template>

        <div class="item-prop-fields">
          <el-form :label-width="80" size="small">
            <el-form-item label="Ключ">
              <el-input
                :model-value="localEditableKeys[subName] ?? subName"
                size="small"
                @input="(v) => onKeyInput(subName, v)"
                @blur="() => onKeyBlur(subName)"
              />
            </el-form-item>
            <el-form-item label="title">
              <el-input
                :model-value="subProp.title"
                size="small"
                @input="(v) => $emit('updateProp', subName, 'title', v)"
              />
            </el-form-item>
            <el-form-item label="type">
              <el-select
                :model-value="subProp.type"
                size="small"
                class="w-full"
                @change="(v) => $emit('updateProp', subName, 'type', v)"
              >
                <el-option label="string" value="string" />
                <el-option label="integer" value="integer" />
                <el-option label="number" value="number" />
                <el-option label="boolean" value="boolean" />
              </el-select>
            </el-form-item>
            <el-form-item label="format">
              <el-select
                :model-value="subProp.format"
                size="small"
                class="w-full"
                clearable
                @change="(v) => $emit('updateProp', subName, 'format', v)"
              >
                <el-option label="—" value="" />
                <el-option label="html" value="html" />
                <el-option label="uuid" value="uuid" />
                <el-option label="uri" value="uri" />
                <el-option label="color" value="color" />
              </el-select>
            </el-form-item>
            <el-form-item label="default">
              <el-input
                :model-value="subProp.default"
                size="small"
                @input="(v) => $emit('updateProp', subName, 'default', v)"
              />
            </el-form-item>
            <el-form-item v-if="subProp.type === 'integer' || subProp.type === 'number'" label="min">
              <el-input-number
                :model-value="subProp.minimum"
                size="small"
                :min="0"
                class="w-full"
                @change="(v) => $emit('updateProp', subName, 'minimum', v)"
              />
            </el-form-item>
            <el-form-item v-if="subProp.type === 'integer' || subProp.type === 'number'" label="max">
              <el-input-number
                :model-value="subProp.maximum"
                size="small"
                :min="0"
                class="w-full"
                @change="(v) => $emit('updateProp', subName, 'maximum', v)"
              />
            </el-form-item>
            <el-button
              size="small"
              type="danger"
              plain
              @click="$emit('remove', subName)"
              class="mt-1"
            >
              <el-icon><Delete /></el-icon>
              Удалить поле
            </el-button>
          </el-form>
        </div>
      </el-collapse-item>
    </el-collapse>

    <p v-if="Object.keys(properties).length === 0" class="text-gray-400 text-xs mt-2">
      Нет полей. Нажмите "Добавить поле".
    </p>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, reactive, watch } from 'vue'
import { Plus, Delete } from '@element-plus/icons-vue'

const props = defineProps({
  properties: { type: Object, required: true },
  editableKeys: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['add', 'remove', 'updateKey', 'updateProp'])

// Все дочерние поля свернуты по умолчанию
const expandedItems = ref<string[]>([])

const localEditableKeys = reactive<Record<string, string>>({})

watch(() => props.editableKeys, (val) => {
  Object.assign(localEditableKeys, { ...val })
}, { immediate: true, deep: true })

function onKeyInput(subName: string, value: string) {
  localEditableKeys[subName] = value
}

function onKeyBlur(subName: string) {
  const newName = localEditableKeys[subName]?.trim()
  if (newName && newName !== subName) {
    emit('updateKey', subName, newName)
  }
}
</script>

<style scoped>
.item-props-editor {
  width: 100%;
}
.item-prop-fields {
  padding: 8px;
}
.item-props-collapse :deep(.el-collapse-item__header) {
  padding-left: 8px;
  background: #f9fafb;
  border-radius: 6px;
  font-size: 12px;
}
.item-props-collapse :deep(.el-collapse-item__wrap) {
  border-radius: 0 0 6px 6px;
}
.item-props-collapse :deep(.el-collapse-item__content) {
  padding: 8px;
}
</style>
