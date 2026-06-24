<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                    <el-image
                        style="width: 100px; height: 100px"
                        :src="staff.photo"
                        :zoom-rate="1.2"
                        :max-scale="7"
                        :min-scale="0.2"
                        :preview-src-list="[staff.photo]"
                        :initial-index="0"
                        fit="cover"
                    />
                </div>
                <div class="ml-5">
                    <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                        {{ func.fullName(staff.fullname) }}
                    </div>
                    <div class="text-slate-500">{{ staff.post }}</div>
                </div>
            </div>
            <el-descriptions :column="1" border title="Персональные данные">
                <el-descriptions-item>
                    <template #label>
                        <div class="items-center flex">
                            <el-icon><User /></el-icon>&nbsp;Логин
                        </div>
                    </template>
                    {{ staff.name }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="items-center flex">
                            <el-icon><iphone  /></el-icon>&nbsp;Телефон
                        </div>
                    </template>
                    {{ func.phone(staff.phone) }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="items-center flex">
                            <el-icon><Promotion /></el-icon>&nbsp;Телеграм ID
                        </div>
                    </template>
                    {{ staff.telegram_user_id }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <div class="items-center flex">
                            <el-icon><Lock /></el-icon>&nbsp;Роль
                        </div>
                    </template>
                    {{ staff.role_name }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="16">
            <el-form-item label="Доступы и уведомления" label-position="top" v-if="staff.show_responsibilities">
                <el-checkbox-group v-model="formResp" class="grid grid-cols-3">
                    <el-checkbox v-for="item in responsibilities" :key="item.value" :value="item.value" :label="item.label"
                                 @change="onChange($event, item.value)"
                                 class="pl-3"
                    />
                </el-checkbox-group>
            </el-form-item>
        </el-col>
    </el-row>
    <div class="flex mt-3">
        <el-button type="warning" @click="showEdit">Редактировать</el-button>
        <el-button plain @click="dialogPassword = true"><i class="fa-light fa-key mr-1"></i>Сменить пароль</el-button>
    </div>

    <el-dialog v-model="dialogPassword" title="Сменить пароль" width="400">
        <el-form >
            <el-form-item :rules="{required: true}">
                <el-input v-model="new_password" autocomplete="off" />
                <div v-if="errors.password" class="text-red-700">{{ errors.password }}</div>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogPassword = false">Отмена</el-button>
                <el-button type="primary" @click="onPassword">
                    Сохранить
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import {defineProps, defineEmits, ref} from 'vue'
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func"


const props = defineProps({
    staff: Object,
    errors: Object,
    responsibilities: Array,
})
const dialogPassword = ref(false)
const new_password = ref(null)
const formResp = ref(props.staff.responsibilities)
function onPassword() {
    router.visit(
        route('admin.staff.password', {staff: props.staff.id}),
        //props.password,
        {
            method: 'post',
            data: {password: new_password.value,},
            preserveState: true,
            preserveScroll: true,
            onSuccess: page => {
                console.log(page.props)
                dialogPassword.value = false
                new_password.value = null;
            },
            onError: page => {

            }
        })
}

function onChange(val, index) {
    router.post(route('admin.staff.responsibility', {staff: props.staff.id}), {code: index});
}
const $emit = defineEmits(['update:show'])

function showEdit() {
    $emit('update:show', true)
}

</script>
<style scoped>

</style>
