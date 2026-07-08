<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl mb-3">Карточка роли</h1>
        <el-card>
            <el-form
                ref="formRef"
                :model="form"
                label-width="200px"
                label-position="top"
                :disabled="saving"
            >
                <el-row :gutter="10">
                    <el-col :span="6">
                        <el-divider content-position="center">Основная информация</el-divider>

                        <el-form-item label="Название" prop="name">
                            <el-input v-model="form.name" placeholder="manager"/>
                        </el-form-item>

                        <el-form-item label="Описание" prop="description">
                            <el-input v-model="form.description" placeholder="Описание роли"/>
                        </el-form-item>

                        <el-form-item label="Системная">
                            <el-tag :type="form.is_system ? 'danger' : 'info'">
                                {{ form.is_system ? 'Системная' : 'Пользовательская' }}
                            </el-tag>
                        </el-form-item>
                    </el-col>
                    <el-col :span="18">
                        <el-divider content-position="center">Разрешения</el-divider>

                        <div v-if="!useAuth.loaded" class="text-gray-400">Загрузка...</div>
                        <template v-else>
                            <div v-for="group in useAuth.permissions" :key="group.role" class="mb-4">
                                <p class="font-medium text-sm text-gray-600 mb-1">{{ group.description || group.role }}</p>
                                <el-checkbox-group v-model="form.permissions" :disabled="form.is_system">
                                    <el-checkbox
                                        v-for="perm in group.permissions"
                                        :key="perm"
                                        :label="perm"
                                        :value="perm"
                                    >
                                        {{ shortPerm(perm) }}
                                    </el-checkbox>
                                </el-checkbox-group>
                            </div>
                        </template>
                    </el-col>
                </el-row>

                <el-form-item v-if="changeInfo && !form.is_system">
                    <el-button type="primary" :loading="saving" @click="onSave">
                        Сохранить
                    </el-button>
                    <el-button @click="onCancel">Отмена</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {reactive, ref, watch} from 'vue'

import {useAuthStore} from "@Res/authStore";

const props = defineProps({
    role: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Карточка роли',
    },
})

const useAuth = useAuthStore();
const saving = ref(false)

function shortPerm(perm: string): string {
    const parts = perm.split('.');
    return parts.length > 1 ? parts.slice(1).join('.') : perm;
}

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialForm = {
    name: props.role?.name ?? '',
    description: props.role?.description ?? '',
    is_system: props.role?.is_system ?? false,
    permissions: props.role?.permissions ?? [],
}

// --- Форма ---
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

function onSave() {
    saving.value = true;
    router.visit(route('admin.role.update', {role: props.role.id}), {
        method: 'put',
        data: {
            name: form.value.name,
            description: form.value.description,
            permissions: form.value.permissions,
        },
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

function onCancel() {
    form.value = {...initialForm}
    changeInfo.value = false
}
</script>
