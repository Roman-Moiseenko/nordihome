<template>
    <Head><title>{{ title }}</title></Head>

    <el-collapse accordion>
        <el-collapse-item :name="template.id" v-for="template in dataTemplate" >
            <template #title>
                <span class="ml-3">{{ template.name }}</span>
            </template>
            <el-row :gutter="10">
                <el-col :span="12">
                    <el-form label-width="auto">
                        <el-form-item label="META Title">
                            <el-input v-model="template.title" @change="onChange"/>
                        </el-form-item>

                        <el-form-item label="META Description">
                            <el-input v-model="template.description" type="textarea" rows="5" @change="onChange"/>
                        </el-form-item>
                    </el-form>
                </el-col>
                <el-col :span="12">
                    <div v-for="variable in template.variables" class="mb-1">
                        <el-button type="warning" size="small" dark @click="copyBuffer(variable)">{{ variable }}</el-button>
                    </div>
                </el-col>
            </el-row>
        </el-collapse-item>
    </el-collapse>

    <div v-if="notSave" class="mt-3">
        <el-button type="danger" @click="onCancel">Отмена</el-button>
        <el-button type="success" @click="onSave">Сохранить</el-button>
    </div>
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";

const props = defineProps({
    templates: Array,
    title: {
        type: String,
        default: 'SEO META',
    },
})
const dataTemplate = ref([]);
const notSave = ref(false)

const loadData = () => {
    dataTemplate.value = []
    props.templates.forEach(item => {
        dataTemplate.value.push({
            id: item.id,
            name: item.name,
            title: item.template_title,
            description: item.description_title,
            variables: [...item.variables]
        });
    })
}
loadData();
function onChange() {
    notSave.value = true
}
function onCancel() {
    notSave.value = false
    loadData();
}
function onSave() {
    router.visit(route('admin.page.seo-meta.set-data'), {
        method: "post",
        data: {templates: dataTemplate.value},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            notSave.value = false
        }
    })
}
function copyBuffer(val) {
    navigator.clipboard.writeText(val);
}
</script>
