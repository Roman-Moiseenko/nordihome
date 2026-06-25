<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl mb-3">Карточка сотрудника</h1>
        <el-card>
            <el-form
                ref="formRef"
                :model="form"
                label-width="200px"
                label-position="top"
                :disabled="saving"
            >
                <el-row :gutter="10">
                    <el-col :span="8">
                        <el-divider content-position="center">Основная информация</el-divider>
                        <el-form-item label="Фамилия" prop="lastName">
                            <el-input v-model="form.lastName" placeholder="Иванов"/>
                        </el-form-item>

                        <el-form-item label="Имя" prop="firstName">
                            <el-input v-model="form.firstName" placeholder="Иван"/>
                        </el-form-item>

                        <el-form-item label="Отчество" prop="middleName">
                            <el-input v-model="form.middleName" placeholder="Иванович"/>
                        </el-form-item>
                        <el-form-item label="Роли" prop="positions">
                            <el-select v-model="form.positions" multiple>
                                <el-option v-for="item in useAuth.positions"
                                           :label="item.label"
                                           :value="item.value"
                                           :key="item.value"
                                />
                            </el-select>
                        </el-form-item>

                    </el-col>
                    <el-col :span="8">
                        <el-divider content-position="center">Контакты</el-divider>

                        <el-form-item label="Рабочий email" prop="workEmail">
                            <el-input v-model="form.workEmail" placeholder="ivanov@company.ru"/>
                        </el-form-item>

                        <el-form-item label="Рабочий телефон" prop="workPhone">
                            <el-input v-model="form.workPhone" placeholder="+7 (999) 123-45-67"/>
                        </el-form-item>

                        <el-form-item label="Личный телефон" prop="personalPhone">
                            <el-input v-model="form.personalPhone" placeholder="+7 (999) 123-45-67"/>
                        </el-form-item>
                        <el-form-item label="Telegram Chat ID" prop="telegramChatId">
                            <el-input v-model="form.telegramChatId" placeholder="123456789"/>
                        </el-form-item>

                        <el-form-item label="Max Chat ID" prop="maxChatId">
                            <el-input v-model="form.maxChatId" placeholder="987654321"/>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-divider content-position="center">Дополнительно</el-divider>

                        <el-form-item label="Отдел" prop="department">
                            <el-input v-model="form.department" placeholder="Отдел продаж"/>
                        </el-form-item>

                        <el-form-item label="Дата приёма" prop="hireDate">
                            <el-date-picker
                                v-model="form.hireDate"
                                type="date"
                                placeholder="Выберите дату"
                                format="YYYY-MM-DD"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>

                        <el-form-item label="Дата рождения" prop="birthDate">
                            <el-date-picker
                                v-model="form.birthDate"
                                type="date"
                                placeholder="Выберите дату"
                                format="YYYY-MM-DD"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>

                        <el-form-item label="Заметки" prop="notes">
                            <el-input
                                v-model="form.notes"
                                type="textarea"
                                :rows="3"
                                placeholder="Дополнительная информация"
                            />
                        </el-form-item>

                        <el-form-item label="Статус">
                            <el-switch
                                v-model="form.terminated"
                                active-text="Уволен"
                                inactive-text="Активен"
                                inline-prompt
                            />
                        </el-form-item>

                    </el-col>
                </el-row>


                <el-form-item v-if="changeInfo">
                    <el-button type="primary" :loading="saving" @click="onSaveInfo">
                        Сохранить
                    </el-button>
                    <el-button @click="onCancelInfo">Отмена</el-button>
                </el-form-item>

            </el-form>


            <el-divider content-position="left">Доступ к системе</el-divider>

            <el-form-item label="Предоставить доступ">
                <el-switch
                    v-model="hasAccess"
                    active-text="Доступ предоставлен"
                    inactive-text="Нет доступа"
                    inline-prompt
                />
            </el-form-item>

            <template v-if="hasAccess">
                <el-form
                    ref="userFormRef"
                    :model="userForm"
                    label-width="200px"
                    label-position="left"
                    style="width: 500px"
                    :disabled="saving"
                >
                    <el-form-item label="Login" prop="name">
                        <el-input v-model="userForm.name" placeholder="ivanov" autocomplete="off" >
                            <template #append>@nordihome.ru</template>
                        </el-input>
                    </el-form-item>

                    <el-form-item label="Пароль" prop="password">
                        <el-input
                            v-model="userForm.password"
                            type="password"
                            show-password
                            autocomplete="new-password"
                            :placeholder="passwordPlaceholder"
                        />
                    </el-form-item>
                    <el-form-item label="Роли" prop="roleNames">
                        <el-select
                            v-model="userForm.roleNames"
                            multiple
                            placeholder="Выберите роли"
                            style="width: 100%"
                            :loading="!useAuth.loaded"
                        >
                            <el-option
                                v-for="r in useAuth.roles"
                                :key="r.name"
                                :label="r.description || r.name"
                                :value="r.name"
                            >
                                <span>{{ r.description || r.name }}</span>
                                <span class="text-gray-400 text-xs ml-2">({{ r.name }})</span>
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item v-if="changeAccess">
                        <el-button type="primary" :loading="saving" @click="onSaveAccess">
                            Сохранить
                        </el-button>
                        <el-button @click="onCancelAccess">Отмена</el-button>
                    </el-form-item>
                </el-form>
            </template>
        </el-card>
    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {computed, reactive, ref, watch} from 'vue'
import {ElMessage} from 'element-plus'

import {useAuthStore} from "@Res/authStore";

const props = defineProps({
    staff: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Карточка сотрудника',
    },
})

const useAuth = useAuthStore();
const saving = ref(false)

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialForm = {
    lastName: props.staff?.lastName ?? '',
    firstName: props.staff?.firstName ?? '',
    middleName: props.staff?.middleName ?? '',
    positions: props.staff?.positions ?? [],
    department: props.staff?.department ?? '',
    workPhone: props.staff?.workPhone ?? '',
    personalPhone: props.staff?.personalPhone ?? '',
    workEmail: props.staff?.workEmail ?? '',
    hireDate: props.staff?.hireDate ?? '',
    birthDate: props.staff?.birthDate ?? '',
    telegramChatId: props.staff?.telegramChatId ?? '',
    maxChatId: props.staff?.maxChatId ?? '',
    notes: props.staff?.notes ?? '',
    terminated: props.staff?.terminated ?? false,
}

const hasUser = computed(() => props.staff?.user !== null && props.staff?.user !== undefined)
const initialUserForm = {
    active: props.staff?.user?.active ?? true,
    name: props.staff?.user ? props.staff.user.email.replace('@nordihome.ru', '') : '',
    password: '',
    roleNames: props.staff?.user?.roleNames ?? [],
}

// --- Сведения о сотруднике ---
const form = ref({...initialForm})
const changeInfo = ref(false)

watch(form, () => {
    changeInfo.value = hasFormChanged()
}, {deep: true})

function hasFormChanged(): boolean {
    for (const key of Object.keys(initialForm)) {
        const a = JSON.stringify((form.value as any)[key])
        const b = JSON.stringify((initialForm as any)[key])
        if (a !== b) return true
    }
    return false
}

function onSaveInfo() {
    saving.value = true;
    router.visit(route('admin.staff.update', {staff: props.staff.id}), {
        method: 'put',
        data: form.value,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            saving.value = false
            changeInfo.value = false
        },
        onError: errors => {
            saving.value = false
        }
    });
}

function onCancelInfo() {
    form.value = {...initialForm}
    changeInfo.value = false
}

// --- Доступ к системе ---
const hasAccess = ref(props.staff?.user !== null)

const userForm = ref({...initialUserForm})
const changeAccess = ref(false)

const passwordPlaceholder = computed(() =>
    hasUser.value ? 'Оставьте пустым, если не меняете' : 'Минимум 6 символов'
)

watch(userForm, () => {
    changeAccess.value = hasUserFormChanged()
}, {deep: true})

function hasUserFormChanged(): boolean {
    const aName = JSON.stringify(userForm.value.name)
    const aPassword = JSON.stringify(userForm.value.password)
    const aRoles = JSON.stringify([...userForm.value.roleNames].sort())
    const bName = JSON.stringify(initialUserForm.name)
    const bPassword = JSON.stringify('')
    const bRoles = JSON.stringify([...initialUserForm.roleNames].sort())
    return aName !== bName || aPassword !== bPassword || aRoles !== bRoles
}

function onSaveAccess() {
    // Если пользователь уже существует и пароль не меняли — отправляем null
    // Если пользователь новый — пароль обязателен
    if (!hasUser.value && userForm.value.password === '') {
        ElMessage.warning('Для нового пользователя необходимо указать пароль')
        return
    }

    const passwordValue = userForm.value.password === '' && hasUser.value
        ? null
        : userForm.value.password

    const formAccess = reactive({
        active: hasAccess.value,
        email: userForm.value.name + '@nordihome.ru',
        password: passwordValue,
        roleNames: [...userForm.value.roleNames],
    })
    saving.value = true;
    router.visit(route('admin.staff.user', {id: props.staff.id}), {
        method: 'post',
        data: formAccess,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            saving.value = false
            changeAccess.value = false
        },
        onError: errors => {
            saving.value = false
        }
    });
}

function onCancelAccess() {
    userForm.value = {...initialUserForm}
    changeAccess.value = false
}

</script>
