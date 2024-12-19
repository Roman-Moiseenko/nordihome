<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Инвентаризация {{ inventory.number }} <span v-if="inventory.incoming_number">({{ inventory.incoming_number }})</span> от {{ func.date(inventory.created_at) }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <InventoryInfo :inventory="inventory" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <InventoryActions :inventory="inventory" />
            </div>
        </el-affix>
        <el-table :data="[...inventory.products.data]"
                  header-cell-class-name="nordihome-header"
                  style="width: 100%;">
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160" />
            <el-table-column prop="product.name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="formal" label="По учету" width="160" >
                <template #default="scope">
                    {{ scope.row.formal }} шт.
                </template>
            </el-table-column>
            <el-table-column prop="quantity" label="Наличие" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.quantity"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit"
                    >
                        <template #append>шт</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column prop="cost" label="Себестоимость" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.cost) }}
                </template>
            </el-table-column>
            <el-table-column prop="cost" label="Сумма" width="180">
                <template #default="scope">
                    {{ func.price((scope.row.quantity - scope.row.formal) * scope.row.cost) }}
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="inventory.products.current_page"
            :per_page="inventory.products.per_page"
            :total="inventory.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из инвентаризации" />
</template>

<script lang="ts" setup>
import {inject, ref, defineProps, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import InventoryInfo from './Blocks/Info.vue'
import InventoryActions from './Blocks/Actions.vue'

const props = defineProps({
    inventory: Object,
    title: {
        type: String,
        default: 'Инвентаризация',
    },
    printed: Object,
    filters: Array,
})
provide('$filters', props.filters)
provide('$printed', props.printed)
const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.inventory.completed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.inventory.set-product', {product: row.id}), {
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
    $delete_entity.show(route('admin.accounting.inventory.del-product', {product: row.id}));
}
const indexMethod = (index: number) => {
    return index + (props.inventory.products.current_page - 1) * props.inventory.products.per_page + 1
}
</script>
