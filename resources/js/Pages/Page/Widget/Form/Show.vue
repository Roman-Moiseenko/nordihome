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
            <el-tabs tab-position="top">
                <FieldsPanel :id="widget.id" :fields="widget.fields" />
                <ListsPanel :id="widget.id" :lists="widget.lists.list" />
            </el-tabs>
        </div>

    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'

import WidgetInfo from './Block/Info.vue'
import FieldsPanel from './Panels/Fields.vue'
import ListsPanel from "./Panels/Lists.vue";


const props = defineProps({
    widget: Object,
    templates: Array,
  //  tiny_api: String,
    title: {
        type: String,
        default: 'Карточка виджета',
    },
})

//console.log(props.widget)

/*
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
*/
</script>
