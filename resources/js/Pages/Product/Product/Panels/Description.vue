<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-memo-pad"></i>
                <span> Описание</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-checkbox v-if="product.modification"
                     v-model="form.modification"
                     :checked="form.modification" class="checkbox-warning">
            Сохранять для всех товаров из Модификации
        </el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="12">

                <el-form label-width="auto">
                    <el-form-item label="Полное описание" label-position="top">
                        <el-input v-model="form.description" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="16" resize="none"/>
                        <div v-if="errors.description" class="text-red-700">{{ errors.description }}</div>
                    </el-form-item>
                    <!-- Повторить -->
                </el-form>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12">
                <el-form label-width="auto">
                    <el-form-item label="Краткое описание" label-position="top">
                        <el-input v-model="form.short" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="10" resize="none"/>
                        <div v-if="errors.short" class="text-red-700">{{ errors.short }}</div>
                    </el-form-item>
                    <!-- Повторить -->
                    <el-form-item label="Метки" label-position="left">
                        <el-select v-model="form.tags" @change="onAutoSave" :disabled="isSaving" multiple filterable allow-create>
                            <el-option v-for="item in tags" :key="item.id" :value="item.id" :label="item.name" />
                        </el-select>
                        <div v-if="errors.tags" class="text-red-700">{{ errors.tags }}</div>
                    </el-form-item>
                    <el-form-item label="Серия" label-position="left">
                        <el-select v-model="form.series_id" @change="onAutoSave" :disabled="isSaving" filterable allow-create>
                            <el-option v-for="item in series" :key="item.id" :value="item.id" :label="item.name" />
                        </el-select>
                        <div v-if="errors.tags" class="text-red-700">{{ errors.tags }}</div>
                    </el-form-item>
                    <el-form-item label="Модель" label-position="left">
                        <el-input v-model="form.model" @change="onAutoSave" :disabled="isSaving" />
                        <div v-if="errors.model" class="text-red-700">{{ errors.model }}</div>
                    </el-form-item>
                </el-form>
            </el-col>
            <!-- Колонка 3 -->
            <el-col :span="4">
                <el-form label-width="auto">
                    <el-form-item>

                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps, computed } from "vue"
import {router} from "@inertiajs/vue3"




const props = defineProps({
    product: Object,
    errors: Object,
    tags: Array,
    series: Array,
})


const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    description: props.product.description,
    short: props.product.short,
    tags: [...props.product.tags.map(item => item.id)],
    series_id: props.product.series_id,
    model: props.product.model,
    modification: props.product.modification,
})


function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.description', {product: props.product.id}), {
        method: "post",
        data: form,
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
            form.tags = [...page.props.product.tags.map(item => item.id)]
        },
        onError: page => {
            isSaving.value = false

        },
    })
}

</script>

<style scoped>

</style>
