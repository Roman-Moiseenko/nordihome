<template>
    <el-descriptions v-if="!editWidget" :column="3" border class="mb-5">
        <el-descriptions-item label="Виджет">
            {{ product.name }}
        </el-descriptions-item>
        <el-descriptions-item label="Шаблон">
            {{ product.template }}
        </el-descriptions-item>
        <el-descriptions-item label="Ссылка">
            {{ product.url }}
        </el-descriptions-item>
        <el-descriptions-item label="Активен">
            <Active :active="product.active" />
        </el-descriptions-item>
        <el-descriptions-item label="Заголовок">
            {{ product.caption }}
        </el-descriptions-item>
        <el-descriptions-item label="Описание">
            {{ product.description }}
        </el-descriptions-item>
        <el-descriptions-item label="Баннер">
            {{ (product.banner) ? widget.banner.name : '' }}
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
    product: Object,
    templates: Array,
    banners: Array,
})
console.log(props.product)
const editWidget = ref(false)
const form = reactive({
    name: props.product.name,
    template: props.product.template,
    banner_id: props.product.banner_id,
    url: props.product.url,
    caption: props.product.caption,
    description: props.product.description,
})


function setWidget() {
    router.visit(route('admin.page.widget.product.set-widget', {product: props.product.id}), {
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
