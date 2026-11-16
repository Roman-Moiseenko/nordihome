<template>
    <el-tab-pane name="parameters">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-sliders"></i>
                <span> Параметры</span>
            </span>
        </template>

        <div v-if="!draftValues.length" class="text-gray-400 py-6 text-center">
            Категория не выбрана или параметры отсутствуют
        </div>

        <template v-else>
            <el-form label-width="240px" label-position="left">
                <el-form-item v-for="item in draftValues" :key="item.fieldId" :label="item.name">
                    <div class="flex items-center w-full gap-2">
                        <!-- Варианты есть — выпадающий список -->
                        <el-select
                            v-if="hasVariants(item) && item.type === 'array'"
                            v-model="item.value"
                            multiple
                            filterable
                            clearable
                            style="flex: 1;"
                        >
                            <el-option
                                v-for="option in item.allowedValues"
                                :key="option"
                                :value="option"
                                :label="option"
                            />
                        </el-select>

                        <el-select
                            v-else-if="hasVariants(item)"
                            v-model="item.value"
                            filterable
                            clearable
                            allow-create
                            default-first-option
                            style="flex: 1;"
                        >
                            <el-option
                                v-for="option in item.allowedValues"
                                :key="option"
                                :value="option"
                                :label="option"
                            />
                        </el-select>

                        <!-- Без вариантов — обычный ввод -->
                        <el-input-number
                            v-else-if="item.type === 'integer' || item.type === 'float'"
                            v-model="item.value"
                            :controls="false"
                            style="flex: 1;"
                        />
                        <el-input v-else v-model="item.value" style="flex: 1;"/>

                        <el-button size="small" @click="openVariantsDialog(item)">
                            Добавить вариант
                        </el-button>
                    </div>
                </el-form-item>
            </el-form>

            <!-- Собственные кнопки Сохранить/Отмена для значений параметров -->
            <div v-if="hasChanges" class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                <el-button @click="onCancel">Отмена</el-button>
                <el-button type="primary" :loading="savingValues" @click="onSave">Сохранить</el-button>
            </div>
        </template>

        <!-- Модальное окно добавления вариантов -->
        <el-dialog v-model="dialogVisible" title="Варианты параметра" width="560px">
            <div v-if="dialogField" class="text-gray-500 mb-3">{{ dialogField.name }}</div>
            <el-select
                v-model="dialogVariants"
                multiple
                filterable
                allow-create
                default-first-option
                :reserve-keyword="false"
                placeholder="Введите и добавьте вариант"
                style="width: 100%;"
            >
                <el-option
                    v-for="option in dialogVariants"
                    :key="option"
                    :value="option"
                    :label="option"
                />
            </el-select>
            <template #footer>
                <el-button @click="dialogVisible = false">Отмена</el-button>
                <el-button type="primary" :loading="saving" @click="saveVariants">Сохранить</el-button>
            </template>
        </el-dialog>
    </el-tab-pane>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps({
    values: {
        type: Array,
        default: () => [],
    },
    categoryId: {
        type: Number,
        default: null,
    },
    productId: {
        type: Number,
        required: true,
    },
})

function cloneValue(value: any) {
    return JSON.parse(JSON.stringify(value ?? null))
}

// Эталон (для сравнения и отмены)
const initialValues = ref(props.values.map((v: any) => ({
    ...v,
    value: cloneValue(v.value),
})))

// Рабочая копия, к которой привязаны поля формы
const draftValues = ref(props.values.map((v: any) => ({
    ...v,
    value: cloneValue(v.value),
})))

const dialogVisible = ref(false)
const dialogField = ref<any>(null)
const dialogVariants = ref<string[]>([])
const saving = ref(false)
const savingValues = ref(false)

const hasChanges = computed(() => {
    if (draftValues.value.length !== initialValues.value.length) return true

    return draftValues.value.some((item: any, i: number) => {
        const initial = initialValues.value[i]
        if (!initial) return true
        return JSON.stringify(item.value) !== JSON.stringify(initial.value)
    })
})

function hasVariants(item: any): boolean {
    return Array.isArray(item.allowedValues) && item.allowedValues.length > 0
}

function openVariantsDialog(item: any) {
    dialogField.value = item
    dialogVariants.value = Array.isArray(item.allowedValues) ? [...item.allowedValues] : []
    dialogVisible.value = true
}

async function saveVariants() {
    if (!dialogField.value || !props.categoryId) return

    saving.value = true
    try {
        const { data } = await axios.post(
            route('admin.marketplace.avito.category.field.variants', {
                id: props.categoryId,
                fieldId: dialogField.value.fieldId,
            }),
            { variants: dialogVariants.value }
        )

        const allowed = Array.isArray(data.allowed_values) ? data.allowed_values : []

        dialogField.value.allowedValues = allowed

        // Обновляем тот же элемент в рабочей копии
        const item = draftValues.value.find((v: any) => v.fieldId === dialogField.value.fieldId)
        if (item) {
            item.allowedValues = allowed
        }

        dialogVisible.value = false
    } finally {
        saving.value = false
    }
}

function onSave() {
    savingValues.value = true
    router.post(route('admin.marketplace.avito.product.values', { id: props.productId }), {
        values: draftValues.value.map((v: any) => ({
            avito_category_field_id: v.fieldId,
            value: v.value,
        })),
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            initialValues.value = draftValues.value.map((v: any) => ({
                ...v,
                value: cloneValue(v.value),
            }))
        },
        onFinish: () => {
            savingValues.value = false
        },
    })
}

function onCancel() {
    draftValues.value = initialValues.value.map((v: any) => ({
        ...v,
        value: cloneValue(v.value),
    }))
}
</script>

<style scoped>

</style>
