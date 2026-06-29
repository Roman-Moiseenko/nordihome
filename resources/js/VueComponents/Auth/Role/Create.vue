<script setup lang="ts">
import {reactive} from "vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps({
    modelValue: Boolean,
    errors: Object,
})

const emit = defineEmits(['update:modelValue'])

const form = reactive({
    name: null,
    description: null,
})

function saveRole() {
    router.visit(route('admin.role.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            emit('update:modelValue', false);
        }
    })
}

function handleClose() {
    emit('update:modelValue', false);
}
</script>

<template>
    <el-dialog :model-value="props.modelValue" @update:model-value="val => emit('update:modelValue', val)" title="Новая роль" width="500" @close="handleClose">
        <el-form label-width="auto">
            <el-form-item label="Название" class="mt-3">
                <el-input v-model="form.name" placeholder="Например: manager"/>
                <div v-if="errors?.name" class="text-red-700">{{ errors.name }}</div>
            </el-form-item>
            <el-form-item label="Описание" class="mt-3">
                <el-input v-model="form.description" placeholder="Описание роли"/>
                <div v-if="errors?.description" class="text-red-700">{{ errors.description }}</div>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="handleClose">Отмена</el-button>
                <el-button type="primary" @click="saveRole">Сохранить</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<style scoped>

</style>
