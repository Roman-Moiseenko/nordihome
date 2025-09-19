<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Баннер {{ widget.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <BannerInfo :widget="widget" :templates="templates"/>
        </div>

        <UploadImageFile
            label="Новый элемент"
            @selectImageFile="onAddItem"
        />

        <div class="mt-3 p-3 bg-white rounded-lg ">
            <BannerItems :items="widget.items" />
        </div>

    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'

import BannerInfo from './Block/Info.vue'
import BannerItems from './Block/Items.vue'
import UploadImageFile from "@Comp/UploadImageFile.vue";
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    widget: Object,
    templates: Array,
    title: {
        type: String,
        default: 'Карточка баннера',
    },
})
const form = reactive({
    file: null,
    clear_file: false,
})
function onAddItem(val) {
    form.clear_file = val.clear_file;
    form.file = val.file
    router.visit(route('admin.page.widget.banner.add-item', {widget: props.widget.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            //editBanner.value = false;
        }
    })
}
</script>
