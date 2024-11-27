<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">
        Подписка {{ subscription.name }}
    </h1>
    <div class="mt-2 p-5 bg-white rounded-md">
        <el-row :gutter="10">
            <el-col :span="8">
                <el-descriptions v-if="!showEdit" column="1" border>
                    <el-descriptions-item label="Название">
                        {{ subscription.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Заголовок для клиентов">
                        {{ subscription.title }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Описание для клиентов">
                        {{ subscription.description }}
                    </el-descriptions-item>
                </el-descriptions >
                <el-button type="primary" class="mt-4" v-if="!showEdit" @click="showEdit = true">Редактировать</el-button>
                <el-form v-if="showEdit" label-width="auto">
                    <el-form-item label="Название">
                        <el-input v-model="form.name"/>
                    </el-form-item>
                    <el-form-item label="Заголовок для клиентов">
                        <el-input v-model="form.title"/>
                    </el-form-item>
                    <el-form-item label="Описание для клиентов">
                        <el-input v-model="form.description" type="textarea" :rows="2"/>
                    </el-form-item>
                    <el-button type="info" @click="showEdit = false">Отмена</el-button>
                    <el-button type="primary" @click="setInfo">Сохранить</el-button>
                </el-form>
            </el-col>
        </el-row>
    </div>
    <el-tag class="mt-5" type="info">На будущее: список подписчиков</el-tag>
</template>

<script setup>
import {defineProps, reactive, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";

const props = defineProps({
    subscription: Object,
    title: {
        type: String,
        default: 'Карточка подписки',
    },
})

const showEdit = ref(null)
const form = reactive({
    name: props.subscription.name,
    title: props.subscription.title,
    description: props.subscription.description,
})

function setInfo() {
    router.visit(route('admin.user.subscription.set-info', {subscription: props.subscription.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            showEdit.value = false;
        }
    })
}
</script>

<style scoped>

</style>
