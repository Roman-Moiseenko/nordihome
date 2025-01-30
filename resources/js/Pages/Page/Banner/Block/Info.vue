<template>
    <el-descriptions v-if="!editBanner" :column="3" border class="mb-5">
        <el-descriptions-item label="Баннер">
            {{ banner.name }}
        </el-descriptions-item>
        <el-descriptions-item label="Шаблон">
            {{ banner.template }}
        </el-descriptions-item>
        <el-descriptions-item label="Заголовок">
            {{ banner.caption }}
        </el-descriptions-item>
        <el-descriptions-item label="Описание">
            {{ banner.description }}
        </el-descriptions-item>
    </el-descriptions>
    <el-button v-if="!editBanner" type="warning" @click="editBanner = true">Изменить</el-button>
    <el-form v-if="editBanner" label-width="auto">
        <el-form-item label="Баннер">
            <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="Шаблон">
            <el-select v-model="form.template">
                <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label" />
            </el-select>
        </el-form-item>
        <el-form-item label="Заголовок">
            <el-input v-model="form.caption" />
        </el-form-item>
        <el-form-item label="Описание">
            <el-input v-model="form.description" type="textarea" rows="3"/>
        </el-form-item>

        <el-button type="info" @click="editBanner = false">Отмена</el-button>
        <el-button type="success" @click="setBanner">Сохранить</el-button>
    </el-form>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    banner: Object,
    templates: Array,
})

const editBanner = ref(false)
const form = reactive({
    name: props.banner.name,
    template: props.banner.template,

    caption: props.banner.caption,
    description: props.banner.description,
})


function setBanner() {
    router.visit(route('admin.page.banner.set-banner', {banner: props.banner.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editBanner.value = false;
        }
    })
}
</script>
