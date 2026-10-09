<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-balloons"></i>
                <span> Аналоги</span>
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
            <el-row :gutter="20" class="mt-2">
                <!-- Колонка 1 -->
                <el-col :span="6">
                    <el-form label-width="auto">
                        <el-form-item label="Связанная группа аналогичных товаров" label-position="top">
                            <el-select v-model="form.equivalentId" @change="onAutoSave" :disabled="isSaving" clearable placeholder="~ Без группы ~">
                                <el-option :key="null" :value="null" label="~ Без группы ~"/>
                                <el-option v-for="item in equivalents" :key="item.id" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.field" class="text-red-700">{{ errors.field }}</div>
                        </el-form-item>
                    </el-form>
                </el-col>
                <!-- Колонка 2 -->
                <el-col :span="12">
                    <el-form label-width="auto">
                        <div v-if="currentEquivalent">
                            <h2>Товары из группы</h2>
                            <div v-for="equi_prod in currentEquivalent.products" :key="equi_prod.id" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md">
                                <img v-if="equi_prod.image" :src="equi_prod.image" width="40" height="40"/>
                                <span class="font-medium ml-3" style="width: 160px;">{{ equi_prod.code }}</span>
                                <span class="font-medium ml-2">
                                    <span v-if="equi_prod.id === props.productId">{{ equi_prod.name }}</span>
                                    <Link v-else type="primary" :href="route('admin.catalog.product.edit', {id: equi_prod.id})">{{ equi_prod.name }}</Link>
                                </span>
                            </div>
                        </div>
                    </el-form>
                </el-col>
            </el-row>
            <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, watch, defineProps} from "vue"
import {Link} from "@inertiajs/vue3"
import {route} from "ziggy-js"
import api from "@Res/api"
import {useProductPanel} from "./useProductPanel"

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
        default: 'equivalent',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)
const equivalents = ref([])
const currentEquivalent = ref(null)

const form = reactive({
    equivalentId: null,
    modification: false,
})

const saveErrors = reactive({})
const errors = {
    get field() {
        return saveErrors['equivalentId'] || saveErrors['equivalent_id'] || null
    },
}

const {load, loading, loaded, setCache} = useProductPanel('equivalent', async () => {
    return api.get(
        route('admin.catalog.product.edit.equivalent', {id: props.productId}),
        null,
        {showSuccess: false},
    )
})

function applyData(data) {
    equivalents.value = Array.isArray(data.equivalents) ? data.equivalents : []
    currentEquivalent.value = data.currentEquivalent ?? null
    form.equivalentId = data.currentEquivalentId ?? null
    hasModification.value = data.hasModification
}

watch(() => props.active, async (active) => {
    if (!active) return
    const {data, fromCache} = await load()
    if (!fromCache && data) applyData(data)
}, {immediate: true})

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}

function onSave() {
    isSaving.value = true
    Object.keys(saveErrors).forEach(key => delete saveErrors[key])

    api.post(
        route('admin.catalog.product.edit.equivalent', {id: props.productId}),
        {
            id: props.productId,
            equivalentId: form.equivalentId,
            modification: form.modification,
        },
        {showSuccess: false},
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
