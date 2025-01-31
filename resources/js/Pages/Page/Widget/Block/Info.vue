<template>
    <el-descriptions v-if="!editWidget" :column="3" border class="mb-5">
        <el-descriptions-item label="Виджет">
            {{ widget.name }}
        </el-descriptions-item>
        <el-descriptions-item label="Шаблон">
            {{ widget.template }}
        </el-descriptions-item>
        <el-descriptions-item label="Ссылка">
            {{ widget.url }}
        </el-descriptions-item>
        <el-descriptions-item label="Активен">
            <Active :active="widget.active" />
        </el-descriptions-item>
        <el-descriptions-item label="Заголовок">
            {{ widget.caption }}
        </el-descriptions-item>
        <el-descriptions-item label="Описание">
            {{ widget.description }}
        </el-descriptions-item>
        <el-descriptions-item label="Баннер">
            {{ (widget.banner) ? widget.banner.name : '' }}
        </el-descriptions-item>
    </el-descriptions>
    <el-button v-if="!editWidget" type="warning" @click="editWidget = true">Изменить</el-button>
    <el-form v-if="editWidget" label-width="auto" style="width: 500px;">
        <el-form-item label="Виджет">
            <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="Шаблон">
            <el-select v-model="form.template">
                <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label" />
            </el-select>
        </el-form-item>
        <el-form-item label="Банер">
            <el-select v-model="form.banner_id" clearable>
                <el-option v-for="item in banners" :key="item.id" :value="item.id" :label="item.name" />
            </el-select>
        </el-form-item>
        <el-form-item label="Ссылка">
            <el-input v-model="form.url" />
        </el-form-item>
        <el-form-item label="Заголовок">
            <el-input v-model="form.caption" />
        </el-form-item>
        <el-form-item label="Описание">
            <el-input v-model="form.description" type="textarea" rows="3"/>
        </el-form-item>
        <el-button type="info" @click="editWidget = false">Отмена</el-button>
        <el-button type="success" @click="setWidget">Сохранить</el-button>
    </el-form>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    widget: Object,
    templates: Array,
    banners: Array,
})
console.log(props.widget)
const editWidget = ref(false)
const form = reactive({
    name: props.widget.name,
    template: props.widget.template,
    banner_id: props.widget.banner_id,
    url: props.widget.url,
    caption: props.widget.caption,
    description: props.widget.description,
})


function setWidget() {
    router.visit(route('admin.page.widget.set-widget', {widget: props.widget.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editWidget.value = false;
        }
    })
}
</script>
