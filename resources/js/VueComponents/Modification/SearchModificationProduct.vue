<template>
    <div class="flex flex-wrap items-center">
        <el-select
            v-model="form.productId"
            filterable
            remote
            clearable
            reserve-keyword
            placeholder="Введите артикул или название"
            :remote-method="remoteMethod"
            :loading="loading"
            style="width: 300px;"
        >
            <el-option
                v-for="item in options"
                :key="item.id"
                :value="item.id"
                :label="item.name + ' (' + item.code + ')'"
            />
        </el-select>

        <el-select
            v-for="attribute in modification.attributes"
            :key="attribute.id"
            v-model="filters[attribute.id]"
            clearable
            :placeholder="attribute.name"
            class="ml-2"
            style="width: 200px;"
            @change="onFilterChange"
        >
            <el-option
                v-for="variant in attribute.variants"
                :key="variant.id"
                :value="variant.id"
                :label="variant.name"
            >
                <el-tag :effect="isUsed(variant.id) ? 'dark' : 'light'" size="small">{{ variant.name }}</el-tag>
            </el-option>
        </el-select>

        <el-button type="primary" @click="onAdd" class="ml-2" :disabled="form.productId === null">
            <i class="fa-light fa-box mr-2"></i>
            Добавить
        </el-button>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import axios from "axios";
import {route} from "ziggy-js";

const props = defineProps({
    modification: Object,
})
const emit = defineEmits(['update:product_id'])

const searchUrl = route('admin.catalog.modification.search-product', {modification: props.modification.id})

const options = ref([])
const loading = ref(false)
const searchQuery = ref('')
const form = reactive({ productId: null })

const filters = reactive({})
for (const attribute of props.modification.attributes) {
    filters[attribute.id] = null
}

function isUsed(variantId) {
    return (props.modification.used_variant_ids || []).includes(variantId)
}

function buildFilters() {
    const result = {}
    for (const [attributeId, variantId] of Object.entries(filters)) {
        if (variantId !== null && variantId !== undefined && variantId !== '') {
            result[attributeId] = variantId
        }
    }
    return result
}

function search() {
    loading.value = true
    axios.post(searchUrl, {
        search: searchQuery.value,
        variants: buildFilters(),
    }).then(response => {
        options.value = response.data.products || []
    }).finally(() => {
        loading.value = false
    })
}

const remoteMethod = (query: string) => {
    searchQuery.value = query
    if (query) {
        search()
    } else {
        options.value = []
    }
}

function onFilterChange() {
    if (searchQuery.value) {
        search()
    } else {
        options.value = []
    }
}

function reset() {
    form.productId = null
    searchQuery.value = ''
    options.value = []
    for (const attribute of props.modification.attributes) {
        filters[attribute.id] = null
    }
}

function onAdd() {
    if (form.productId === null) return
    emit('update:product_id', form.productId)
    reset()
}
</script>
