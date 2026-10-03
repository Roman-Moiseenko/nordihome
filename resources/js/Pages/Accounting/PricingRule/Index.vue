<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Правила расчета цены</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="openCreateDialog">
                Создать правило
            </el-button>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="rules"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="show"
            >
                <el-table-column prop="name" label="Название правила"/>
                <el-table-column label="Активно">
                    <template #default="scope">
                        <Active :active="scope.row.isActive"/>
                    </template>
                </el-table-column>
                <el-table-column prop="categoriesCount" label="Кол-во категорий" width="180"/>
                <el-table-column label="Действия" align="right" width="220">
                    <template #default="scope">
                        <el-button size="small" type="primary" @click.stop="show(scope.row)">
                            Show
                        </el-button>
                        <el-button size="small" type="danger" @click.stop="handleDeleteEntity(scope.row)">
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-dialog v-model="dialogVisible" title="Создать правило" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название">
                    <el-input v-model="form.name" placeholder="Название правила"/>
                </el-form-item>
                <el-form-item label="Вес (руб./кг)">
                    <el-input-number v-model="form.ratioWeight" :min="0" :precision="2" style="width: 100%"/>
                </el-form-item>
                <el-form-item label="Наценка">
                    <el-input-number v-model="form.ratioMarkup" :min="0" :precision="3" :step="0.1"
                                     style="width: 100%"/>
                </el-form-item>
                <el-form-item label="Шаг округления">
                    <el-input-number v-model="form.roundingStep" :min="0" style="width: 100%"/>
                </el-form-item>

                <el-form-item label="Вычитание при округлении">
                    <el-input-number v-model="form.roundingSubtract" :min="0" style="width: 100%"/>
                </el-form-item>
                <el-form-item>
                    <el-checkbox v-model="form.isActive">Активно</el-checkbox>
                </el-form-item>

            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogVisible = false">Отмена</el-button>
                    <el-button type="primary" @click="store">Создать</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>
    <DeleteEntityModal name_entity="Правило расчета цены"/>
</template>

<script setup lang="ts">
import {inject, reactive, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    rules: Array,
    title: {
        type: String,
        default: 'Правила расчета цены',
    },
})

const $delete_entity = inject("$delete_entity")

const dialogVisible = ref(false)
const form = reactive({
    name: '',
    ratioWeight: 100,
    ratioMarkup: 1.300,
    roundingStep: 100,
    roundingSubtract: 10,
    isActive: true,
})

function openCreateDialog() {
    form.name = ''
    form.ratioWeight = 100
    form.ratioMarkup = 1.300
    form.roundingStep = 100
    form.roundingSubtract = 10
    form.isActive = true
    dialogVisible.value = true
}

function store() {
    router.post(route('admin.accounting.pricing-rule.store', form), {
        preserveScroll: true,
        onSuccess: () => {
            dialogVisible.value = false
        }
    })
}

function show(row) {
    router.get(route('admin.accounting.pricing-rule.show', {id: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.pricing-rule.destroy', {id: row.id}))
}
</script>
