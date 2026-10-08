<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-ear-muffs"></i>
                <span> Сопутствующие</span>
            </span>
        </template>
        <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
            <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
        </div>

        <el-row v-else :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-checkbox v-if="hasModification"
                             v-model="form.modification"
                             :checked="form.modification" class="checkbox-warning">
                    Сохранять для всех товаров из Модификации
                </el-checkbox>
                <div class="flex mt-2">
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
                        <i class="fa-light fa-box mr-2"></i>Добавить аксессуар
                    </el-button>
                </div>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12">
                <div v-for="prod in products" :key="prod.id" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                    <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                    <span class="font-medium ml-3" style="width: 120px;">{{ prod.code }}</span>
                    <span class="font-medium ml-2"><Link type="primary" :href="route('admin.catalog.product.edit', {id: prod.id})">{{ prod.name }}</Link></span>
                    <div class="ml-auto">
                        <el-button type="danger" @click="onRemove(prod.id)" :disabled="isSaving"><i
                            class="fa-light fa-trash"></i></el-button>
                    </div>
                </div>
            </el-col>
        </el-row>
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
        default: 'related',
    },
})

const isSaving = ref(false)
const hasModification = ref(false)
const products = ref([])
const options = ref([])
const searchLoading = ref(false)

const form = reactive({
    productId: null,
    modification: false,
})

const {load, loading, loaded, setCache} = useProductPanel('related', async () => {
    return api.get(
        route('admin.catalog.product.edit.related', {id: props.productId}),
        null,
        {showSuccess: false},
    )
})

function applyData(data) {
    products.value = Array.isArray(data.products) ? data.products : []
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
        route('admin.catalog.product.edit.related', {id: props.productId}),
        {id: props.productId, ...data},
        {showSuccess: false},
    ).then(data => {
        applyData(data)
        setCache(data)
    }).finally(() => {
        isSaving.value = false
    })
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
</script>

<style scoped>

</style>
