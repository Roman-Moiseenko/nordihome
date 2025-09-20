<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Виджет {{ widget.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <WidgetInfo :widget="widget" :templates="templates"/>
        </div>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <div class="flex" style="width: 450px;">



            <el-button type="primary" @click="onAddItem">Добавить блок</el-button>
            </div>
        </div>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <WidgetItems :items="widget.items" :tiny_api="tiny_api"/>
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
    widget: Object,
    templates: Array,
    tiny_api: String,
    title: {
        type: String,
        default: 'Карточка виджета',
    },
})
const form = reactive({
    file: null,
    clear_file: false,
})

function onAddItem(val) {
    form.clear_file = val.clear_file;
    form.file = val.file
    router.visit(route('admin.page.widget.text.add-item', {widget: props.widget.id}), {
        method: "post",
        data: {},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            //editBanner.value = false;
        }
    })
}
</script>
