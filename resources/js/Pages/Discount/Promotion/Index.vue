<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Акции продаж</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Новая акция
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="new_promotion" placeholder="Название акции" class="mt-1"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Товар, акция"/>
                <el-select v-model="filter.status" placeholder="Статус" class="mt-1">
                    <el-option v-for="item in statuses" :key="item.value" :value="item.value" :label="item.label"/>
                </el-select>
            </TableFilter>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Название" width="280" show-overflow-tooltip/>
                <el-table-column prop="" label="Начало-Конец" width="300" align="center">
                    <template #default="scope">
                        {{ scope.row.start_at ? func.date(scope.row.start_at) : 'ручной запуск'}}
                        -
                        {{ scope.row.finish_at ? func.date(scope.row.finish_at) : 'бессрочная'}}
                    </template>
                </el-table-column>
                <el-table-column label="Статус">
                    <template #default="scope">
                        <el-tag :type="statusType(scope.row.status)" >{{ statusText(scope.row.status) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Товары" />
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <span v-if="scope.row.active && !scope.row.is_finished">
                            <el-button size="small" type="danger" @click.stop="handleFinish(scope.row)">
                                Stop
                            </el-button>
                        </span>
                        <span v-else-if="!scope.row.is_finished">
                            <span v-if="scope.row.published">
                                <el-button size="small" type="warning" @click.stop="handleToggle(scope.row)">
                                    Hide
                                </el-button>
                                <el-button size="small" type="primary" @click.stop="handleStart(scope.row)">
                                    Start
                                </el-button>
                            </span>
                            <span v-if="!scope.row.published">
                                <el-button size="small" type="success" @click.stop="handleToggle(scope.row)">
                                    Show
                                </el-button>
                            </span>
                            <el-button size="small" type="danger" class="ml-2"
                                       @click.stop="handleDeleteEntity(scope.row)">
                                Delete
                            </el-button>
                        </span>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <pagination
            :current_page="promotions.current_page"
            :per_page="promotions.per_page"
            :total="promotions.total"
        />


        <el-dialog v-model="dialogStop" title="Остановить акцию" width="400" center>
            <div class="font-medium text-md mt-2">
                Не все товары распроданы. Остановить акцию?
            </div>
            <div class="text-red-600 text-md mt-2">
                Повторный запуск будет невозможен
            </div>
            <slot />
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogStop = false">Отмена</el-button>
                    <el-button type="danger" @click="stopAction(stopPromotionId)">
                        Остановить
                    </el-button>
                </div>
            </template>
        </el-dialog>


        <DeleteEntityModal name_entity="Акцию"/>

    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";
import axios from "axios";
import { func } from "@Res/func"


const props = defineProps({
    promotions: Object,
    title: {
        type: String,
        default: 'Список акций',
    },
    filters: Array,
    statuses: Array,
})
const dialogStop = ref(false)
const stopPromotionId = ref(null)
const visible_create = ref(false)
const new_promotion = ref(null)
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.promotions.data])
const filter = reactive({
    name: props.filters.name,
    status: props.filters.status,
})

function createButton() {
    router.post(route('admin.discount.promotion.store', {name: new_promotion.value}))
}
function routeClick(row) {
    router.get(route('admin.discount.promotion.show', {promotion: row.id}))
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.discount.promotion.destroy', {promotion: row.id}));
}
function handleStart(row) {
    router.visit(route('admin.discount.promotion.start', {promotion: row.id}), {
        method: 'post'
    });
}
function handleFinish(row) {
    if (row.quantity > 0) {
        stopPromotionId.value = row.id
        dialogStop.value = true
    } else {
        stopAction(row.id)
    }

}
function stopAction(id) {
    router.visit(route('admin.discount.promotion.stop', {promotion: id}), {
        method: 'post'
    });
}

function handleToggle(row) {
    router.visit(route('admin.discount.promotion.toggle', {promotion: row.id}), {
        method: 'post'
    });
}

function statusText(val) {
    let status = 'error';
    props.statuses.forEach(function (item) {
        if (item.value === val) return status = item.label
    })
    return status
}
function statusType(val) {
    if (val === 104) return 'primary'
    if (val === 103) return 'success'
    if (val === 102) return 'warning'
    if (val === 101) return 'info'
}
</script>
