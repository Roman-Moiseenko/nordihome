<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Виджет {{ product.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <WidgetInfo :product="product" :templates="templates" :banners="banners"/>
        </div>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <div class="flex" style="width: 450px;">
            <el-select v-model="group_id" clearable>
                <el-option v-for="item in groups" :value="item.id" :label="item.name" />
            </el-select>
            <el-button type="primary" @click="onAddItem">Добавить группу</el-button>
            </div>
        </div>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <WidgetItems :items="product.items" />
        </div>

    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'

import WidgetInfo from './Block/Info.vue'
import WidgetItems from './Block/Items.vue'
import UploadImageFile from "@Comp/UploadImageFile.vue";
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    product: Object,
    templates: Array,
    groups: Array,
    banners: Array,
    title: {
        type: String,
        default: 'Карточка виджета',
    },
})
const form = reactive({
    file: null,
    clear_file: false,
})
const group_id = ref(null)
function onAddItem(val) {
    form.clear_file = val.clear_file;
    form.file = val.file
    router.visit(route('admin.page.widget.product.add-item', {product: props.product.id}), {
        method: "post",
        data: {group_id: group_id.value},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            //editBanner.value = false;
        }
    })
}
</script>
