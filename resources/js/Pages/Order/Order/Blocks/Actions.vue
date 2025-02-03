<template>

    <template v-if="is_new">
        <SearchAddProduct
            :route="route('admin.order.add-product', {order: order.id})"
            :quantity="true"
            :preorder="true"
            :create="true"
        />
        <SearchAddProducts :route="route('admin.order.add-products', {order: order.id})" class="ml-3"/>
        <SelectAddition :additions="additions" :order="order"/>
        <div class="flex ml-1">
            <span class="ml-2 my-auto text-red-800">Скидка: </span>

            <el-input v-model="form.manual"
                      clearable
                      :formatter="val => func.MaskCount(val, 0)"
                      @change="setDiscount('manual')"
                      :disabled="iSaving"
                      style="width: 110px">
                <template #append>₽</template>
            </el-input>
            <el-input v-model="form.percent"
                      :formatter="val => func.MaskFloat(val)"
                      clearable
                      class="ml-1" style="width: 90px"
                      @change="setDiscount('percent')"
                      :disabled="iSaving"
            >
                <template #append>%</template>
            </el-input>
            <el-input v-model="form.coupon" clearable class="ml-2" style="width: 80px" placeholder="Купон"
                      @change="setDiscount('coupon')"
                      :disabled="iSaving"/>

        </div>
    </template>

    <el-dropdown v-if="is_awaiting || is_issued">
        <el-button type="primary mr-2">
            Создать на основании
            <el-icon class="el-icon--right">
                <arrow-down/>
            </el-icon>
        </el-button>
        <template #dropdown>
            <el-dropdown-menu>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onPayment('cash')">Оплата в кассу
                </el-dropdown-item>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onPayment('card')">Оплата по карте
                </el-dropdown-item>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onPayment('account')">Оплата по счету
                </el-dropdown-item>

                <el-dropdown-item v-if="!order.status.is_paid && order.shopper_id" @click="onFindPayment">
                    <span class="text-orange-600"> Найти оплату </span>
                </el-dropdown-item>

                <el-dropdown-item v-if="is_issued" @click="dialogIssued = true">
                    Распоряжение на отгрузку
                </el-dropdown-item>

            </el-dropdown-menu>
        </template>
    </el-dropdown>
    <template v-if="!is_new && order.payments.length > 0">
        <el-dropdown>
            <el-button type="success" class="mr-2">
                Платежи
                <el-icon class="el-icon--right">
                    <arrow-down/>
                </el-icon>
            </el-button>
            <template #dropdown>
                <div v-for="item in order.payments" class="p-2">
                    <Link type="primary" :href="route('admin.order.payment.show', {payment: item.id})">Платеж на сумму
                        {{ func.price(item.amount) }} [{{ item.method_text }}]
                    </Link>
                </div>
            </template>
        </el-dropdown>
    </template>
    <template v-if="!is_new && !is_awaiting">

        <el-dropdown v-if="order.movements.length > 0">
            <el-button type="warning" class="mr-2">
                Перемещения
                <el-icon class="el-icon--right">
                    <arrow-down/>
                </el-icon>
            </el-button>
            <template #dropdown>
                <div v-for="item in order.movements" class="p-2">
                    <Link type="warning"
                          :href="route('admin.accounting.movement.show', {movement: item.id})">Перемещение
                        №{{ item.number }} [{{ item.status_text }}]
                    </Link>
                </div>
            </template>
        </el-dropdown>
        <el-dropdown v-if="order.expenses.length > 0">
            <el-button type="success" plain class="mr-2">
                Распоряжения на выдачу
                <el-icon class="el-icon--right">
                    <arrow-down/>
                </el-icon>
            </el-button>
            <template #dropdown>
                <div v-for="item in order.expenses" class="p-2">
                    <Link :type="typeExpense(item)"
                          :href="route('admin.order.expense.show', {expense: item.id})">Распоряжение
                        №{{ item.number }} от {{ func.date(item.created_at) }} [{{ item.status_text }}]
                    </Link>
                </div>
            </template>
        </el-dropdown>
    </template>
    <template v-if="is_view">
        <el-button type="warning" class="ml-5" @click="onCopy">Скопировать</el-button>
    </template>

    <template v-if="order.status.is_prepaid">

    </template>
    <template v-if="order.status.is_paid">

    </template>
    <template v-if="order.status.is_completed">

    </template>
    <template v-if="order.status.is_canceled">

    </template>

    <el-dialog v-model="dialogFindPayment" title="Выбрать платеж" width="400">
        <el-select v-model="payment_id">
            <el-option v-for="item in payments" :value="item.id" :label="item.purpose">
                {{ item.purpose }}
                <el-tag type="success" effect="plain">{{ func.price(item.amount) }}</el-tag>
            </el-option>
        </el-select>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogFindPayment = false">Отмена</el-button>
                <el-button type="primary" @click="setPayment">Выбрать</el-button>
            </div>
        </template>
    </el-dialog>

    <el-dialog v-model="dialogIssued" title="Распоряжение на выдачу товара">

        <el-radio-group v-model="issued.method">
            <el-radio-button value="shop" size="large" @click="onStorage(true)">Выдать с магазина</el-radio-button>
            <el-radio-button value="warehouse" size="large" @click="onStorage(true)">Выдать со склада</el-radio-button>
            <el-radio-button value="expense" size="large" @click="onStorage(false)">На доставку</el-radio-button>
        </el-radio-group>

        <el-form-item label="Склад/Магазин выдачи">
            <el-select v-model="issued.storage_id" style="width: 200px;" class="mt-2 mb-2" :disabled="disabled_storage">
                <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"/>
            </el-select>
        </el-form-item>

        <el-table
            :data="issued.items"
            header-cell-class-name="nordihome-header"
            style="width: 100%;"
        >
            <el-table-column type="index" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="110"/>
            <el-table-column prop="product.name" label="Товар" width="240" show-overflow-tooltip/>
            <el-table-column label="Продажа" width="230" align="center">
                <template #default="scope">
                    {{ func.price(scope.row.sell_cost) }}
                </template>
            </el-table-column>

            <el-table-column label="Не выдано" width="240">
                <template #header>
                    Не выдано <el-checkbox v-model="checkedItems" :checked="checkedItems" @change="checkItems" />
                </template>
                <template #default="scope">
                    <div class="flex">
                        <el-input v-model="scope.row.remains" :disabled="!scope.row.issued" style="width: 60px;"/>
                        <el-checkbox v-model="scope.row.issued" :checked="scope.row.issued" class="ml-2"
                                     label="Выдать"/>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <el-table
            :data="issued.additions"
            header-cell-class-name="nordihome-header"
            style="width: 100%;"
        >
            <el-table-column type="index" label="п/п"/>
            <el-table-column prop="name" label="Услуга" width="240" show-overflow-tooltip/>
            <el-table-column label="Продажа" width="230" align="center">
                <template #default="scope">
                    {{ func.price(scope.row.amount) }}
                </template>
            </el-table-column>

            <el-table-column label="Не выдано" width="240">
                <template #header>
                    Не выдано <el-checkbox v-model="checkedAdditions" :checked="checkedAdditions" @change="checkAdditions" />
                </template>
                <template #default="scope">
                    <div class="flex">
                        <el-input v-model="scope.row.remains" :disabled="!scope.row.issued" style="width: 80px;"/>
                        <el-checkbox v-model="scope.row.issued" :checked="scope.row.issued" class="ml-2"
                                     label="Выдать"/>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogIssued = false">Отмена</el-button>
                <el-button type="primary" @click="setIssued">Выдать</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps, inject, reactive, ref, onMounted, computed} from "vue";
import {Link, router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import {ElLoading, ElMessage} from "element-plus";
import SelectAddition from "@Page/Order/Order/Blocks/SelectAddition.vue";
import axios from "axios";

const props = defineProps({
    order: Object,
    additions: Array,
    storages: Array,
})


const iSaving = ref(false)
const form = reactive({
    coupon: null,
    manual: props.order.amount.manual,
    percent: props.order.amount.percent,
    action: null,
})
const {is_new, is_awaiting, is_issued, is_view} = inject('$status')

function setDiscount(action) {
    form.action = action
    iSaving.value = true
    router.visit(route('admin.order.set-discount', {order: props.order.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

function onPayment(val) {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.order.payment.create', {order: props.order.id}), {
        method: "post",
        data: {method: val},
        onSuccess: page => {
            loading.close()
        }
    })
}

function onCopy() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Идет копирование заказа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.order.copy', {order: props.order.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}

//Найти платежку
const dialogFindPayment = ref(false)
const payment_id = ref(null)
const payments = ref([])

function onFindPayment() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Ищем платежи',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    axios.post(route('admin.order.payment.find'),
        {
            shopper_id: props.order.shopper_id,
            trader_id: props.order.trader_id,
        },
    ).then(result => {
        loading.close()
        console.log(result)
        if (result.data.length === 0) {
            ElMessage({
                message: 'Платежи не найдены',
                type: 'error',
                plain: true,
                showClose: true,
                duration: 5000,
                center: true,
            });
        } else {
            dialogFindPayment.value = true
            payments.value = [...result.data]
        }
    })
}

function setPayment() {
    router.visit(route('admin.order.payment.set-order', {order: props.order.id, payment: payment_id.value}), {
        method: "post",
        onSuccess: page => {
            dialogFindPayment.value = false;
        }
    })
}

//Выдача товара
const dialogIssued = ref(false)
const issued = reactive({
    method: 'expense',
    storage_id: null,
    items: [...props.order.items.map(item => {
        if (item.preorder || item.remains === null || item.remains === 0) return null
        item.issued = true
        return item
    }).filter(item => {
        return item !== null
    })],
    additions: [...props.order.additions.map(addition => {
        if (addition.remains === null || addition.remains === 0) return null
        addition.issued = true
        return addition
    }).filter(addition => {
        return addition !== null
    })],
})
const disabled_storage = ref(true)
const checkedItems = computed( () => {
    let check = true
    issued.items.forEach(function (item) {
        if (item.issued !== true) check = false
    })
    return check
})
const checkedAdditions = computed( () => {
    let check = true
    issued.additions.forEach(function (item) {
        if (item.issued !== true) check = false
    })
    return check
})
function checkItems() {
    let val = !checkedItems.value
    issued.items.forEach(function (item) {
        item.issued = val
    })

}
function checkAdditions() {
    let val = !checkedAdditions.value
    issued.additions.forEach(function (item) {
        item.issued = val
    })

}
function typeExpense(item) {
    if (item.is_canceled) return 'info'
    if (item.is_completed) return 'success'
    return 'warning'
}

function onStorage(val) {
    if (val) {
        disabled_storage.value = false
    } else {
        disabled_storage.value = true
        issued.storage_id = null
    }
}

function setIssued() {
    const form = reactive({
        method: issued.method,
        storage_id: issued.storage_id,
        items: issued.items.filter(item => {
            return item.issued !== false
        }).map(item => {
            return {
                id: item.id,
                value: item.remains
            }
        }),
        additions: issued.additions.filter(addition => {
            return addition.issued !== false
        }).map(addition => {
            return {
                id: addition.id,
                value: addition.amount
            }
        }),


    })

    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })

    router.visit(route('admin.order.expense.create', {order: props.order.id}), {
        method: "post",
        data: form,
        onSuccess: page => {
            loading.close()
            dialogIssued.value = false
        }
    })
}
</script>
