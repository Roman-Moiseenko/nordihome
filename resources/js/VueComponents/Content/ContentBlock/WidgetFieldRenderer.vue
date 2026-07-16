<template>
    <div class="widget-field-renderer">
        <el-form
            v-if="fields.length > 0"
            :model="formModel"
            label-position="top"
            size="small"
        >
            <template v-for="field in fields" :key="field.name">
                <!-- === ОБЫЧНЫЕ ПОЛЯ (примитивные типы) === -->

                <!-- Поле с format === 'color' -->
                <el-form-item
                    v-if="field.format === 'color'"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-color-picker
                        v-model="formModel[field.name]"
                        :disabled="disabled"
                    />
                </el-form-item>

                <!-- Поле с format === 'html' (textarea с rich text) -->
                <el-form-item
                    v-else-if="field.format === 'html'"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-input
                        v-model="formModel[field.name]"
                        type="textarea"
                        :rows="4"
                        :disabled="disabled"
                        :placeholder="field.label"
                    />
                </el-form-item>

                <!-- Поле с format === 'widget' (ссылка на другой экземпляр виджета) -->
                <el-form-item
                    v-else-if="field.format === 'widget'"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <div class="nested-widget-field">
                        <el-tag v-if="formModel[field.name]" type="success" closable @close="removeNestedWidget(field.name)">
                            Виджет #{{ formModel[field.name] }}
                        </el-tag>
                        <el-button v-else size="small" @click="openNestedWidgetSelector(field.name)">
                            Выбрать виджет
                        </el-button>
                    </div>
                </el-form-item>

                <!-- Поле с options (enum) -->
                <el-form-item
                    v-else-if="field.options && field.options.length > 0"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-select
                        v-model="formModel[field.name]"
                        :disabled="disabled"
                        :multiple="field.type === 'array'"
                        clearable
                        class="w-full"
                    >
                        <el-option
                            v-for="opt in field.options"
                            :key="opt"
                            :label="opt"
                            :value="opt"
                        />
                    </el-select>
                </el-form-item>

                <!-- boolean (чекбокс) -->
                <el-form-item
                    v-else-if="field.type === 'boolean'"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-switch
                        v-model="formModel[field.name]"
                        :disabled="disabled"
                    />
                </el-form-item>

                <!-- number / integer -->
                <el-form-item
                    v-else-if="field.type === 'integer' || field.type === 'number'"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-input-number
                        v-model="formModel[field.name]"
                        :disabled="disabled"
                        :min="0"
                        class="w-full"
                    />
                </el-form-item>

                <!-- Текстовое поле — textarea если значение длинное -->
                <el-form-item
                    v-else-if="field.type === 'text' || (field.type === 'string' && isLongText(field))"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-input
                        v-model="formModel[field.name]"
                        type="textarea"
                        :rows="4"
                        :disabled="disabled"
                        :placeholder="field.label"
                    />
                </el-form-item>

                <!-- type === 'array' с nestedFields (массив объектов) -->
                <el-form-item
                    v-else-if="field.type === 'array' && field.nestedFields"
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
                            <WidgetFieldRenderer
                                :fields="nestedFieldInstances(field.nestedFields, field.name, itemIdx)"
                                :disabled="disabled"
                                :showSaveButton="false"
                                @save="(vals) => onArrayItemSave(field.name, itemIdx, vals)"
                            />
                        </div>
                        <el-button v-if="!disabled" size="small" type="primary" plain @click="addArrayItem(field.name, field.nestedFields!)">
                            + Добавить элемент
                        </el-button>
                    </div>
                </el-form-item>

                <!-- type === 'object' с nestedFields (вложенный объект) -->
                <el-form-item
                    v-else-if="field.type === 'object' && field.nestedFields"
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <div class="object-field border rounded p-3 bg-gray-50 w-full">
                        <WidgetFieldRenderer
                            :fields="nestedFieldInstances(field.nestedFields, field.name)"
                            :disabled="disabled"
                            :showSaveButton="false"
                            @save="(vals) => onObjectSave(field.name, vals)"
                        />
                    </div>
                </el-form-item>

                <!-- default: string input -->
                <el-form-item
                    v-else
                    :label="field.label"
                    :required="field.required"
                    :prop="field.name"
                >
                    <el-input
                        v-model="formModel[field.name]"
                        :disabled="disabled"
                        :placeholder="field.label"
                    />
                </el-form-item>
            </template>

            <el-button
                v-if="!disabled && showSaveButton"
                type="primary"
                :loading="saving"
                @click="$emit('save', formModel)"
            >
                Сохранить
            </el-button>
        </el-form>

        <el-empty v-else description="Нет полей для настройки" />
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'

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

const formModel = ref<Record<string, any>>({})

// Инициализируем модель из полей
watch(() => props.fields, (fields) => {
    const model: Record<string, any> = {}
    for (const field of fields) {
        if (field.type === 'object' && field.nestedFields) {
            // Для объектов оставляем текущее значение как объект (или пустой)
            model[field.name] = field.value && typeof field.value === 'object' && !Array.isArray(field.value)
                ? { ...field.value }
                : {}
        } else if (field.type === 'array' && field.nestedFields) {
            // Для массива объектов оставляем массив
            model[field.name] = Array.isArray(field.value) ? [...field.value] : []
        } else {
            model[field.name] = field.value !== undefined && field.value !== null
                ? field.value
                : field.default ?? null
        }
    }
    formModel.value = model
}, { immediate: true, deep: true })

/**
 * Для массива объектов: получить элементы массива из formModel.
 */
function arrayItems(fieldName: string): any[] {
    const val = formModel.value[fieldName]
    return Array.isArray(val) ? val : []
}

/**
 * Добавить элемент в массив объектов.
 */
function addArrayItem(fieldName: string, nestedFields: WidgetFormFieldData[]) {
    if (!Array.isArray(formModel.value[fieldName])) {
        formModel.value[fieldName] = []
    }
    const newItem: Record<string, any> = {}
    for (const nf of nestedFields) {
        newItem[nf.name] = nf.default ?? null
    }
    formModel.value[fieldName].push(newItem)
}

/**
 * Удалить элемент из массива объектов.
 */
function removeArrayItem(fieldName: string, index: number) {
    if (Array.isArray(formModel.value[fieldName])) {
        formModel.value[fieldName].splice(index, 1)
    }
}

/**
 * Создать экземпляры полей для вложенного объекта с подставленными значениями.
 */
function nestedFieldInstances(
    nestedFields: WidgetFormFieldData[],
    parentName: string,
    itemIndex?: number,
): WidgetFormFieldData[] {
    if (itemIndex !== undefined) {
        const arr = formModel.value[parentName]
        const itemValue = (Array.isArray(arr) && arr[itemIndex]) ? arr[itemIndex] : {}
        return nestedFields.map(f => ({
            ...f,
            value: itemValue[f.name] !== undefined ? itemValue[f.name] : f.default ?? null,
        }))
    }
    const objValue = formModel.value[parentName]
    const val = (objValue && typeof objValue === 'object' && !Array.isArray(objValue)) ? objValue : {}
    return nestedFields.map(f => ({
        ...f,
        value: val[f.name] !== undefined ? val[f.name] : f.default ?? null,
    }))
}

/**
 * Сохранить вложенный объект.
 */
function onObjectSave(parentName: string, vals: Record<string, any>) {
    formModel.value[parentName] = {
        ...(formModel.value[parentName] || {}),
        ...vals,
    }
}

/**
 * Сохранить элемент массива.
 */
function onArrayItemSave(parentName: string, itemIndex: number, vals: Record<string, any>) {
    if (!Array.isArray(formModel.value[parentName])) {
        formModel.value[parentName] = []
    }
    if (!formModel.value[parentName][itemIndex]) {
        formModel.value[parentName][itemIndex] = {}
    }
    formModel.value[parentName][itemIndex] = {
        ...formModel.value[parentName][itemIndex],
        ...vals,
    }
}

function removeNestedWidget(fieldName: string) {
    formModel.value[fieldName] = null
}

function openNestedWidgetSelector(fieldName: string) {
    emit('select-nested-widget', fieldName)
}

/**
 * Проверить, нужно ли для поля показывать textarea вместо input.
 * Если значение длиннее 80 символов — textarea.
 */
function isLongText(field: WidgetFormFieldData): boolean {
    const val = formModel.value[field.name]
    return typeof val === 'string' && val.length > 80
}
</script>

<style scoped>
.widget-field-renderer {
    max-width: 500px;
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
