<template>
    <div class="nested-widget-block border rounded-lg bg-white shadow-sm w-full">
        <!-- Шапка блока (всегда видна) -->
        <div
            class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none"
            @click="collapsed = !collapsed"
        >
            <el-icon class="text-gray-400" :class="{ 'rotate-90': collapsed }">
                <i :class="collapsed ? 'fa-light fa-chevron-right' : 'fa-light fa-chevron-down'" />
            </el-icon>

            <span class="text-sm font-medium text-gray-600">
                {{ field.label || field.name }}
            </span>

            <el-tag v-if="modelValue?.widgetName" size="small" type="success">
                {{ modelValue.widgetName }}
            </el-tag>

            <div class="ml-auto flex items-center gap-2" @click.stop>
                <template v-if="modelValue?.id">
                    <el-button size="small" @click="openSelector">
                        Заменить
                    </el-button>
                    <el-button size="small" type="danger" text @click="removeInstance">
                        Удалить
                    </el-button>
                </template>
                <el-button
                    v-else
                    size="small"
                    type="primary"
                    @click="openSelector"
                >
                    + Выбрать
                </el-button>
            </div>
        </div>

        <!-- Разворачиваемая часть — поля дочернего виджета -->
        <div v-show="!collapsed" class="border-t px-3 py-3">
            <div v-if="modelValue?.fields?.length > 0">
                <WidgetFieldRenderer
                    :key="'nested-widget-' + field.name"
                    :ref="registerRenderer"
                    :fields="modelValue.fields"
                    :disabled="disabled"
                    :showSaveButton="false"
                    @save="onNestedSave"
                />
            </div>
            <div v-else class="text-gray-400 text-xs py-2">
                Выберите экземпляр виджета для настройки
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'
import WidgetFieldRenderer from './WidgetFieldRenderer.vue'

const props = defineProps<{
    field: WidgetFormFieldData
    modelValue: any
    disabled?: boolean
    registerRenderer?: (el: any) => void
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void
    (e: 'select-nested-widget', fieldName: string): void
}>()

const collapsed = ref(true)

function openSelector() {
    emit('select-nested-widget', props.field.name)
}

function removeInstance() {
    emit('update:modelValue', null)
}

/**
 * Обработчик данных дочернего виджета — данные уже синхронизированы
 * через formModel дочернего рендерера, здесь ничего делать не нужно.
 */
function onNestedSave(vals: Record<string, any>) {
    // no-op
}
</script>

<style scoped>
.nested-widget-block {
    border: 1px solid #e5e7eb;
}
.nested-widget-block:hover {
    border-color: #d1d5db;
}
.rotate-90 {
    transform: rotate(90deg);
}
</style>
