<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Установка цен {{ pricing.number }} <span v-if="pricing.incoming_number">({{ pricing.incoming_number }})</span> от {{ func.date(pricing.created_at) }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <PricingInfo :pricing="pricing" :storages="storages" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <PricingActions :pricing="pricing" />
            </div>
        </el-affix>
        <el-table :data="[...pricing.products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="tableRowClassName"
                  style="width: 100%;" :class="'pricing-table'">
            <el-table-column prop="code" label="Артикул" width="160" />
            <el-table-column prop="name" label="Товар" show-overflow-tooltip/>
            <!-- ЦЕНЫ -->
            <el-table-column label="Розничная (₽)" width="180" align="center">
                <template #default="scope">
                    <el-input v-model="scope.row.price_retail" class="text-center"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="changeItem(scope.row)"
                              :disabled="isBlocked(scope.row)"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.price_retail_old }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Оптовая (₽)" width="180" align="center">
                <template #default="scope">
                    <el-input v-model="scope.row.price_bulk" class="text-center"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="changeItem(scope.row)"
                              :disabled="isBlocked(scope.row)"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.price_bulk_old }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Специальная (₽)" width="180" align="center">
                <template #default="scope">
                    <el-input v-model="scope.row.price_special"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="changeItem(scope.row)"
                              :disabled="isBlocked(scope.row)"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.price_special_old }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Под заказ (₽)" width="180" align="center">
                <template #default="scope">
                    <el-input v-model="scope.row.price_pre" class="text-center"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="changeItem(scope.row)"
                              :disabled="isBlocked(scope.row)"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.price_pre_old }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Минимальная (₽)" width="180" align="center">
                <template #default="scope">
                    <el-input v-model="scope.row.price_min"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="changeItem(scope.row)"
                              :disabled="isBlocked(scope.row)"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.price_min_old }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Себестоимость (₽)" width="180" align="center">
                <template #default="scope">
                    <el-input v-model="scope.row.price_cost"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="changeItem(scope.row)"
                              :disabled="isBlocked(scope.row)"
                              :readonly="!isEdit"
                    >
                        <template #prepend>{{ scope.row.price_cost_old }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button v-if="scope.row.id === change_id" type="success" @click="setItem(scope.row)" plain><i class="fa-light fa-floppy-disk"></i></el-button>
                    <!--el-button v-if="scope.row.id === change_id" type="info" @click="clearItem" plain><i class="fa-light fa-xmark"></i></el-button-->
                    <el-button v-if="isEdit && !Blocked" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="pricing.products.current_page"
            :per_page="pricing.products.per_page"
            :total="pricing.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из поступления" />
</template>

<script lang="ts" setup>
import {inject, ref, defineProps, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import PricingInfo from './Blocks/Info.vue'
import PricingActions from './Blocks/Actions.vue'

const props = defineProps({
    pricing: Object,
    title: {
        type: String,
        default: 'Установка цен',
    },
    printed: Object,
})
provide('$printed', props.printed)
interface IRow {
    price_cost: number,
    price_retail: number,
    price_bulk: number,
    price_special: number,
    price_min: number,
    price_pre: number,
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.price_cost === 0 || row.price_retail === 0 ||
        row.price_bulk === 0 || row.price_special === 0 ||
        row.price_min === 0 || row.price_pre === 0
    ) {
        return 'error-row'
    }
    return ''
}
const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.pricing.completed);
const $delete_entity = inject("$delete_entity")
const change_id = ref(null)
const Blocked = ref(false)

function changeItem(row) {
    change_id.value = row.id
    Blocked.value = true
}
function isBlocked(row) {
    if (Blocked.value === false) return false;
    if (change_id.value === null) return false;
    if (Blocked.value === true && row.id === change_id.value) return false;
    return true;

}
function clearItem() {
    change_id.value = null
    Blocked.value = false
}
function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.pricing.set-product', {product: row.id}), {
        method: "post",
        data: {
            price_cost: row.price_cost,
            price_retail: row.price_retail,
            price_bulk: row.price_bulk,
            price_special: row.price_special,
            price_min: row.price_min,
            price_pre: row.price_pre,
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false
            clearItem()
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.pricing.del-product', {product: row.id}));
}
</script>
<style lang="scss">
.pricing-table {

    .el-input-group__prepend {
        width: 100%;
    }
}
</style>
