<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Расходная накладная {{ departure.number }} <span
            v-if="departure.incoming_number">({{ departure.incoming_number }})</span> от
            {{ func.date(departure.created_at) }}
            <el-tag v-if="departure.trashed" type="danger">Удален</el-tag>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <DepartureInfo :departure="departure" :storages="storages" :customers="customers"/>
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <DepartureActions :departure="departure"/>
            </div>
        </el-affix>
        <el-table :data="[...departure.products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="classes.TableCostCurrency"
                  style="width: 100%;">
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160"/>
            <el-table-column prop="product.name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="cost" label="Цена" width="180">
                <template #default="scope">{{ func.price(scope.row.cost) }}</template>
            </el-table-column>
            <el-table-column prop="quantity" label="Кол-во" width="180">
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
            <el-table-column prop="quantity" label="Сумма в рублях" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.quantity * scope.row.cost) }}
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain>
                        <el-icon>
                            <Delete/>
                        </el-icon>
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="departure.products.current_page"
            :per_page="departure.products.per_page"
            :total="departure.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из списания"/>
</template>

<script lang="ts" setup>
import {inject, ref, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import DepartureInfo from './Blocks/Info.vue'
import DepartureActions from './Blocks/Actions.vue'
import {classes} from "@Res/className"

const props = defineProps({
    departure: Object,
    title: {
        type: String,
        default: 'Списание товаров',
    },
    storages: Array,
    printed: Object,
    filters: Array,
    customers: Array,
})
provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.departure) //Для общих действий

const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.departure.completed && !props.departure.trashed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.departure.set-product', {product: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            cost: row.cost_currency
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.departure.del-product', {product: row.id}));
}
const indexMethod = (index: number) => {
    return index + (props.departure.products.current_page - 1) * props.departure.products.per_page + 1
}
</script>
