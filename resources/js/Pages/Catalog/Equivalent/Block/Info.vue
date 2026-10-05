<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="10">
                <el-form-item label="Название группы">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Категория">
                    <el-select v-model="info.categoryId" filterable clearable placeholder="Категория">
                        <el-option v-for="item in useCatalog.categories" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
            Отмена
        </el-button>
        <el-button v-if="hasChanges" type="success" @click="onSetInfo">
            Сохранить
        </el-button>
    </el-form>
</template>

<script setup>
import {reactive, computed} from "vue";
import {router} from "@inertiajs/vue3";
import {useCatalogStore} from "@Res/catalogStore.ts";

const props = defineProps({
    equivalent: Object,
})

const useCatalog = useCatalogStore()

// --- Исходные данные из пропсов (эталон для отмены и сравнения) ---
const initialInfo = reactive({
    name: props.equivalent.name,
    categoryId: props.equivalent.categoryId ?? null,
})

const info = reactive({
    name: initialInfo.name,
    categoryId: initialInfo.categoryId,
})

// --- Отслеживание изменений ---
const hasChanges = computed(() => {
    return ['name', 'categoryId'].some(
        key => JSON.stringify(info[key]) !== JSON.stringify(initialInfo[key])
    )
})

function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    router.visit(
        route('admin.catalog.equivalent.update', {id: props.equivalent.id}), {
            method: "put",
            data: {...info},
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Обновляем эталон реактивно — кнопки "Сохранить"/"Отмена" скроются
                Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
            }
        }
    );
}

</script>

<style scoped>

</style>
