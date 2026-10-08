<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-business-time"></i>
                <span> Управление</span>
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
                <el-col :span="8">
                    <el-form label-width="auto">
                        <el-form-item label="Товар опубликован">
                            <el-checkbox v-model="form.published" :checked="form.published" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Товар снят с продажи">
                            <el-checkbox v-model="form.notSale" :checked="form.notSale" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Приоритетный показ">
                            <el-checkbox v-model="form.priority" :checked="form.priority" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Цена снижена">
                            <el-checkbox v-model="form.priceReduced" :checked="form.priceReduced" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Скрывать в прайс-листах">
                            <el-checkbox v-model="form.hidePrice" :checked="form.hidePrice" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Доступен для предзаказа">
                            <el-checkbox v-model="form.preOrder" :checked="form.preOrder" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Только под заказ">
                            <el-checkbox v-model="form.onlyOnOrder" :checked="form.onlyOnOrder" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                    </el-form>
                </el-col>
                <el-col :span="8">
                    <el-form label-width="auto">
                        <h2>Хранение</h2>
                        <el-form-item v-for="storage in form.storages" :label="storage.name">
                            <el-input v-model="storage.cell" placeholder="Ячейка" @change="onAutoSave" :disabled="isSaving" style="width: 200px;"/>
                        </el-form-item>

                        <h2 class="mt-3">Баланс</h2>
                        <div class="flex items-center">
                            <el-form-item label="Мин.кол-во" label-position="top">
                                <el-input v-model="form.balance.min" @change="onAutoSave" :disabled="isSaving" style="width: 100px;"/>
                            </el-form-item>
                            <el-form-item label="Макс.кол-во" label-position="top">
                                <el-input v-model="form.balance.max" @change="onAutoSave" :disabled="isSaving" style="width: 100px;" class="ml-2" clearable/>
                            </el-form-item>
                            <el-form-item label="Закупать" label-position="top">
                                <el-switch
                                    v-model="form.balance.buy"
                                    inline-prompt
                                    style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ccc"
                                    active-text="Да"
                                    inactive-text="Нет"
                                    class="ml-2"
                                    @change="onAutoSave" :disabled="isSaving"
                                />
                            </el-form-item>
                        </div>
                    </el-form>
                </el-col>
                <el-col :span="8"></el-col>
            </el-row>
            <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, computed, watch, defineProps } from "vue";
import api from "@Res/api";
import {route} from "ziggy-js";
import {useProductPanel} from "./useProductPanel";

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
        default: 'management',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)

const form = reactive({
    id: props.productId,
    published: false,
    notSale: false,
    priority: false,
    priceReduced: false,
    hidePrice: false,
    preOrder: true,
    onlyOnOrder: false,
    storages: [],
    balance: { min: 0, max: null, buy: false },
    modification: false,
})

// Ошибки валидации (приходят из ответа на сохранение).
const saveErrors = reactive({})
const errors = computed(() => ({ ...saveErrors }))

// Загрузка данных панели с кэшем на 5 минут.
const { load, loading, loaded, setCache } = useProductPanel('management', async () => {
    return api.get(
        route('admin.catalog.product.edit.management', { id: props.productId }),
        null,
        { showSuccess: false },
    )
})

function applyData(data) {
    form.id = data.id
    form.published = data.published
    form.notSale = data.notSale
    form.priority = data.priority
    form.priceReduced = data.priceReduced
    form.hidePrice = data.hidePrice
    form.preOrder = data.preOrder
    form.onlyOnOrder = data.onlyOnOrder
    form.storages = Array.isArray(data.storages) ? data.storages.map(s => ({ ...s })) : []
    form.balance = { ...form.balance, ...(data.balance || {}) }
    hasModification.value = data.hasModification
}

// Автозагрузка при активации панели.
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
        route('admin.catalog.product.edit.management', { id: props.productId }),
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
