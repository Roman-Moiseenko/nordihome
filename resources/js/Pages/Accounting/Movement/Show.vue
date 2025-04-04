<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Перемещение {{ movement.number }}
            <span v-if="movement.incoming_number">({{ movement.incoming_number }})</span>
            от {{ func.date(movement.created_at) }}
            <el-tag v-if="movement.trashed" type="danger">Удален</el-tag>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <MovementInfo :movement="movement" :storages="storages"/>
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <MovementActions :movement="movement" />
            </div>
        </el-affix>
        <el-table :data="[...movement.products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="tableRowClassName" class="movement-table"
                  style="width: 100%;">
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160" />
            <el-table-column prop="product.name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="quantity" label="Кол-во" width="300">
                <template #default="scope">
                    <el-input v-model="scope.row.quantity"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.quantity_out }} <i class="fa-light fa-arrow-right-to-bracket ml-1"></i></template>
                        <template #append><i class="fa-light fa-arrow-right-from-bracket mr-1"></i> {{ scope.row.quantity_in }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right" width="250">
                <template #default="scope">
                    <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="movement.products.current_page"
            :per_page="movement.products.per_page"
            :total="movement.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из перемещения" />
</template>

<script lang="ts" setup>
import {inject, ref, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import MovementInfo from './Blocks/Info.vue'
import MovementActions from './Blocks/Actions.vue'

const props = defineProps({
    movement: Object,
    title: {
        type: String,
        default: 'Перемещение товаров',
    },
    storages: Array,
    printed: Object,
    filters: Array,
})
provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.movement) //Для общих действий
interface IRow {
    quantity: number,
    quantity_out: number
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.quantity === 0 || row.quantity > row.quantity_out) {
        return 'error-row'
    }
    return ''
}
const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.movement.completed && !props.movement.trashed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.movement.set-product', {product: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.movement.del-product', {product: row.id}));
}
const indexMethod = (index: number) => {
    return index + (props.movement.products.current_page - 1) * props.movement.products.per_page + 1
}
</script>
<style lang="scss">
.movement-table {
    .el-input-group__prepend, .el-input-group__append {
        width: 90px;
    }
}
</style>
