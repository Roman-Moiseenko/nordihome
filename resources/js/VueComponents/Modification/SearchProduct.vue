<template>
    <div class="flex">
        <el-select
            id="select"
            v-model="form.productId"
            filterable
            remote
            reserve-keyword
            placeholder="Введите артикул или название"
            :remote-method="remoteMethod"
            :loading="loading"
            style="width: 340px;"
            @keyup.enter="onSelect"
            :disabled="disabledSearch"
        >
            <el-option
                v-for="item in options"
                :key="item.id"
                :value="item.id"
                :label="item.name + ' ('+ item.code + ')'"
            />
        </el-select>
        <el-button id="button" type="primary" @click="onAdd" class="ml-1" :disabled="disabledSearch">
            <i class="fa-light fa-box mr-2"></i>
            Выбрать
        </el-button>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, defineProps } from "vue";
import axios from "axios";
import {route} from "ziggy-js";

const search = route('admin.catalog.modification.search-create')

const props = defineProps({
    action: String,
})
interface ListItem {
    id: Number
    name: String,
    code: String,
}
const options = ref<ListItem[]>([])
const attributes = ref<ListItem[]>([])

const loading = ref(false)
const disabledSearch = ref(false)
const $emit = defineEmits(['update:productId', 'update:name', 'update:attributes'])

const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(search, {search: query}).then(response => {
            if (response.data.error !== undefined) console.log(response.data.error)
            options.value = response.data.products || []
            attributes.value = response.data.attributes || []
            loading.value = false
        });
    } else {
        options.value = []
        attributes.value = []
    }
}
const form = reactive({
    productId: null,
})

const selectedProduct = computed(() =>
    options.value.find(item => item.id === form.productId)
)

function onSelect() {
    document.getElementById('button').focus()
}

function onAdd() {
    if (form.productId === null) return;
    $emit('update:productId', form.productId)
    $emit('update:name', selectedProduct.value ? selectedProduct.value.name : '')
    $emit('update:attributes', attributes.value)
}
</script>
