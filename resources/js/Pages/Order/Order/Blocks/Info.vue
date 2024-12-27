<template>
    <el-row :gutter="10">
        <el-col :span="12">
            Редактируемые Поля - Клиент, смена Менеджера
        </el-col>
        <el-col :span="12">
            Итоговые значения заказа, скидки, к оплате
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Accounting/Document.vue";

const props = defineProps({
    order: Object,
    storages: Array,
    mainStorage: Object,
})
const iSavingInfo = ref(false)
const info = reactive({
  /*  document: {
        number: props.arrival.number,
        created_at: props.arrival.created_at,
        incoming_number: props.arrival.incoming_number,
        incoming_at: props.arrival.incoming_at,
        comment: props.arrival.comment,
    },
    storage_id: props.arrival.storage_id,
    exchange_fix: props.arrival.exchange_fix,
    operation: props.arrival.operation,
*/
})
const notEdit = computed(() => props.arrival.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.order.set-info', {order: props.order.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
