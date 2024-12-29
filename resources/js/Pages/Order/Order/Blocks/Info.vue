<template>
    <el-row :gutter="10">
        <el-col :span="8">
            Редактируемые Поля - Клиент, смена Менеджера
        </el-col>
        <el-col :span="8">
            Редактируемые Поля - Клиент, смена Менеджера
        </el-col>
        <el-col :span="8">

            <el-descriptions column="2" border>
                <el-descriptions-item
                    label="Сумма базовая"
                    label-width="160"
                    label-class-name="bg-sell"
                    class-name="bg-sell"
                >
                    {{ func.price(order.amount.base) }}
                </el-descriptions-item>
                <el-descriptions-item label="Услуги" label-width="160"
                                      label-class-name="bg-sell" class-name="bg-sell">
                    {{ func.price(order.amount.addition) }}
                </el-descriptions-item>
                <el-descriptions-item
                    label="Скидка за товары"
                    label-class-name="bg-discount"
                    class-name="bg-discount"
                >
                    {{ func.price(order.amount.manual) }}
                </el-descriptions-item>
                <el-descriptions-item label="Бонус по акции"
                                      label-class-name="bg-discount"
                                      class-name="bg-discount">
                    {{ func.price(order.amount.promotions) }}
                </el-descriptions-item>

                <el-descriptions-item label="Скидка по купону"
                                      label-class-name="bg-discount"
                                      class-name="bg-discount">
                    {{ func.price(order.amount.coupon) }}
                </el-descriptions-item>
                <el-descriptions-item label="Скидка на заказ"
                                      label-class-name="bg-discount"
                                      class-name="bg-discount">
                    {{ func.price(order.amount.discount) }}
                </el-descriptions-item>
                <el-descriptions-item label="Итого к оплате"
                label-class-name="bg-amount"
                class-name="bg-amount">
                    {{ func.price(order.amount.total) }}
                </el-descriptions-item>
            </el-descriptions>
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
<style lang="scss" scoped>
:deep(.bg-sell) {
    background: #c5dcba !important;
}
:deep(.bg-discount) {
    background: #e7cfcf !important;
}
:deep(.bg-amount) {
    background: #a0d786 !important;
    font-size: 1.125rem !important;
    line-height: 1.75rem !important;
    font-weight: 500 !important;
}
</style>
