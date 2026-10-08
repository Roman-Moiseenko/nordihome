<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-face-smile-plus"></i>
                <span> Бонусные товары</span>
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
                <el-col :span="8">
                    <div class="flex">
                        <el-select
                            v-model="form.productId"
                            filterable
                            remote
                            reserve-keyword
                            placeholder="Введите артикул или название"
                            :remote-method="remoteMethod"
                            :loading="searchLoading"
                            style="width: 260px;"
                        >
                            <el-option
                                v-for="item in options"
                                :key="item.id"
                                :value="item.id"
                                :label="item.name + ' (' + item.code + ')'"
                            />
                        </el-select>
                        <el-button type="primary" @click="onAdd" class="ml-1" :disabled="isSaving">
                            <i class="fa-light fa-box mr-2"></i>Добавить бонус
                        </el-button>
                    </div>
                </el-col>
                <!-- Колонка 2 -->
                <el-col :span="12">
                    <div v-for="prod in products" :key="prod.id" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                        <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                        <span class="font-medium ml-3" style="width: 120px;">{{ prod.code }}</span>
                        <span class="font-medium ml-2"><Link type="primary" :href="route('admin.catalog.product.edit', {id: prod.id})">{{ prod.name }}</Link></span>
                        <span class="text-red-800 line-through ml-auto">{{ func.price(prod.price) }}</span>
                        <el-input v-model="prod.discount" @change="onAutoSave" :disabled="isSaving" class="ml-2" style="width: 160px;">
                            <template #append>₽</template>
                        </el-input>
                        <div class="ml-2">
                            <el-button type="danger" @click="onRemove(prod.id)" :disabled="isSaving"><i
                                class="fa-light fa-trash"></i></el-button>
                        </div>
                    </div>
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
import {func} from "@Res/func"
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
        default: 'bonus',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)
const products = ref([])
const options = ref([])
const searchLoading = ref(false)

const form = reactive({
    productId: null,
    modification: false,
})

const {load, loading, loaded, setCache} = useProductPanel('bonus', async () => {
    return api.get(
        route('admin.catalog.product.edit.bonus', {id: props.productId}),
        null,
        {showSuccess: false},
    )
})

function applyData(data) {
    products.value = Array.isArray(data.products) ? data.products.map(p => ({...p})) : []
    hasModification.value = data.hasModification
}

watch(() => props.active, async (active) => {
    if (!active) return
    const {data, fromCache} = await load()
    if (!fromCache && data) applyData(data)
}, {immediate: true})

function remoteMethod(query) {
    if (!query) {
        options.value = []
        return
    }
    searchLoading.value = true
    api.post(
        route('admin.catalog.product.search-add'),
        {search: query},
        {showSuccess: false, showError: false},
    ).then(data => {
        options.value = Array.isArray(data) ? data : []
    }).finally(() => {
        searchLoading.value = false
    })
}

function post(data) {
    isSaving.value = true
    return api.post(
        route('admin.catalog.product.edit.bonus', {id: props.productId}),
        {id: props.productId, ...data},
        {showSuccess: false},
    ).then(data => {
        applyData(data)
        setCache(data)
    }).finally(() => {
        isSaving.value = false
    })
}

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}

function onAdd() {
    if (form.productId === null) return
    post({productId: form.productId, modification: form.modification}).then(() => {
        form.productId = null
    })
}

function onRemove(productId) {
    post({productId, action: 'remove', modification: form.modification})
}

function onSave() {
    post({
        action: 'edit',
        bonus: products.value.map(p => ({id: p.id, discount: p.discount})),
        modification: form.modification,
    })
}
</script>

<style scoped>

</style>
