<template>
    <Head><title>{{ $props.title }}</title></Head>
    <h1 class="font-medium text-xl">Отправить уведомление сотрудникам и персоналу</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <div class="grid lg:grid-cols-2 grid-cols-1 divide-x">
                <div class="p-4">
                    <h2 class="font-medium text-lg">Сотрудники компании</h2>
                    <div v-for="staff in staffs">
                        <el-checkbox v-model="form.staffs" :label="staff.name"
                                     type="checkbox" :disabled="staff.telegram_id === 0"
                                     :value="staff.id"
                        />
                    </div>
                </div>
                <div class="p-4">
                    <h2 class="font-medium text-lg">Персонал компании</h2>
                    <div v-for="employee in employees">
                        <el-checkbox v-model="form.employees" :label="employee.name"
                                     type="checkbox" :disabled="employee.telegram_id === 0"
                                     :value="employee.id"
                        />
                    </div>
                </div>
            </div>

            <el-form-item label="Сообщение" :rules="{required: true}" class="mt-3">
                <el-input v-model="form.message" placeholder="Напишите сообщение сотрудникам" @input="handleMaskName" type="textarea" :rows="2" maxlength="4096" show-word-limit/>
                <div v-if="errors.message" class="text-red-700">{{ errors.message }}</div>
            </el-form-item>
            <el-checkbox v-model="form.confirmation" label="Подтверждение получения"
                         type="checkbox"/>

            <div class="mt-4">
                <el-button type="primary" @click="onSubmit">Отправить</el-button>
            </div>
            <div v-if="form.isDirty">Изменения не сохранены</div>
        </el-form>
    </div>
</template>


<script lang="ts" setup>
import {Head} from '@inertiajs/vue3'
import {reactive} from 'vue'
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"

const props = defineProps({
    errors: Object,
    staffs: Array,
    employees: Array,
    title: {
        type: String,
        default: 'Новое уведомление',
    },
});
const form = reactive({
    staffs: [],
    employees: [],
    message: null,
    confirmation: false,
})
function onSubmit() {
    router.post(route('admin.notification.notification.store'), form)
}
</script>

