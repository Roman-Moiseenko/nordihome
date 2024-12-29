<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <SearchAddUser :user_id="order.user_id" :route="route('admin.order.set-user', {order: order.id})" />
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
        <el-col :span="8">
            <div class="inline-grid ">

                <el-form-item v-if="is_new" label="Резерв">
                    <el-date-picker v-model="reserve" type="datetime" @change="handleReserve" :disabled="iSavingInfo"/>
                </el-form-item>

                <el-button type="success">На оплату</el-button>
                <el-button type="success" plain>Скачать счет</el-button>
                <el-button type="info" plain>Отменить</el-button>
            </div>
        </el-col>

    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, inject, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import SearchAddUser from "@Comp/Search/AddUser.vue"
import AccountingDocument from "@Comp/Accounting/Document.vue";

const props = defineProps({
    order: Object,
    storages: Array,
    mainStorage: Object,
})
const iSavingInfo = ref(false)
const reserve = ref(props.order.reserve)
const notEdit = computed(() => props.arrival.completed);
const {is_new, is_issued, is_view} = inject('$status')
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

function handleReserve() {
    iSavingInfo.value = true
    router.visit(route('admin.order.set-reserve', {order: props.order.id}), {
        method: "post",
        data: {
            reserve_at: func.datetime(reserve.value),
        },
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

.inline-grid {
    .el-button + .el-button {
        margin-top: 0.5rem;
        margin-left: 0;
    }
}
</style>
