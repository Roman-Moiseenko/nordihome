<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-memo-pad"></i>
                <span> Описание</span>
            </span>
        </template>
        <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
            <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
        </div>

        <div v-else>
            <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
            <el-checkbox v-if="hasModification"
                v-model="form.modification"
                :checked="form.modification" class="checkbox-warning">
                Сохранять для всех товаров из Модификации
            </el-checkbox>

            <el-row :gutter="10" class="mt-2">
                <!-- Колонка 1 -->
                <el-col :span="12">
                    <el-form label-width="auto">
                        <el-form-item label="Полное описание" label-position="top">
                            <el-input v-model="form.description" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="26" resize="none"/>
                            <div v-if="errors.description" class="text-red-700">{{ errors.description }}</div>
                        </el-form-item>
                    </el-form>
                </el-col>
                <!-- Колонка 2 -->
                <el-col :span="12">
                    <el-form label-width="auto">
                        <el-form-item label="Краткое описание" label-position="top">
                            <el-input v-model="form.short" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="10" resize="none"/>
                            <div v-if="errors.short" class="text-red-700">{{ errors.short }}</div>
                        </el-form-item>
                        <el-form-item label="Материал и уход" label-position="top">
                            <el-input v-model="form.care" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="7" resize="none"/>
                            <div v-if="errors.care" class="text-red-700">{{ errors.care }}</div>
                        </el-form-item>
                        <el-form-item label="Метки" label-position="left">
                            <el-select v-model="form.tags" @change="onAutoSave" :disabled="isSaving" multiple filterable allow-create>
                                <el-option v-for="item in useCatalog.tags" :key="item.id" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.tags" class="text-red-700">{{ errors.tags }}</div>
                        </el-form-item>
                        <el-form-item label="Серия" label-position="left">
                            <el-select v-model="form.seriesId" @change="onAutoSave" :disabled="isSaving" filterable allow-create clearable>
                                <el-option v-for="item in useCatalog.series" :key="item.id" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.seriesId" class="text-red-700">{{ errors.seriesId }}</div>
                        </el-form-item>
                        <el-form-item label="Модель" label-position="left">
                            <el-input v-model="form.model" @change="onAutoSave" :disabled="isSaving"/>
                            <div v-if="errors.model" class="text-red-700">{{ errors.model }}</div>
                        </el-form-item>
                    </el-form>
                </el-col>
            </el-row>
            <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, computed, watch, defineProps } from "vue";
import {useCatalogStore} from "@Res/catalogStore.ts";
import api from "@Res/api";
import {route} from "ziggy-js";
import {useProductPanel} from "./useProductPanel";

const useCatalog = useCatalogStore()

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    name: {
        type: String,
        default: 'description',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)

const form = reactive({
    id: props.productId,
    description: '',
    short: '',
    care: '',
    model: '',
    tags: [],
    seriesId: null,
    modification: false,
})

// Ошибки валидации (приходят из ответа на сохранение).
const saveErrors = reactive({})
const errors = computed(() => ({ ...saveErrors }))

// Загрузка данных панели с кэшем на 5 минут.
const { load, loading, loaded, setCache } = useProductPanel('description', async () => {
    return api.get(
        route('admin.catalog.product.edit.description', { id: props.productId }),
        null,
        { showSuccess: false },
    )
})

function applyData(data) {
    form.id = data.id
    form.description = data.description
    form.short = data.short
    form.care = data.care
    form.model = data.model
    form.tags = Array.isArray(data.tags) ? data.tags : []
    form.seriesId = data.seriesId
    hasModification.value = data.hasModification
}

// Автозагрузка при активации панели.
// Если данные уже загружались — повторно не запрашиваем (кэш 5 минут),
// а форму не перезатираем, чтобы не потерять несохранённые изменения.
watch(() => props.active, async (active) => {
    if (!active) return
    const { data, fromCache } = await load()
    if (!fromCache && data) {
        applyData(data)
    }
}, { immediate: true })


function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}

function onSave() {
    isSaving.value = true;
    Object.keys(saveErrors).forEach(key => delete saveErrors[key])

    api.post(
        route('admin.catalog.product.edit.description', { id: props.productId }),
        { ...form },
    ).then(data => {
        applyData(data)
        setCache(data)
    }).catch(error => {
        const errs = error?.response?.data?.errors
        if (errs && typeof errs === 'object') {
            for (const [key, value] of Object.entries(errs)) {
                saveErrors[key] = Array.isArray(value) ? value[0] : value
            }
        }
    }).finally(() => {
        isSaving.value = false
    })
}
</script>

<style scoped>

</style>
