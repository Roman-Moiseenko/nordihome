<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Оприходование {{ surplus.number }} <span
            v-if="surplus.incoming_number">({{ surplus.incoming_number }})</span> от
            {{ func.date(surplus.created_at) }}
            <el-tag v-if="surplus.trashed" type="danger">Удален</el-tag>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <SurplusInfo :surplus="surplus" :storages="storages" :customers="customers"/>
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <SurplusActions :surplus="surplus"/>
            </div>
        </el-affix>
        <el-table :data="[...surplus.products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="classes.TableCostCurrency"
                  style="width: 100%;">
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160"/>
            <el-table-column prop="product.name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="cost" label="Цена" width="180">
              <template #default="scope">
                <el-input v-model="scope.row.cost"
                          :formatter="(value) => func.MaskInteger(value)"
                          @change="setItem(scope.row)"
                          :disabled="iSaving"
                          :readonly="!isEdit"
                >
                  <template #append>₽</template>
                </el-input>
              </template>
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
            :current_page="surplus.products.current_page"
            :per_page="surplus.products.per_page"
            :total="surplus.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из оприходования"/>
</template>

<script lang="ts" setup>
import {inject, ref, defineProps, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import SurplusInfo from './Blocks/Info.vue'
import SurplusActions from './Blocks/Actions.vue'
import {classes} from "@Res/className"

const props = defineProps({
    surplus: Object,
    title: {
        type: String,
        default: 'Оприходование товаров',
    },
    storages: Array,
    printed: Object,
    filters: Array,
    customers: Array,
})
provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.surplus) //Для общих действий

const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.surplus.completed && !props.surplus.trashed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.surplus.set-product', {product: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            cost: row.cost
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.surplus.del-product', {product: row.id}));
}
const indexMethod = (index: number) => {
    return index + (props.surplus.products.current_page - 1) * props.surplus.products.per_page + 1
}
</script>
