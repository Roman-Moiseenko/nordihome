<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Транспорт доставки</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onOpenDialog" ref="buttonRef">
                Добавить транспорт
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Название" width="280" show-overflow-tooltip/>
                <el-table-column prop="weight" label="Грузоподъемность (кг)" width="280"/>
                <el-table-column prop="volume" label="Объем (м3)" width="280"/>
                <el-table-column prop="active" label="Активен" width="180" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            :type="scope.row.active ? 'warning' : 'success'"
                            @click.stop="onToggle(scope.row)"
                        >
                            {{ scope.row.active ? 'Draft' : 'Active' }}
                        </el-button>
                        <el-button v-if="!scope.row.active"
                                   size="small"
                                   type="danger"
                                   @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <DeleteEntityModal name_entity="Автомобиль"/>

        <el-dialog v-model="dialogCreate" title="Автомобиль доставки" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name" />
                </el-form-item>
                <el-form-item label="Грузоподъемность (кг)" label-position="top" class="mt-3">
                    <el-input v-model="form.weight" :formatter="val => func.MaskInteger(val)"/>
                </el-form-item>
                <el-form-item label="Объем (м3)" label-position="top" class="mt-3">
                    <el-input v-model="form.volume" :formatter="val => func.MaskFloat(val)"/>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveTruck">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";
import {func} from "@Res/func.js"
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";

import {route} from "ziggy-js";
import axios from "axios";


const props = defineProps({
    trucks: Array,
    title: {
        type: String,
        default: 'Транспорт доставки',
    },
})

const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.trucks])


const form = reactive({
    id: null,
    name: null,
    weight: null,
    volume: null,

})

function onOpenDialog() {
    form.id = null
    form.name = null
    form.weight = null
    form.volume = null
    dialogCreate.value = true
}

function saveTruck() {
    let _route = '';
    if (form.id === null) {
        _route = route('admin.delivery.truck.store' )
    } else {
        _route = route('admin.delivery.truck.set-info', {truck: form.id})
    }
    router.visit(_route , {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
    })
    dialogCreate.value = false
}

function routeClick(row) {
    form.id = row.id
    form.name = row.name
    form.weight = row.weight
    form.volume = row.volume
    dialogCreate.value = true
}

function onToggle(row) {
    router.visit(route('admin.delivery.truck.toggle', {truck: row.id}) , {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.delivery.truck.destroy', {truck: row.id}));
}
</script>
