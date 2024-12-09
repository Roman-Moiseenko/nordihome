<template>
    <div class="flex">
        <el-select
            id="select"
            v-model="form.product_id"
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
            >
            </el-option>
        </el-select>
        <el-button id="button" type="primary" @click="onAdd" class="ml-1" :disabled="disabledSearch">
            <i class="fa-light fa-box mr-2"></i>
            Выбрать
        </el-button>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref, defineProps } from "vue";
import axios from "axios";
import {route} from "ziggy-js";

const search = route('admin.product.modification.search')

const props = defineProps({
    action: String,
})
interface ListItem {
    id: Number
    name: String,
    code: String,
}
const options = ref<ListItem[]>([])

const loading = ref(false)
const disabledSearch = ref(false)
const $emit = defineEmits(['update:product_id'])

const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(search, {search: query, action: props.action,}).then(response => {
            console.log(response.data)
            if (response.data.error !== undefined) console.log(response.data.error)
            options.value = response.data
            loading.value = false
        });
    } else {
        options.value = []
    }
}
const form = reactive({
    product_id: null,
})

function onSelect() {
    document.getElementById('button').focus()
}

function onAdd() {
    if (form.product_id === null) return;
    $emit('update:product_id', form.product_id)
}
</script>

