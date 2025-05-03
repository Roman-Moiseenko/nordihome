<template>
    <el-row :gutter="10">
        <!-- Данные клиента -->
        <el-col :span="8">
            <div v-if="order.user_id">
                <el-descriptions :column="1" border class="mb-1" size="small">
                    <el-descriptions-item v-if="order.user.organization" label="Юридическое лицо">
                        <el-select v-model="info.shopper_id" @change="setInfo" :disabled="iSavingInfo || !is_new"
                                   filterable style="max-width: 280px;" clearable>
                            <el-option v-for="item in order.shoppers" :key="item.id" :value="item.id"
                                       :label="item.short_name + ' (' + item.inn +')'"/>
                        </el-select>
                    </el-descriptions-item>
                    <el-descriptions-item label="ФИО">
                        {{ func.fullName(order.user.fullname) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Телефон">
                        {{ func.phone(order.user.phone) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Email">
                        {{ order.user.email }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Доставка">
                        {{ order.user.delivery_name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Адрес">
                        {{ order.user.address.post }} {{ order.user.address.region }} {{ order.user.address.address }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Цена">
                        {{ order.user.pricing }}
                    </el-descriptions-item>

                </el-descriptions>
                <Link type="warning" :href="route('admin.user.show', {user: order.user.id})">Карточка клиента</Link>
            </div>
            <SearchUser v-else :route="route('admin.order.set-user', {order: order.id})"/>
        </el-col>
        <!-- Суммы по заказу -->
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
                <el-descriptions-item label="Итого"
                                      label-class-name="bg-amount"
                                      class-name="bg-amount">
                    {{ func.price(order.amount.total) }}
                </el-descriptions-item>
                <el-descriptions-item v-if="order.amount.payment" label="Оплачено"
                                      label-class-name="bg-amount"
                                      class-name="bg-amount">
                    {{ func.price(order.amount.payment) }}
                </el-descriptions-item>
                <el-descriptions-item v-if="order.amount.refund !== 0" label="Возврат"
                                      label-class-name="bg-discount"
                                      class-name="bg-discount">
                    {{ func.price(order.amount.refund) }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <!-- Действия (кнопки) с заказом -->
        <el-col :span="8">
            <div class="inline-grid ">

                <el-form-item v-if="is_new || is_awaiting" label="Резерв">
                    <el-date-picker v-model="reserve" type="datetime" @change="handleReserve" :disabled="iSavingInfo"/>
                </el-form-item>

                <el-popover v-if="is_issued" :visible="visible_movement" placement="bottom-start" :width="246">
                    <template #reference>
                        <el-button type="primary" class="p-4 my-3" @click="visible_movement = !visible_movement"
                                   ref="buttonRef">
                            Перемещение
                            <el-icon class="ml-1">
                                <ArrowDown/>
                            </el-icon>
                        </el-button>
                    </template>
                    <el-select v-model="movement.storage_out" placeholder="Склад Убытия" class="mt-1">
                        <el-option v-for="item in storages" :key="item.id" :label="item.name" :value="item.id"/>
                    </el-select>
                    <el-select v-model="movement.storage_in" placeholder="Склад Назначения" class="mt-1">
                        <el-option v-for="item in storages" :key="item.id" :label="item.name" :value="item.id"/>
                    </el-select>
                    <div class="mt-2">
                        <el-button @click="visible_movement = false">Отмена</el-button>
                        <el-button @click="onMovement" type="primary">Создать</el-button>
                    </div>
                </el-popover>

                <el-button v-if="is_new" type="success" @click="dialogAwaiting = true">На оплату</el-button>

                <el-button v-if="!is_view" type="success" plain @click="getInvoice">Скачать счет</el-button>
                <el-button v-if="is_awaiting" type="warning" plain @click="onWork">Вернуть в работу</el-button>
                <el-button v-if="!is_view" type="info" plain @click="dialogCancel = true">Отменить</el-button>
            </div>
            <div class="border-t mt-2 pt-2">
                <el-form-item label="Продавец" size="small">
                    <el-select v-model="info.trader_id" @change="setInfo" :disabled="iSavingInfo || !is_new" filterable
                               style="max-width: 280px;">
                        <el-option v-for="item in traders" :key="item.id" :value="item.id"
                                   :label="item.short_name + ' (' + item.inn +')'"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Комментарий" size="small">
                    <el-input v-model="info.comment" @change="setInfo" :disabled="iSavingInfo || !is_new"/>
                </el-form-item>
            </div>
        </el-col>

    </el-row>

    <el-dialog v-model="dialogAwaiting" title="Отправить заказ на оплату?" width="360">
        <div v-if="order.shopper_id">
            <h3>Если email отличается от учетной записи, выберите и/или введите новые адреса почты</h3>
            <el-select v-model="formEmails" multiple allow-create filterable class="mt-2">
                <el-option v-for="item in order.emails" :key="item.label" :value="item.label" :label="item.value">
                    {{ item.value }} <{{ item.label }}>
                </el-option>
            </el-select>
        </div>

        <div>
            <el-checkbox v-model="formPayment.account" :checked="formPayment.account">Отправить счет</el-checkbox>
        </div>
        <div>
            <el-checkbox v-model="formPayment.qr" :checked="formPayment.qr">Отправить QR-код</el-checkbox>
        </div>
        <template #footer>
            <div class="dialog-footer mt-3">
                <el-button @click="dialogAwaiting = false">Отмена</el-button>
                <el-button type="success" @click="onAwaiting()">
                    Отправить
                </el-button>
            </div>
        </template>

    </el-dialog>
    <el-dialog v-model="dialogCancel" title="Отменить заказ" center width="400">
        <div class="font-medium text-md my-2">
            Вы уверены, что хотите отменить заказ?
        </div>
        <el-form-item label="Причина">
            <el-input v-model="cancel_comment"/>
        </el-form-item>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogCancel = false">Отмена</el-button>
                <el-button type="warning" @click="onCancel()">
                    Отменить
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, inject, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import SearchEditUser from "@Comp/User/SearchEdit.vue"
import SearchUser from "@Comp/User/Search.vue";
import {ElLoading} from "element-plus";
import axios from "axios";

const props = defineProps({
    order: Object,
    storages: Array,
    mainStorage: Object,
    traders: Array,
})

const iSavingInfo = ref(false)
const info = reactive({
    trader_id: props.order.trader_id,
    shopper_id: props.order.shopper_id,
    comment: props.order.comment,
})
const reserve = ref(props.order.reserve)

const dialogCancel = ref(false)
const cancel_comment = ref(null)
const {is_new, is_awaiting, is_issued, is_view} = inject('$status')

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.order.set-info', {order: props.order.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: false,
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

function getInvoice() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Идет формирование счета',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    axios.post(route('admin.order.invoice', {order: props.order.id}), null,
        {
            responseType: 'arraybuffer',
        }
    ).then(response => {
        let blob = new Blob([response.data], {type: 'application/*'})
        let link = document.createElement('a')
        let headers = response.headers

        link.href = window.URL.createObjectURL(blob)
        link.download = headers['filename']
        link._target = 'blank'
        document.body.appendChild(link);
        link.click();
        loading.close()
        URL.revokeObjectURL(link.href)
    }).catch(reason => {
        loading.close()
    })
}

//На оплату
const dialogAwaiting = ref(false)
const formEmails = ref([]);
const formPayment = reactive({
    account: true,
    qr: false,
});

function onAwaiting() {
    router.visit(route('admin.order.awaiting', {order: props.order.id}), {
        method: "post",
        data: {emails: formEmails.value, payment: formPayment},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogAwaiting.value = false;
        }
    })
}

function onWork() {
    router.post(route('admin.order.work', {order: props.order.id}))
}

function onCancel() {
    router.visit(route('admin.order.cancel', {order: props.order.id}), {
        method: "post",
        data: {comment: cancel_comment.value},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogCancel.value = false;
        }
    })
}

//Перемещение
const visible_movement = ref(false)
const movement = reactive({
    storage_out: null,
    storage_in: null
})

function onMovement() {
    router.post(route('admin.order.movement', {order: props.order.id}), movement)
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
