<template>
    <el-form-item
        :label="field.label"
        :required="field.required"
        :prop="field.name"
        :class="variant === 'compact' ? 'widget-field-simple--compact' : 'widget-field-simple--fullwidth'"
    >
        <!-- Поля на всю ширину: html, textarea, обычный input -->
        <template v-if="variant === 'fullwidth'">
            <!-- html — WYSIWYG-редактор (@erag/text-editor-vue) -->
            <RichTextEditor
                v-if="field.format === 'html'"
                :model-value="modelValue || ''"
                @update:model-value="(val) => emit('update:modelValue', val)"
                :disabled="disabled"
                :placeholder="field.label"
                :height="300"
            />
            <!-- textarea если длинное значение -->
            <el-input
                v-else-if="isLongText"
                :model-value="modelValue"
                @update:model-value="(val) => emit('update:modelValue', val)"
                type="textarea"
                :rows="4"
                :disabled="disabled"
                :placeholder="field.label"
            />
            <!-- обычное строковое поле -->
            <el-input
                v-else
                :model-value="modelValue"
                @update:model-value="(val) => emit('update:modelValue', val)"
                :disabled="disabled"
                :placeholder="field.label"
            />
        </template>

        <!-- Компактные поля: color, select, switch, number -->
        <template v-else>
            <!-- color -->
            <el-color-picker
                v-if="field.format === 'color'"
                :model-value="modelValue"
                @update:model-value="(val) => emit('update:modelValue', val)"
                :disabled="disabled"
            />
            <!-- enum / select -->
            <el-select
                v-else-if="field.options && field.options.length > 0"
                :model-value="modelValue"
                @update:model-value="(val) => emit('update:modelValue', val)"
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
                :model-value="modelValue"
                @update:model-value="(val) => emit('update:modelValue', val)"
                :disabled="disabled"
            />
            <!-- number / integer -->
            <el-input-number
                v-else-if="field.type === 'integer' || field.type === 'number'"
                :model-value="modelValue"
                @update:model-value="(val) => emit('update:modelValue', val)"
                :disabled="disabled"
                :min="0"
            />
        </template>
    </el-form-item>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'
import RichTextEditor from './RichTextEditor.vue'

const props = defineProps<{
    field: WidgetFormFieldData
    modelValue: any
    disabled?: boolean
    variant: 'fullwidth' | 'compact'
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void
}>()

const isLongText = computed(() => {
    return typeof props.modelValue === 'string' && props.modelValue.length > 80
})
</script>

<style scoped>
/* Поля на всю ширину — label сверху */
.widget-field-simple--fullwidth {
    display: block;
    margin-bottom: 16px;
}
.widget-field-simple--fullwidth :deep(.el-form-item__label) {
    display: block;
    text-align: left;
    padding-bottom: 4px;
}
.widget-field-simple--fullwidth :deep(.el-form-item__content) {
    display: block;
}
.widget-field-simple--fullwidth :deep(.el-form-item__content .el-input),
.widget-field-simple--fullwidth :deep(.el-form-item__content .el-textarea) {
    width: 100%;
}

/* Компактные поля — в ряд, label слева */
.widget-field-simple--compact {
    flex: 0 1 auto;
    min-width: 180px;
    margin-bottom: 0;
    display: flex !important;
    flex-direction: row !important;
    align-items: center;
    gap: 6px;
}
.widget-field-simple--compact :deep(.el-form-item__label) {
    white-space: nowrap;
    padding: 0;
    text-align: left;
    float: none;
    display: inline-block;
    width: auto;
    line-height: 28px;
}
.widget-field-simple--compact :deep(.el-form-item__content) {
    display: inline-flex;
    flex: 0 1 auto;
    width: auto;
    min-width: 120px;
}
.widget-field-simple--compact :deep(.el-form-item__content .el-select) {
    width: 100%;
    min-width: 140px;
}
.widget-field-simple--compact :deep(.el-form-item__content .el-switch) {
    margin-top: 0;
}
</style>
