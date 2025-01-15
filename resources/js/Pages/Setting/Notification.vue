<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto" style="max-width: 500px">

            <el-form-item label="API чат-бота Телеграм">
                <el-input v-model="form.telegram_api" placeholder=""/>
                <div v-if="errors.telegram_api" class="text-red-700">{{ errors.telegram_api }}</div>
            </el-form-item>


            <el-button type="primary" @click="onSubmit">Сохранить</el-button>
            <div v-if="form.isDirty">Изменения не сохранены</div>
        </el-form>
    </div>
</template>


<script lang="ts" setup>
import {reactive} from 'vue'
import {Head, router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"

const props = defineProps({
    errors: Object,
    notification: Object,
    title: {
        type: String,
        default: 'Настройки уведомлений',
    }
});

const form = reactive({
    slug: 'notification',
    telegram_api: props.notification.telegram_api,

    /**
     * Добавить новые поля
     */
})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>

