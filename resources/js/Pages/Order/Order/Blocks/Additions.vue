<template>
    <el-table
        :data="[...additions]"
        header-cell-class-name="nordihome-header"
        style="width: 100%;"
    >
        <el-table-column type="index" label="п/п"/>
        <el-table-column prop="name" label="Услуга"/>
        <el-table-column prop="base" label="Базовый коэф." width="160px" align="center"/>
        <el-table-column prop="manual" label="Авто" width="80px" align="center">
            <template #default="scope">
                <Active :active="!scope.row.manual"/>
            </template>
        </el-table-column>
        <el-table-column prop="quantity" label="Кол-во" width="80px" align="center">
            <template #default="scope">
                <div v-if="scope.row.is_quantity">
                    <el-input v-if="is_new" v-model="scope.row.quantity" :formatter="val => func.MaskCount(val, 1)"
                              @change="setAddition(scope.row)"
                              :disabled="iSaving"
                    />
                    <el-tag v-if="is_issued || is_view" type="primary" effect="dark">{{ scope.row.quantity }}</el-tag>
                </div>
                <div v-else>
                    -
                </div>
            </template>
        </el-table-column>
        <el-table-column prop="" label="Стоимость" width="160">
            <template #default="scope">
                <div v-if="scope.row.is_quantity">
                    <el-tag v-if="!scope.row.manual" type="success" effect="dark">{{ func.price(scope.row.calculate) }}
                    </el-tag>
                    <el-tag v-if="scope.row.manual && !is_new" type="success" effect="dark">
                        {{ func.price(scope.row.calculate) }}
                    </el-tag>
                </div>
                <el-input v-if="scope.row.manual && is_new" v-model="scope.row.amount"
                          @change="setAddition(scope.row)"
                          :disabled="iSaving"
                >
                    <template #append>₽</template>
                </el-input>

            </template>
        </el-table-column>
        <el-table-column prop="" label="Сумма">
            <template #default="scope">
                <el-tag v-if="!scope.row.manual" type="success" effect="dark">
                    {{ func.price(scope.row.calculate * scope.row.quantity) }}
                </el-tag>
                <el-tag v-if="scope.row.manual" type="success" effect="dark">
                    {{ func.price(scope.row.amount * scope.row.quantity) }}
                </el-tag>
            </template>
        </el-table-column>
        <el-table-column prop="comment" label="Комментарий" :width="is_new ? 260 : 120" show-overflow-tooltip>
            <template #default="scope">
                <el-input v-if="is_new"
                          v-model="scope.row.comment"
                          @change="setAddition(scope.row)"
                          :disabled="iSaving"
                />
                <span v-else>{{ scope.row.comment }}</span>
            </template>
        </el-table-column>
        <el-table-column label="Действия" width="" align="right">
            <template #default="scope">
                <div v-if="is_new">
                    <el-button type="danger" @click="handleDeleteEntity(scope.row)">
                        <i class="fa-light fa-trash"></i>
                    </el-button>
                </div>
                <div v-if="is_issued">
                    Выдать
                </div>
            </template>
        </el-table-column>
    </el-table>
    <DeleteEntityModal name_entity="Услугу из заказа" name="addition"/>
</template>

<script setup lang="ts">
import Active from "@Comp/Elements/Active.vue";
import {func} from "@Res/func.js"
import {computed, inject, ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    additions: Array,
})
const $delete_entity = inject("$delete_entity")
const iSaving = ref(false)
const {is_new, is_issued, is_view} = inject('$status')

function setAddition(row) {
    iSaving.value = true;
    router.visit(route('admin.order.set-addition', {addition: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            amount: row.amount,
            comment: row.comment,
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.order.del-addition', {addition: row.id}), 'addition');
}
</script>

<style scoped>

</style>
