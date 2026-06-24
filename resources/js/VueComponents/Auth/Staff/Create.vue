<script setup lang="ts">
import {reactive, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {func} from "@Res/func";
import {useAuthStore} from "@Res/authStore";

const props = defineProps({
    modelValue: Boolean,
    errors: Object,
})

const useAuth = useAuthStore();
const emit = defineEmits(['update:modelValue'])

const login = ref(null)

const form = reactive({
    lastName: null,
    firstName: null,
    middleName: null,
    workPhone: null,
    telegramChatId: null,
    workEmail: null,
    positions: [],
    password: null,
})

watch(login, (val) => {
    if (val && val.trim()) {
        form.workEmail = `${val.trim()}@nordihome.ru`;
    } else {
        form.workEmail = null;
    }
})

function saveStaff() {
    router.visit(route('admin.staff.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            emit('update:modelValue', false);
            router.get(route('admin.staff.show', {staff: page.props.staff?.id}));
        }
    })
}

function handleClose() {
    emit('update:modelValue', false);
}
</script>

<template>
    <el-dialog :model-value="props.modelValue" @update:model-value="val => emit('update:modelValue', val)" title="Новый сотрудник" width="500" @close="handleClose">
        <el-form label-width="auto">
            <el-form-item label="Фамилия" class="mt-3">
                <el-input v-model="form.lastName" placeholder="Фамилия"/>
                <div v-if="errors.lastName" class="text-red-700">{{ errors.surname }}</div>
            </el-form-item>
            <el-form-item label="Имя" class="mt-3">
                <el-input v-model="form.firstName" placeholder="Имя"/>
                <div v-if="errors.firstName" class="text-red-700">{{ errors.firstname }}</div>
            </el-form-item>
            <el-form-item label="Отчество" class="mt-3">
                <el-input v-model="form.middleName" placeholder="Отчество"/>
                <div v-if="errors.middleName" class="text-red-700">{{ errors.secondname }}</div>
            </el-form-item>
            <el-divider/>
            <el-form-item label="Телефон" class="mt-3">
                <el-input v-model="form.workPhone" :formatter="val => func.MaskPhone(val)"/>
                <div v-if="errors.workPhone" class="text-red-700">{{ errors.workPhone }}</div>
            </el-form-item>

            <el-form-item label="Телеграм" class="mt-3">
                <el-input v-model="form.telegramChatId" :formatter="val => func.MaskInteger(val)"/>
                <div v-if="errors.telegramChatId" class="text-red-700">{{ errors.telegramChatId }}</div>
            </el-form-item>

            <el-form-item label="Роль" class="mt-3">
                <el-select v-model="form.positions" multiple>
                    <el-option v-for="item in useAuth.positions" :value="item.value" :label="item.label" />
                </el-select>
                <div v-if="errors.position" class="text-red-700">{{ errors.role }}</div>
            </el-form-item>

            <el-divider>Аутентификация</el-divider>
            <el-form-item label="Логин" class="mt-3">
                <el-input v-model="login" :formatter="val => func.MaskLogin(val)"/>
                <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
            </el-form-item>
            <el-form-item label="Пароль" class="mt-3">
                <el-input v-model="form.password"/>
                <div v-if="errors.password" class="text-red-700">{{ errors.password }}</div>
            </el-form-item>
            <el-form-item label="Email" class="mt-3">
                <el-input v-model="form.workEmail" disabled/>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="handleClose">Отмена</el-button>
                <el-button type="primary" @click="saveStaff">Сохранить</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<style scoped>

</style>
