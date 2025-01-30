<template>
    <el-descriptions v-if="!editPage" :column="3" border class="mb-5">
        <el-descriptions-item label="Страница">
            {{ page.name }}
        </el-descriptions-item>
        <el-descriptions-item label="Ссылка">
            {{ page.slug }}
        </el-descriptions-item>
        <el-descriptions-item label="Родительская">
            {{ page.parent_name }}
        </el-descriptions-item>
        <el-descriptions-item label="Показывать в меню">
            <Active :active="page.menu" />
        </el-descriptions-item>
        <el-descriptions-item label="Шаблон">
            {{ page.template }}
        </el-descriptions-item>
        <el-descriptions-item label="Мета Заголовок">
            {{ page.title }}
        </el-descriptions-item>
        <el-descriptions-item label="Мета Описание">
            {{ page.description }}
        </el-descriptions-item>
    </el-descriptions>
    <el-button v-if="!editPage" type="warning" @click="editPage = true">Изменить</el-button>
    <el-form v-if="editPage" label-width="auto" style="width: 500px;">
        <el-form-item label="Страница">
            <el-input v-model="form.name"/>
        </el-form-item>
        <el-form-item label="Ссылка">
            <el-input v-model="form.slug" clearable/>
        </el-form-item>
        <el-form-item label="Родительская">
            <el-select v-model="form.parent_id" clearable filterable>
                <el-option v-for="item in pages" :key="item.id" :value="item.id" :label="item.name"/>
            </el-select>
        </el-form-item>
        <el-form-item label="Показывать в меню">
            <el-checkbox v-model="form.menu" :checked="form.menu"/>
        </el-form-item>
        <el-form-item label="Шаблон">
            <el-select v-model="form.template">
                <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
            </el-select>
        </el-form-item>
        <el-form-item label="Мета Заголовок">
            <el-input v-model="form.title"/>
        </el-form-item>
        <el-form-item label="Мета Описание">
            <el-input v-model="form.description" type="textarea" rows="3"/>
        </el-form-item>

        <el-button type="info" @click="editPage = false">Отмена</el-button>
        <el-button type="success" @click="setPage">Сохранить</el-button>
    </el-form>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    page: Object,
    templates: Array,
    pages: Array,
})

const editPage = ref(false)
const form = reactive({
    name: props.page.name,
    slug: props.page.slug,
    parent_id: props.page.parent_id,
    menu: props.page.menu,
    template: props.page.template,
    title: props.page.title,
    description: props.page.description,
})


function setPage() {
    router.visit(route('admin.page.page.set-info', {page: props.page.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editPage.value = false;
        }
    })
}
</script>
