<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Правила скидок</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Добавить правило
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Название правила" />
                <el-select v-model="form.class" class="mt-1" placeholder="Тип скидки">
                    <el-option v-for="item in types" :value="item.value" :label="item.label" />
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Название" width="280" show-overflow-tooltip/>
                <el-table-column prop="discount" label="Скидка" width="300" align="center">
                    <template #default="scope">
                        {{ scope.row.discount }}%
                    </template>
                </el-table-column>
                <el-table-column prop="caption" label="Условие"/>
                <el-table-column prop="type" label="Тип"/>
                <el-table-column label="Активна">
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button size="small"
                                   :type="scope.row.active ? 'warning' : 'success' "
                                   @click.stop="handleToggle(scope.row)"
                        >
                            {{ scope.row.active ? 'Stop' : 'Active' }}
                        </el-button>
                        <el-button v-if="!scope.row.active" size="small" type="danger" class="ml-2"
                                   @click.stop="handleDeleteEntity(scope.row)">
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <DeleteEntityModal name_entity="Правило скидки"/>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from "@Comp/Elements/Active.vue";
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";

const props = defineProps({
    discounts: Array,
    title: {
        type: String,
        default: 'Список скидок',
    },
    types: Array,
})
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.discounts])
const form = reactive({
    name: null,
    type: null,
})

function createButton() {
    router.post(route('admin.discount.discount.store'), form)
}
function routeClick(row) {
    router.get(route('admin.discount.discount.show', {discount: row.id}))
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.discount.discount.destroy', {discount: row.id}));
}
function handleToggle(row) {
    router.visit(route('admin.discount.discount.toggle', {discount: row.id}), {
        method: 'post'
    });
}

</script>
