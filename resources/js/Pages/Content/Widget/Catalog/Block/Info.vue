<template>
    <el-row :gutter="10" v-if="!editWidget">
        <el-col :span="24">
            <el-descriptions :column="4" border class="mb-5">
                <el-descriptions-item label="Каталог">
                    {{ widget.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Шаблон">
                    {{ widget.template }}
                </el-descriptions-item>
                <el-descriptions-item label="Заголовок">
                    {{ widget.caption }}
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    {{ widget.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
    <el-button v-if="!editWidget" type="warning" @click="editWidget = true">Изменить</el-button>

    <el-form  v-if="editWidget" label-width="auto">
        <el-row :gutter="10">
            <el-col :span="8">
                <el-form-item label="Каталог">
                    <el-input v-model="form.name"/>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="form.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="8">
                <el-form-item label="Заголовок">
                    <el-input v-model="form.caption"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="form.description" type="textarea" rows="3"/>
                </el-form-item>
            </el-col>
        </el-row>
        <el-button type="info" @click="editWidget = false">Отмена</el-button>
        <el-button type="success" @click="setBanner">Сохранить</el-button>
    </el-form>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from "@Comp/UploadImageFile.vue";

const props = defineProps({
    widget: Object,
    templates: Array,
})

const editWidget = ref(false)
const form = reactive({
    name: props.widget.name,
    template: props.widget.template,
    caption: props.widget.caption,
    description: props.widget.description,

})


function setBanner() {
    router.visit(route('admin.content.widget.catalog.set-widget', {widget: props.widget.id}), {
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
