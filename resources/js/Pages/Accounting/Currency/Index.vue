<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Валюты</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Добавить валюту
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="currency_name" placeholder="Название валюты" class="mt-1" />
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
                v-loading="store.getLoading"
            >
                <el-table-column prop="name" label="Название" width="260"/>
                <el-table-column prop="sign" label="Обозначение" width="160" align="center"/>
                <el-table-column prop="exchange" label="Текущий курс" width="120" align="center"/>

                <el-table-column prop="extra" label="Доп.наценка (%)" width="200" align="center"/>
                <el-table-column prop="default" label="По умолчанию" width="220" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.default" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
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

    </el-config-provider>
    <DeleteEntityModal name_entity="Валюту" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'

import {useStore} from "@Res/store.js"

import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'

const props = defineProps({
    currencies: Array,
    title: {
        type: String,
        default: 'Список валют',
    },
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.currencies])

const currency_name = ref<String>(null)


function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.currency.destroy', {currency: row.id}));
}
function createButton() {
    router.post(route('admin.accounting.currency.store', {name: currency_name.value}))
}
function routeClick(row) {
    router.get(route('admin.accounting.currency.show', {currency: row.id}))
}
</script>

