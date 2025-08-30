<template>
    <el-table
        :data="tableData"
        header-cell-class-name="nordihome-header"
        style="width: 100%;"
    >
        <el-table-column type="index" label="п/п" align="center"/>
        <el-table-column prop="product.code" label="Артикул" width="100" align="center"/>
        <el-table-column prop="product.name" label="Товар / Вес, Объем" width="260" show-overflow-tooltip>
            <template #default="scope">
                {{ scope.row.product.name}}
                <div>
                    {{ scope.row.product.weight }}кг / {{ scope.row.product.volume }}м3
                </div>
            </template>
        </el-table-column>
        <el-table-column v-if="is_new" label="Базовая" width="100" align="center">
            <template #default="scope">
                <span :class="scope.row.percent > 0 ? 'text-red-800 font-medium' : ''">{{
                        func.price(scope.row.base_cost)
                    }}</span>
                <div v-if="isProm(scope.row)" class="text-red-800">
                    Акция
                </div>
            </template>
        </el-table-column>
        <el-table-column label="Продажа" width="90" align="center">
            <template #default="scope">
                <span v-if="is_new" class="flex">
                    <el-input
                        v-model="scope.row.sell_cost"
                        :formatter="val => func.MaskInteger(val)"
                        @change="setProduct(scope.row)"
                        :disabled="iSaving || isProm(scope.row)"
                        style="width: 86px;">
                    </el-input>
                </span>
                <span v-else>
                    <span class="text-green-800">{{ func.price(scope.row.sell_cost) }}</span>
                    <span v-if="scope.row.percent > 0" class="text-green-800 ml-2"> -{{
                            scope.row.percent
                        }}%</span>
                </span>
            </template>
        </el-table-column>
        <el-table-column v-if="is_new" label="Скид. %" width="70" align="center">
            <template #default="scope">
                <el-input
                    v-model="scope.row.percent"
                    :formatter="val => func.MaskFloat(val, 0, 100)"
                    @change="setProduct(scope.row)"
                    :disabled="iSaving || isProm(scope.row)"
                    :class="(scope.row.percent > 0 ? 'bg-red-100' : '') + ' m-0'" style="width: 60px;">
                </el-input>
            </template>
        </el-table-column>
        <el-table-column label="Кол-во" width="60" align="center">
            <template #default="scope">
                <span v-if="is_new">
                    <el-input
                        v-model="scope.row.quantity"
                        :formatter="val => func.MaskFloat(val)"
                        @change="setProduct(scope.row)"
                        :disabled="iSaving"
                        style="width: 50px;">
                        <!--template #append>{{ scope.row.product.measuring }}</template-->
                    </el-input>
                </span>
                <span v-else>
                    <span class="text-green-800">{{ scope.row.quantity }}</span>
                </span>
            </template>
        </el-table-column>
        <el-table-column v-if="is_new" label="Сумма" width="80" align="center">
            <template #default="scope">
                <el-tag size="large" type="success" effect="dark">{{ func.price(scope.row.quantity * scope.row.sell_cost) }}</el-tag>
            </template>
        </el-table-column>

        <el-table-column v-if="is_new" prop="quantity_sell" label="Налич." width="80" align="center" >
        </el-table-column>
        <el-table-column prop="assemblage" width="50" align="center">
            <template #header>
                <el-tooltip content="Сборка у заказчика">Сб </el-tooltip>
                <el-checkbox v-model="checkedAssemblage" :checked="checkedAssemblage" @change="checkAssemblage" />
            </template>
            <template #default="scope">
                <el-checkbox v-if="is_new"
                             :checked="scope.row.assemblage"
                             @change="val => setAssemblage(val, scope.row)"
                             :disabled="iSaving"
                />
                <Active v-else :active="scope.row.assemblage"/>
            </template>
        </el-table-column>
        <el-table-column prop="packing" width="50" align="center">
            <template #header>
                <el-tooltip content="Упаковка товара">Уп </el-tooltip>
                <el-checkbox v-model="checkedPacking" :checked="checkedPacking" @change="checkPacking" />

            </template>
            <template #default="scope">
                <el-checkbox v-if="is_new"
                             :checked="scope.row.packing"
                             @change="val => setPacking(val, scope.row)"
                             :disabled="iSaving"
                />
                <Active v-else :active="scope.row.packing"/>
            </template>
        </el-table-column>

        <el-table-column prop="comment" label="Комментарий" :width="is_new ? 200 : 120" show-overflow-tooltip>
            <template #default="scope">
                <el-input v-if="is_new"
                          v-model="scope.row.comment"
                          @change="setProduct(scope.row)"
                          :disabled="iSaving"
                />
                <span v-else>{{ scope.row.comment }}</span>
            </template>
        </el-table-column>
        <el-table-column label="Действия" width="" align="right">
            <template #default="scope">
                <div v-if="is_new">
                    <el-button type="danger" @click="handleDeleteItem(scope.row)">
                        <i class="fa-light fa-trash"></i>
                    </el-button>
                </div>
            </template>
        </el-table-column>
    </el-table>
    <DeleteEntityModal name_entity="Товар из заказа" />

</template>

<script setup lang="ts">
import {computed, inject, ref} from "vue"
import {func} from "@Res/func.js"
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
})
//console.log(props.items)
const $delete_entity = inject("$delete_entity")
const iSaving = ref(false)
const {is_new, is_issued, is_view} = inject('$status')
const tableData = ref([...props.items])

function setAssemblage(val, row) {
    row.assemblage = val
    setProduct(row)
}
function setPacking(val, row) {
    row.packing = val
    setProduct(row)
}

function setProduct(row) {
   // console.log('row', row)
    iSaving.value = true;
    router.visit(route('admin.order.set-item', {item: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            sell_cost: row.sell_cost,
            percent: row.percent,
            comment: row.comment,
            assemblage: row.assemblage,
            packing: row.packing,
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

function handleDeleteItem(row) {
    $delete_entity.show(route('admin.order.del-item', {item: row.id}));
}
function isProm(row) {
    return row.product.has_promotion && !row.preorder
}

const checkedAssemblage = computed( () => {
    let check = true
    props.items.forEach(function (item) {
        if (item.assemblage !== true) check = false
    })
    return check
})
const checkedPacking = computed( () => {
    let check = true
    props.items.forEach(function (item) {
        if (item.packing !== true) check = false
    })
    return check
})
function checkAssemblage() {
   // console.log([...props.items.map(i => i.id)])
    iSaving.value = true;
    router.visit(route('admin.order.set-assemblage'), {
        method: "post",
        data: {
            assemblage: !checkedAssemblage.value,
            items: [...props.items.map(i => i.id)]
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function checkPacking() {
   // console.log([...props.items.map(i => i.id)])
    iSaving.value = true;
    router.visit(route('admin.order.set-packing'), {
        method: "post",
        data: {
            packing: !checkedPacking.value,
            items: [...props.items.map(i => i.id)]
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
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
