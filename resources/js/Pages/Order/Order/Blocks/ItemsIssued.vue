<template>
    <el-table
        :data="tableData"
        header-cell-class-name="nordihome-header"
        style="width: 100%;"
    >
        <el-table-column type="index" label="п/п"/>
        <el-table-column prop="product.code" label="Артикул" width="110"/>
        <el-table-column prop="product.name" label="Товар / Вес, Объем" width="240" show-overflow-tooltip>
            <template #default="scope">
                {{ scope.row.product.name }} <span v-if="scope.row.preorder" class="font-medium ml-2">(предзаказ)</span>
                <div>
                    {{ scope.row.product.weight }}кг / {{ scope.row.product.volume }}м3
                </div>
            </template>
        </el-table-column>
        <el-table-column label="Продажа" width="230" align="center">
            <template #default="scope">
                <span class="text-green-800">{{ func.price(scope.row.sell_cost) }}</span>
                <span v-if="scope.row.percent > 0" class="text-green-800 ml-2"> -{{
                        scope.row.percent
                    }}%</span>
            </template>
        </el-table-column>
        <el-table-column label="Кол-во" width="110" align="center">
            <template #default="scope">

                <span>
                    <span class="text-green-800">{{ scope.row.quantity }}</span>
                </span>
            </template>
        </el-table-column>
        <el-table-column v-if="is_new" prop="product.quantity_sell" label="Наличие" width="90" align="center"/>


        <el-table-column label="Выдача" width="240">
            <template #default="scope">
                <div v-if="scope.row.reserves">
                    <el-input v-model="scope.row.remains" style="width: 60px;" :disabled="!scope.row.issued"/>
                    <el-checkbox v-model="scope.row.issued" :checked="scope.row.issued" class="ml-2 my-auto">На выдачу</el-checkbox>
                </div>
                <div v-if="scope.row.preorder">
                    <el-button v-if="!scope.row.supply_stack" type="primary" class="p-4 my-3"
                               @click="toStackSupply(scope.row)" ref="buttonRef">
                        В Заказ поставщику
                    </el-button>

                    <span v-if="scope.row.supply_stack">
                        <Link v-if="scope.row.supply_stack.supply_id" type="primary" :href="route('admin.accounting.supply.show', {supply: scope.row.supply_stack.supply_id})">
                            {{ scope.row.supply_stack.status_text }}
                        </Link>
                        <el-tag v-else type="warning" size="large" effect="dark">{{ scope.row.supply_stack.status_text }}</el-tag>
                    </span>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="Резерв по складам" width="">
            <template #default="scope">
                <div v-for="storage in scope.row.storages" class="flex">
                    <div class="my-auto items-center" style="width: 110px;">
                        {{ storage.name }}
                    </div>
                    <div style="width: 60px;">
                        <el-tooltip effect="dark" placement="top-start" content="Всего на складе">
                            <el-tag :type="notStorage(scope.row, storage) ? 'danger' : 'info'" size="large" effect="light" class="ml-2">
                                {{ storage.quantity }}
                            </el-tag>
                        </el-tooltip>
                    </div>
                    <div style="width: 40px;">
                        <el-tooltip effect="dark" placement="top-start" content="Резерв по другим заказам">
                            <el-tag type="info" size="large" effect="plain" class="ml-2">
                                {{ storage.reserve_other }}
                            </el-tag>
                        </el-tooltip>
                    </div>
                    <div>
                        <el-tooltip effect="dark" placement="top-start" content="В резерве по заказу">
                            <el-tag type="info" size="large" effect="dark" class="ml-2">
                                {{ storage.reserve }}
                            </el-tag>
                        </el-tooltip>

                        <el-tooltip effect="dark" placement="top-start" content="Перенести резерв 1 шт">
                            <el-button type="info"  plain class="" @click="onUpReserve(scope.row.id, storage.id, 1)"
                                       :disabled="!scope.row.reserves"
                            >
                                <i class="fa-light fa-chevron-left"></i>
                            </el-button>
                        </el-tooltip>
                        <el-tooltip effect="dark" placement="top-start" content="Перенести резерв Все">
                            <el-button type="info" plain class="" @click="onUpReserve(scope.row.id, storage.id, scope.row.quantity)"
                                       :disabled="!scope.row.reserves" style="margin-left: 0;">
                                <i class="fa-light fa-chevrons-left"></i>
                            </el-button>
                        </el-tooltip>
                    </div>
                </div>
            </template>
        </el-table-column>
        <el-table-column prop="comment" align="right" label="Комментарий" width="120" show-overflow-tooltip />
    </el-table>
</template>

<script setup lang="ts">
import {computed, inject, ref} from "vue"
import {func} from "@Res/func.js"
import Active from "@Comp/Elements/Active.vue";
import {router, Link} from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
})

const iSaving = ref(false)
const {is_new, is_issued, is_view} = inject('$status')
const tableData = ref([...props.items])

const tableDate = [...props.items.map(item => {
    item.issued = !item.preorder
    return item
})]

//Проверяем хватает ли на складе для выдачи товара
function notStorage(row, storage) {
    return row.remains > (storage.quantity - storage.reserve_other)
}

function toStackSupply(row) {
    //TODO
    router.visit(route('admin.accounting.supply.add-stack', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {

        },
    })
}
function onUpReserve(item, storage, quantity) {
    router.visit(route('admin.order.reserve-collect', {item: item}), {
        method: "post",
        data: {
            storage_id: storage,
            quantity: quantity
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {

        },
    })
}
</script>

<style lang="scss">
.el-input-group__append {
    padding: 0 8px;
}

.el-input[class*=bg-] {
    > .el-input__wrapper {
        background-color: unset;
    }
}
</style>
