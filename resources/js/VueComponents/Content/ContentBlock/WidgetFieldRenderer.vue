<template>
    <div class="widget-field-renderer">
        <el-form
            v-if="fields.length > 0"
            :model="formModel"
            size="small"
            label-position="top"
        >
            <!-- Поля на всю ширину (string без формата, long text, html) -->
            <div class="fullwidth-fields">
                <WidgetFieldSimple
                    v-for="field in fullwidthFields"
                    :key="field.name"
                    :field="field"
                    :model-value="formModel[field.name]"
                    :disabled="disabled"
                    variant="fullwidth"
                    @update:model-value="(val) => onFieldChange(field.name, val)"
                />
            </div>

            <!-- Компактные поля в ряд (все остальные) -->
            <div class="compact-fields">
                <WidgetFieldSimple
                    v-for="field in compactFields"
                    :key="field.name"
                    :field="field"
                    :model-value="formModel[field.name]"
                    :disabled="disabled"
                    variant="compact"
                    @update:model-value="(val) => onFieldChange(field.name, val)"
                />
            </div>

            <!-- Составные поля: array с nestedFields, object с nestedFields, widget -->
            <div class="composite-fields">
                <template v-for="field in compositeFields" :key="field.name">
                    <!-- array с nestedFields (массив объектов) -->
                    <el-form-item
                        v-if="field.type === 'array' && field.nestedFields"
                        :label="field.label"
                        :required="field.required"
                        :prop="field.name"
                    >
                        <WidgetFieldArray
                            :field="field"
                            :model-value="formModel[field.name]"
                            :disabled="disabled"
                            @update:model-value="(val) => onFieldChange(field.name, val)"
                        />
                    </el-form-item>

                    <!-- object с nestedFields -->
                    <el-form-item
                        v-else-if="field.type === 'object' && field.nestedFields"
                        :label="field.label"
                        :required="field.required"
                        :prop="field.name"
                    >
                        <WidgetFieldObject
                            :field="field"
                            :model-value="formModel[field.name]"
                            :disabled="disabled"
                            @update:model-value="(val) => onObjectFieldChange(field.name, val)"
                        />
                    </el-form-item>

                    <!-- widget — вложенный виджет (сворачиваемый блок) -->
                    <el-form-item
                        v-else-if="field.format === 'widget'"
                        :label="field.label"
                        :required="field.required"
                        class="nested-widget-form-item"
                    >
                        <WidgetFieldNestedWidget
                            :field="field"
                            :model-value="formModel[field.name]"
                            :disabled="disabled"
                            :register-renderer="(el: any) => registerNestedRenderer(field.name, el)"
                            @update:model-value="(val) => onFieldChange(field.name, val)"
                            @select-nested-widget="(fieldName: string) => emit('select-nested-widget', fieldName)"
                        />
                    </el-form-item>
                </template>
            </div>

            <!-- Кнопка "Сохранить всё" (родитель + все дети) -->
            <div class="mt-4">
                <el-button
                    v-if="!disabled && showSaveButton"
                    type="success"
                    :loading="cascadingSaving"
                    @click="onCascadingSave"
                >
                    Сохранить всё
                </el-button>
            </div>
        </el-form>

        <el-empty v-else description="Нет полей для настройки" />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'
import WidgetFieldSimple from './WidgetFieldSimple.vue'
import WidgetFieldArray from './WidgetFieldArray.vue'
import WidgetFieldObject from './WidgetFieldObject.vue'
import WidgetFieldNestedWidget from './WidgetFieldNestedWidget.vue'

const props = defineProps<{
    fields: WidgetFormFieldData[]
    disabled?: boolean
    saving?: boolean
    showSaveButton?: boolean
}>()

const emit = defineEmits<{
    (e: 'save', params: Record<string, any>): void
    (e: 'select-nested-widget', fieldName: string): void
    (e: 'cascading-save', parentParams: Record<string, any>, childInstances: Array<{ id: number; params: Record<string, any> }>): void
}>()

/** Программно установить значение поля (для внешних вызовов из диалогов) */
function setFieldValue(name: string, value: any) {
    formModel[name] = value
}

const formModel = reactive<Record<string, any>>({})

defineExpose({ setFieldValue, formModel })

/**
 * Храним сигнатуру полей (имена + значения на момент инициализации),
 * чтобы отслеживать только полную смену набора полей (например, при открытии другого виджета),
 * а не ререндеры или обновления после сохранения.
 */
const fieldsSignature = ref('')

// Инициализируем модель из полей — только при реальном изменении набора полей
watch(() => props.fields, (fields) => {
    const newSignature = fields.map(f => f.name).sort().join(',')

    // Если сигнатура не изменилась — не сбрасываем пользовательские изменения
    if (newSignature === fieldsSignature.value) return

    fieldsSignature.value = newSignature

    // Очищаем старые ключи, которых больше нет
    const currentNames = new Set(fields.map(f => f.name))
    for (const key of Object.keys(formModel)) {
        if (!currentNames.has(key)) {
            delete formModel[key]
        }
    }

    // Заполняем модель из новых полей
    for (const field of fields) {
        if (field.type === 'object' && field.nestedFields) {
            formModel[field.name] = field.value && typeof field.value === 'object' && !Array.isArray(field.value)
                ? { ...field.value }
                : {}
            // Для product_group гарантируем наличие limit (максимум товаров)
            if (field.format === 'product_group' && formModel[field.name].limit === undefined) {
                formModel[field.name].limit = 0
            }
        } else if (field.type === 'array' && field.nestedFields) {
            formModel[field.name] = Array.isArray(field.value) ? [...field.value] : []
        } else if (field.format === 'widget') {
            // Для поля виджета — value это объект {id, title, widgetName, widgetId, fields}
            formModel[field.name] = field.value && typeof field.value === 'object' && field.value !== null
                ? { ...field.value }
                : { id: null, fields: [] }
        } else {
            formModel[field.name] = field.value !== undefined && field.value !== null
                ? field.value
                : field.default ?? null
        }
    }
}, { immediate: true, deep: false })

/**
 * Поля на всю ширину — string без форматов (кроме select/enum) и html
 */
const fullwidthFields = computed(() => {
    return props.fields.filter(f => {
        if (f.format === 'html') return true
        if (f.type === 'text' || (f.type === 'string' && f.value && typeof f.value === 'string' && f.value.length > 80)) return true
        if (f.type === 'string' && !f.options && !f.format) return true
        return false
    })
})

/**
 * Компактные поля — всё остальное, что не fullwidth и не composite
 */
const compactFields = computed(() => {
    return props.fields.filter(f => {
        if (fullwidthFields.value.includes(f)) return false
        if (f.nestedFields) return false
        if (f.format === 'widget') return false
        return true
    })
})

/**
 * Составные поля — object/array с nestedFields или format='widget'
 */
const compositeFields = computed(() => {
    return props.fields.filter(f => f.nestedFields || f.format === 'widget')
})

/**
 * Поля с format='widget' (для каскадного сохранения)
 */
const widgetFields = computed(() => {
    return props.fields.filter(f => f.format === 'widget')
})

// --- Вложенные рендереры дочерних виджетов ---
const nestedRenderers = ref<Record<string, any>>({})
const cascadingSaving = ref(false)

function registerNestedRenderer(fieldName: string, el: any) {
    if (el) {
        nestedRenderers.value[fieldName] = el
    }
}

/** Обычное присвоение значения полю (простые поля, массивы, вложенные виджеты) */
function onFieldChange(name: string, value: any) {
    formModel[name] = value
}

/** Присвоение значения объектному полю: null означает удаление ключа */
function onObjectFieldChange(name: string, value: any) {
    if (value === null) {
        delete formModel[name]
    } else {
        formModel[name] = value
    }
}

/**
 * Если это вложенный рендерер (без кнопки сохранения), эмитим save при любом изменении модели,
 * чтобы родительский компонент получил обновлённые вложенные данные.
 */
watch(formModel, () => {
    if (!props.showSaveButton) {
        const snapshot = JSON.parse(JSON.stringify(formModel))
        emit('save', snapshot)
    }
}, { deep: true })

/**
 * Собрать params для родителя — преобразовать format:'widget' обратно в ID
 */
function buildParentParamsSnapshot(): Record<string, any> {
    const snapshot = JSON.parse(JSON.stringify(formModel))

    for (const field of props.fields) {
        if (field.format === 'widget') {
            const val = snapshot[field.name]
            if (val && typeof val === 'object' && 'id' in val) {
                snapshot[field.name] = val.id
            } else {
                snapshot[field.name] = null
            }
        }
    }

    return snapshot
}

/**
 * Получить список дочерних экземпляров с их params для каскадного сохранения
 */
function getChildInstancesToSave(): Array<{ id: number; params: Record<string, any> }> {
    const children: Array<{ id: number; params: Record<string, any> }> = []

    for (const field of widgetFields.value) {
        const val = formModel[field.name]
        if (val && typeof val === 'object' && val.id) {
            const childRenderer = nestedRenderers.value[field.name]
            let childParams: Record<string, any> = {}

            if (childRenderer?.formModel) {
                childParams = JSON.parse(JSON.stringify(childRenderer.formModel))
            }

            children.push({
                id: val.id,
                params: childParams,
            })
        }
    }

    return children
}

/**
 * Каскадное сохранение: эмитит событие cascading-save.
 * ContentBlockItem обработает: сначала дети, потом родитель.
 */
function onCascadingSave() {
    const children = getChildInstancesToSave()
    const parentSnapshot = buildParentParamsSnapshot()

    emit('cascading-save', parentSnapshot, children)
}
</script>

<style scoped>
/* Компактные поля — в ряд */
.compact-fields {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    margin-bottom: 16px;
}

/* Составные поля */
.composite-fields {
    margin-bottom: 16px;
}
.composite-fields :deep(.el-form-item) {
    display: block;
    margin-bottom: 16px;
}
</style>
