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
                <template v-for="field in fullwidthFields" :key="field.name">
                    <el-form-item
                        :label="field.label"
                        :required="field.required"
                        :prop="field.name"
                    >
                        <!-- html — редактор HTML-кода -->
                        <HtmlEditor
                            v-if="field.format === 'html'"
                            :model-value="formModel[field.name] || ''"
                            @update:model-value="(val) => formModel[field.name] = val"
                            :disabled="disabled"
                            :placeholder="field.label"
                            :height="300"
                        />
                        <!-- textarea если длинное значение -->
                        <el-input
                            v-else-if="isLongText(field)"
                            v-model="formModel[field.name]"
                            type="textarea"
                            :rows="4"
                            :disabled="disabled"
                            :placeholder="field.label"
                        />
                        <!-- обычное строковое поле -->
                        <el-input
                            v-else
                            v-model="formModel[field.name]"
                            :disabled="disabled"
                            :placeholder="field.label"
                            @input="onFieldChange(field.name, $event)"
                        />
                    </el-form-item>
                </template>
            </div>
            <!-- Компактные поля в ряд (все остальные) -->
            <div class="compact-fields">
                <div class="compact-row" v-for="field in compactFields" :key="field.name">
                    <el-form-item
                        :label="field.label"
                        :required="field.required"
                        :prop="field.name"
                    >
                        <!-- color -->
                        <el-color-picker
                            v-if="field.format === 'color'"
                            v-model="formModel[field.name]"
                            :disabled="disabled"
                        />

                        <!-- enum / select -->
                        <el-select
                            v-else-if="field.options && field.options.length > 0"
                            v-model="formModel[field.name]"
                            :disabled="disabled"
                            :multiple="field.type === 'array'"
                            clearable
                        >
                            <el-option
                                v-for="opt in field.options"
                                :key="opt"
                                :label="opt"
                                :value="opt"
                            />
                        </el-select>

                        <!-- boolean -->
                        <el-switch
                            v-else-if="field.type === 'boolean'"
                            v-model="formModel[field.name]"
                            :disabled="disabled"
                        />

                        <!-- number / integer -->
                        <el-input-number
                            v-else-if="field.type === 'integer' || field.type === 'number'"
                            v-model="formModel[field.name]"
                            :disabled="disabled"
                            :min="0"
                        />
                    </el-form-item>
                </div>
            </div>

            <!-- Составные поля: array с nestedFields, object с nestedFields, widget -->
            <div class="composite-fields"       >
                <template v-for="field in compositeFields" :key="field.name">
                    <!-- array с nestedFields (массив объектов) -->
                    <el-form-item
                        v-if="field.type === 'array' && field.nestedFields"
                        :label="field.label"
                        :required="field.required"
                        :prop="field.name"
                    >
                        <div class="array-object-field">
                            <div
                                v-for="(item, itemIdx) in arrayItems(field.name)"
                                :key="itemIdx"
                                class="array-object-item border rounded p-3 mb-2"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium">Элемент #{{ itemIdx + 1 }}</span>
                                    <el-button
                                        size="small"
                                        type="danger"
                                        text
                                        @click="removeArrayItem(field.name, itemIdx)"
                                    >
                                        Удалить
                                    </el-button>
                                </div>
                                                                <!-- Для массива изображений — компонент выбора изображения -->
                                    <div v-if="field.format === 'image'" class="image-object-field">
                                        <ImagePicker
                                            :model-value="item || null"
                                            @update:model-value="(val) => onArrayImageFieldChange(field.name, itemIdx, val)"
                                        />
                                    </div>
                                <!-- Для массива товаров — компонент выбора товара -->
                                <div v-else-if="field.format === 'product'" class="product-object-field">
                                    <ProductPicker
                                        :model-value="item || null"
                                        @update:model-value="(val) => onArrayProductFieldChange(field.name, itemIdx, val)"
                                    />
                                </div>
                                <!-- Обычный массив объектов — через вложенный WidgetFieldRenderer -->
                                <WidgetFieldRenderer
                                    v-else
                                    :fields="nestedFieldInstances(field.nestedFields, field.name, itemIdx)"
                                    :disabled="disabled"
                                    :showSaveButton="false"
                                    @save="(vals) => onArrayItemSave(field.name, itemIdx, vals)"
                                />
                            </div>
                            <el-button v-if="!disabled" size="small" type="primary" plain @click="addArrayItem(field.name, field.nestedFields!, field.format)">
                                + Добавить элемент
                            </el-button>
                        </div>
                    </el-form-item>

                    <!-- object с nestedFields -->
                    <el-form-item
                        v-else-if="field.type === 'object' && field.nestedFields"
                        :label="field.label"
                        :required="field.required"
                        :prop="field.name"
                    >
                        <!-- Для image показываем компонент выбора изображения -->
                        <div v-if="field.format === 'image'" class="image-object-field w-full">
                            <ImagePicker
                                :model-value="formModel[field.name] || null"
                                @update:model-value="(val) => onImageFieldChange(field.name, val)"
                            />
                        </div>
                        <!-- Для product показываем компонент выбора товара -->
                        <div v-else-if="field.format === 'product'" class="product-object-field w-full">
                            <ProductPicker
                                :model-value="formModel[field.name] || null"
                                @update:model-value="(val) => onProductFieldChange(field.name, val)"
                            />
                        </div>
                        <!-- Обычный object — через вложенный WidgetFieldRenderer -->
                        <div v-else class="object-field border rounded p-3 bg-gray-50 w-full">
                            <WidgetFieldRenderer
                                :fields="nestedFieldInstances(field.nestedFields, field.name)"
                                :disabled="disabled"
                                :showSaveButton="false"
                                @save="(vals) => onObjectSave(field.name, vals)"
                            />
                        </div>
                </el-form-item>

                    <!-- widget — вложенный виджет -->
                    <el-form-item
                        v-else-if="field.format === 'widget'"
                        :label="field.label"
                        :required="field.required"
                    >
                        <div class="nested-widget-field w-full border rounded p-3 bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">
                                    <template v-if="formModel[field.name]?.widgetName">
                                        <el-tag size="small" type="success">{{ formModel[field.name].widgetName }}</el-tag>
                                    </template>
                                </span>
                                <div class="flex items-center gap-2">
                                    <el-button
                                        v-if="!formModel[field.name]?.id"
                                        size="small"
                                        @click="openNestedWidgetSelector(field.name)"
                                    >
                                        + Выбрать
                                    </el-button>
                                    <template v-if="formModel[field.name]?.id">
                                        <el-button size="small" @click="openNestedWidgetSelector(field.name)">
                                            Заменить
                                        </el-button>
                                        <el-button size="small" type="danger" text @click="removeNestedWidgetInstance(field.name)">
                                            Удалить
                                        </el-button>
                                    </template>
                                </div>
                            </div>

                            <!-- Вложенная форма дочернего экземпляра -->
                            <WidgetFieldRenderer
                                v-if="formModel[field.name]?.fields?.length > 0"
                                :fields="formModel[field.name].fields"
                                :disabled="disabled"
                                :showSaveButton="false"
                                @save="(vals) => onNestedWidgetFormSave(field.name, vals)"
                            />
                            <div v-else-if="!formModel[field.name]?.id" class="text-gray-400 text-xs py-2">
                                Выберите экземпляр виджета для настройки
                            </div>
                        </div>
                    </el-form-item>
                </template>
            </div>

            <el-button
                v-if="!disabled && showSaveButton"
                type="primary"
                :loading="saving"
                @click="onSave"
            >
                Сохранить
            </el-button>
        </el-form>

        <el-empty v-else description="Нет полей для настройки" />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'
import ImagePicker from './ImagePicker.vue'
import ProductPicker from './ProductPicker.vue'
import HtmlEditor from './HtmlEditor.vue'

const props = defineProps<{
    fields: WidgetFormFieldData[]
    disabled?: boolean
    saving?: boolean
    showSaveButton?: boolean
}>()

const emit = defineEmits<{
    (e: 'save', params: Record<string, any>): void
    (e: 'select-nested-widget', fieldName: string): void
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
        // html — всегда на всю ширину
        if (f.format === 'html') return true
        // long text — на всю ширину
        if (f.type === 'text' || (f.type === 'string' && f.value && typeof f.value === 'string' && f.value.length > 80)) return true
        // обычный string без enum и без спецформатов — на всю ширину
        if (f.type === 'string' && !f.options && !f.format) return true
        return false
    })
})

/**
 * Компактные поля — всё остальное, что не fullwidth и не composite
 */
const compactFields = computed(() => {
    return props.fields.filter(f => {
        // Исключаем fullwidth
        if (fullwidthFields.value.includes(f)) return false
        // Исключаем составные (object/array с nestedFields)
        if (f.nestedFields) return false
        // Исключаем format='widget' — оно отображается отдельно как вложенная форма
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

// --- Вспомогательные функции ---

function arrayItems(fieldName: string): any[] {
    const val = formModel[fieldName]
    return Array.isArray(val) ? val : []
}

function addArrayItem(fieldName: string, nestedFields: WidgetFormFieldData[], format?: string | null) {
    if (!Array.isArray(formModel[fieldName])) {
        formModel[fieldName] = []
    }
    // Для массива изображений — инициализируем объект с id, src, alt, title, description
    if (format === 'image') {
        formModel[fieldName].push({ id: null, src: '', alt: '', title: '', description: '' })
        return
    }
    // Для массива товаров — инициализируем объект с полями товара
    if (format === 'product') {
        formModel[fieldName].push({ id: null, name: null, url: null, short: null, price: null, image_src: null, image_alt: null, image_next_src: null, image_next_alt: null })
        return
    }
    const newItem: Record<string, any> = {}
    for (const nf of nestedFields) {
        newItem[nf.name] = nf.default ?? null
    }
    formModel[fieldName].push(newItem)
}

function removeArrayItem(fieldName: string, index: number) {
    if (Array.isArray(formModel[fieldName])) {
        formModel[fieldName].splice(index, 1)
    }
}

function nestedFieldInstances(
    nestedFields: WidgetFormFieldData[],
    parentName: string,
    itemIndex?: number,
): WidgetFormFieldData[] {
    if (itemIndex !== undefined) {
        const arr = formModel[parentName]
        const itemValue = (Array.isArray(arr) && arr[itemIndex]) ? arr[itemIndex] : {}
        return nestedFields.map(f => ({
            ...f,
            value: itemValue[f.name] !== undefined ? itemValue[f.name] : f.default ?? null,
        }))
    }
    const objValue = formModel[parentName]
    const val = (objValue && typeof objValue === 'object' && !Array.isArray(objValue)) ? objValue : {}
    return nestedFields.map(f => ({
        ...f,
        value: val[f.name] !== undefined ? val[f.name] : f.default ?? null,
    }))
}

function onObjectSave(parentName: string, vals: Record<string, any>) {
    formModel[parentName] = {
        ...(formModel[parentName] || {}),
        ...vals,
    }
}

function onArrayItemSave(parentName: string, itemIndex: number, vals: Record<string, any>) {
    if (!Array.isArray(formModel[parentName])) {
        formModel[parentName] = []
    }
    if (!formModel[parentName][itemIndex]) {
        formModel[parentName][itemIndex] = {}
    }
    formModel[parentName][itemIndex] = {
        ...formModel[parentName][itemIndex],
        ...vals,
    }
}

function removeNestedWidget(fieldName: string) {
    formModel[fieldName] = null
}

/** Удаление вложенного экземпляра виджета (когда value — объект с id) */
function removeNestedWidgetInstance(fieldName: string) {
    formModel[fieldName] = null
}

function openNestedWidgetSelector(fieldName: string) {
    emit('select-nested-widget', fieldName)
}

function isLongText(field: WidgetFormFieldData): boolean {
    const val = formModel[field.name]
    return typeof val === 'string' && val.length > 80
}

/**
 * Если это вложенный рендерер (без кнопки сохранения), эмитим save при любом изменении модели,
 * чтобы родительский компонент получил обновлённые вложенные данные.
 */
watch(formModel, () => {
    if (!props.showSaveButton) {
        const snapshot = JSON.parse(JSON.stringify(formModel))
        //console.debug('[WidgetFieldRenderer] nested auto-save:', snapshot)
        emit('save', snapshot)
    }
}, { deep: true })

function onFieldChange(name: string, value: any) {
    //console.debug('[WidgetFieldRenderer] field changed:', name, '=', value)
    formModel[name] = value
}

/** Обновить image-объект целиком (приходит из ImagePicker) */
function onImageFieldChange(parentName: string, value: any) {
    if (value === null) {
        delete formModel[parentName]
    } else {
        formModel[parentName] = { ...value }
    }
}

/** Обновить элемент массива изображений (приходит из ImagePicker) */
function onArrayImageFieldChange(parentName: string, itemIndex: number, value: any) {
    if (!Array.isArray(formModel[parentName])) {
        formModel[parentName] = []
    }
    if (value === null) {
        formModel[parentName].splice(itemIndex, 1)
    } else {
        formModel[parentName][itemIndex] = { ...value }
    }
}

/** Сохранить параметры вложенного дочернего экземпляра виджета */
function onNestedWidgetFormSave(fieldName: string, vals: Record<string, any>) {
    // Не обновляем formModel — вложенная форма автосохраняется через эмит
    // при showSaveButton=false, но данные уже в formModel[fieldName].fields
    // ничего делать не нужно, данные во вложенной форме уже синхронизированы
}

/** Обновить product-объект целиком (приходит из ProductPicker) */
function onProductFieldChange(parentName: string, value: any) {
    if (value === null) {
        delete formModel[parentName]
    } else {
        formModel[parentName] = { ...value }
    }
}

/** Обновить элемент массива товаров (приходит из ProductPicker) */
function onArrayProductFieldChange(parentName: string, itemIndex: number, value: any) {
    if (!Array.isArray(formModel[parentName])) {
        formModel[parentName] = []
    }
    if (value === null) {
        formModel[parentName].splice(itemIndex, 1)
    } else {
        formModel[parentName][itemIndex] = { ...value }
    }
}

function onSave() {
    const snapshot = JSON.parse(JSON.stringify(formModel))

    // Преобразуем поля с format='widget' обратно в простые ID для сохранения
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

    emit('save', snapshot)
}
</script>

<style scoped>
.widget-field-renderer {
    max-width: 600px;
}

/* Поля на всю ширину — label сверху */
.fullwidth-fields :deep(.el-form-item) {
    display: block;
    margin-bottom: 16px;
}
.fullwidth-fields :deep(.el-form-item__label) {
    display: block;
    text-align: left;
    padding-bottom: 4px;
}
.fullwidth-fields :deep(.el-form-item__content) {
    display: block;
}
.fullwidth-fields :deep(.el-form-item__content .el-input),
.fullwidth-fields :deep(.el-form-item__content .el-textarea) {
    width: 100%;
}

/* Компактные поля — в ряд, label слева */
.compact-fields {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    margin-bottom: 16px;
}
.compact-row {
    flex: 0 1 auto;
    min-width: 180px;
}
.compact-fields :deep(.el-form-item) {
    margin-bottom: 0;
    display: flex !important;
    flex-direction: row !important;
    align-items: center;
    gap: 6px;
}
.compact-fields :deep(.el-form-item__label) {
    white-space: nowrap;
    padding: 0;
    text-align: left;
    float: none;
    display: inline-block;
    width: auto;
    line-height: 28px;
}
.compact-fields :deep(.el-form-item__content) {
    display: inline-flex;
    flex: 0 1 auto;
    width: auto;
    min-width: 120px;
}

/* Select внутри компактных полей — растягиваем на всю доступную ширину */
.compact-fields :deep(.el-form-item__content .el-select) {
    width: 100%;
    min-width: 140px;
}

/* Для boolean — switch без лишнего пространства */
.compact-fields :deep(.el-form-item__content .el-switch) {
    margin-top: 0;
}

/* Составные поля */
.composite-fields {
    margin-bottom: 16px;
}
.composite-fields :deep(.el-form-item) {
    display: block;
    margin-bottom: 16px;
}

.nested-widget-field {
    display: flex;
    align-items: center;
    gap: 8px;
}
.array-object-field {
    width: 100%;
}
.array-object-item {
    background: #f9fafb;
}
.object-field {
    max-width: 100%;
}
</style>
