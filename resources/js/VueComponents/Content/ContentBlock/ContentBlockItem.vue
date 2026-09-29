<template>
    <div class="border rounded-lg bg-white shadow-sm" :data-id="block.id">
        <!-- Заголовок блока (всегда виден) -->
        <div
            class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none"
            @click="onToggle"
        >
            <span class="drag-handle cursor-grab text-gray-400 hover:text-gray-600">
                ⠿
            </span>

            <el-icon class="text-gray-400" :class="{ 'rotate-90': collapsed }">
                <i :class="collapsed ? 'fa-light fa-chevron-right' : 'fa-light fa-chevron-down'" />
            </el-icon>

            <span class="text-sm font-medium text-gray-600">
                Блок #{{ index + 1 }}
            </span>

            <span class="text-sm text-gray-700 truncate max-w-[200px]" :title="block.caption || ''">
                {{ block.caption || '' }}
            </span>

            <el-tag v-if="block.section" size="small" type="info">
                {{ block.section }}
            </el-tag>

            <el-tag v-if="block.widgetInstance" size="small" type="success">
                {{ block.widgetInstance.widgetName || `Виджет #${block.widgetInstance.id}` }}
            </el-tag>

            <div class="ml-auto flex items-center gap-2" @click.stop>
                <el-switch
                    :model-value="block.active !== false"
                    size="small"
                    active-text=""
                    inactive-text=""
                    @change="onToggleActive"
                />
                <el-button size="small" type="primary" link @click="$emit('edit', block.id)">
                    Изменить
                </el-button>
                <el-button size="small" type="primary" link @click="$emit('copy', block.id)">
                    Копировать
                </el-button>
                <el-button size="small" type="danger" text @click="$emit('delete', block.id)">
                    Удалить блок
                </el-button>
            </div>
        </div>

        <!-- Контент блока (сворачивается) -->
        <div v-show="!collapsed" class="border-t px-3 py-3">
            <!-- Есть виджет — показываем форму его полей -->
            <div v-if="block.widgetInstance" class="widget-container">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-500">
                        Виджет: <strong>{{ block.widgetInstance.widgetName }}</strong>
                        (ID: {{ block.widgetInstance.id }})
                    </span>
                    <div class="flex gap-2">
                        <el-button
                            size="small"
                            type="danger"
                            plain
                            @click="$emit('removeWidget', block.id)"
                        >
                            Удалить виджет
                        </el-button>
                    </div>
                </div>

                <!-- Загрузка формы -->
                <div v-if="formLoading" class="flex items-center justify-center py-4">
                    <el-icon class="is-loading" :size="20">
                        <i class="fa-light fa-spinner" />
                    </el-icon>
                    <span class="ml-2 text-sm text-gray-400">Загрузка полей...</span>
                </div>

                <!-- Ошибка загрузки -->
                <div v-else-if="formError" class="text-sm text-red-500 py-2">
                    {{ formError }}
                    <el-button size="small" text type="primary" @click="loadForm">Повторить</el-button>
                </div>

                <!-- Рендер полей формы -->
                <WidgetFieldRenderer
                    v-else-if="formFields.length > 0"
                    ref="fieldRendererRef"
                    :fields="formFields"
                    :saving="saving"
                    :showSaveButton="true"
                    @save="onSaveParams"
                    @select-nested-widget="onSelectNestedWidget"
                    @cascading-save="onCascadingSave"
                />

                <el-empty v-else description="Нет полей для настройки" />
            </div>

            <!-- Нет виджета — показываем кнопку добавить -->
            <div v-else class="flex items-center justify-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                <el-button
                    type="primary"
                    size="default"
                    @click="$emit('addWidget', block.id)"
                >
                    + Добавить Виджет
                </el-button>
            </div>
        </div>

        <!-- Диалог выбора экземпляра виджета для вложенного поля -->
        <WidgetInstanceSelectorDialog
            :visible="showInstanceSelector"
            @close="showInstanceSelector = false"
            @select="onNestedWidgetInstanceSelected"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useContentBlock, type WidgetFormFieldData } from '@Res/composables/useContentBlock'
import WidgetFieldRenderer from './WidgetFieldRenderer.vue'
import WidgetInstanceSelectorDialog from './WidgetInstanceSelectorDialog.vue'

const props = defineProps<{
    block: any
    index: number
    collapsed: boolean
}>()

const emit = defineEmits<{
    (e: 'toggle', id: number): void
    (e: 'delete', id: number): void
    (e: 'addWidget', id: number): void
    (e: 'removeWidget', id: number): void
    (e: 'edit', id: number): void
    (e: 'copy', id: number): void
    (e: 'toggleActive', id: number, active: boolean): void
}>()

const { getWidgetInstance, updateWidgetInstance } = useContentBlock()

const formFields = ref<WidgetFormFieldData[]>([])
const formLoading = ref(false)
const formError = ref<string | null>(null)
const saving = ref(false)
const formLoaded = ref(false)

// --- Вложенный экземпляр виджета ---
const fieldRendererRef = ref<any>(null)
const nestedWidgetFieldName = ref<string | null>(null)
const showInstanceSelector = ref(false)

/**
 * Загрузить поля формы для виджета.
 */
async function loadForm() {
    const instanceId = props.block.widgetInstance?.id
    if (!instanceId) return

    formLoading.value = true
    formError.value = null
    formLoaded.value = false

    try {
        const data = await getWidgetInstance(instanceId)
        formFields.value = data.fields || []
        formLoaded.value = true
    } catch (e: any) {
        formError.value = e?.message || 'Не удалось загрузить поля виджета'
    } finally {
        formLoading.value = false
    }
}

/**
 * Сохранить параметры виджета (только родитель).
 */
async function onSaveParams(params: Record<string, any>) {
    const instanceId = props.block.widgetInstance?.id
    if (!instanceId) return
    saving.value = true
    try {
        const data = await updateWidgetInstance(instanceId, { params })
        formFields.value = data.fields || []
    } catch (e: any) {
        console.error('Ошибка сохранения:', e)
    } finally {
        saving.value = false
    }
}

/**
 * === Вариант 2 (каскадное сохранение): сначала дети, потом родитель ===
 * Вызывается из WidgetFieldRenderer по кнопке "Сохранить всё".
 */
async function onCascadingSave(
    parentParams: Record<string, any>,
    childInstances: Array<{ id: number; params: Record<string, any> }>,
) {
    const instanceId = props.block.widgetInstance?.id
    if (!instanceId) return

    saving.value = true
    try {
        // 1. Сначала сохраняем всех детей
        for (const child of childInstances) {
            await updateWidgetInstance(child.id, { params: child.params })
        }

        // 2. Затем сохраняем родителя
        const data = await updateWidgetInstance(instanceId, { params: parentParams })
        formFields.value = data.fields || []
    } catch (e: any) {
        console.error('Ошибка каскадного сохранения:', e)
    } finally {
        saving.value = false
    }
}

/**
 * Открыть диалог выбора экземпляра для вложенного поля виджета.
 */
function onSelectNestedWidget(fieldName: string) {
    nestedWidgetFieldName.value = fieldName
    showInstanceSelector.value = true
}

/**
 * Получить выбранный экземпляр из диалога и установить в поле формы.
 */
async function onNestedWidgetInstanceSelected(instance: any) {
    showInstanceSelector.value = false

    // Сохраняем имя поля ДО сброса — оно нужно для записи значения и params
    const fieldName = nestedWidgetFieldName.value
    if (fieldName === null) return
    nestedWidgetFieldName.value = null

    const instanceId = props.block.widgetInstance?.id

    if (!instanceId) {
        // Родителя нет — просто ставим значение локально
        fieldRendererRef.value?.setFieldValue(fieldName, {
            id: instance.id,
            title: instance.title,
            widgetName: instance.widgetName,
            widgetId: instance.widgetId,
            fields: instance.fields || [],
        })
        return
    }

    // Сохраняем родительскую форму, чтобы ID дочернего экземпляра попал в params
    saving.value = true
    try {
        // Собираем актуальные params из formModel, преобразуя widget-поля в ID
        const raw = fieldRendererRef.value?.formModel
            ? JSON.parse(JSON.stringify(fieldRendererRef.value.formModel))
            : {}
        const params: Record<string, any> = {}
        for (const [key, val] of Object.entries(raw)) {
            if (val && typeof val === 'object' && !Array.isArray(val) && val.id && val.widgetId) {
                params[key] = val.id
            } else {
                params[key] = val
            }
        }
        // Гарантируем, что выбранное поле хранит именно ID экземпляра
        params[fieldName] = instance.id

        // updateWidgetInstance уже возвращает обновлённую форму родителя —
        // используем её напрямую, без повторного GET-запроса loadForm()
        const data = await updateWidgetInstance(instanceId, { params })
        formFields.value = data.fields || []

        // Обновляем вложенное поле, чтобы сразу отобразились поля дочернего виджета
        const widgetField = (data.fields || []).find(
            (f: WidgetFormFieldData) => f.name === fieldName && f.format === 'widget',
        )
        fieldRendererRef.value?.setFieldValue(fieldName, widgetField?.value ?? {
            id: instance.id,
            title: instance.title,
            widgetName: instance.widgetName,
            widgetId: instance.widgetId,
            fields: instance.fields || [],
        })
    } catch (e) {
        console.error('Ошибка сохранения при выборе вложенного виджета:', e)
    } finally {
        saving.value = false
    }
}

/**
 * При разворачивании — загружаем форму.
 */
function onToggle() {
    // Если блок разворачивается и есть виджет — загружаем поля
    if (props.collapsed && props.block.widgetInstance) {
        loadForm()
    }
    emit('toggle', props.block.id)
}

// Следим за block.widgetInstance — если он появился (добавили виджет), загружаем поля
watch(() => props.block.widgetInstance, (newVal) => {
    if (newVal && !props.collapsed) {
        // Блок развёрнут и появился виджет — сразу загружаем
        loadForm()
    }
})

/**
 * Переключить active у блока.
 */
function onToggleActive(val: boolean) {
    emit('toggleActive', props.block.id, val)
}
</script>

<style scoped>
.drag-handle {
    cursor: grab;
}
.drag-handle:active {
    cursor: grabbing;
}
.rotate-90 {
    transform: rotate(90deg);
}
</style>
