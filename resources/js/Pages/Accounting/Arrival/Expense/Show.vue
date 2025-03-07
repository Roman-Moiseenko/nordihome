<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Дополнительные расходы {{ expense.number }}
            <span v-if="expense.incoming_number">({{ expense.incoming_number }})</span>
            от {{ func.date(expense.created_at) }}
            <el-tag v-if="expense.trashed" type="danger">Удален</el-tag>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <ExpenseInfo :expense="expense" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <ExpenseActions :expense="expense" />
            </div>
        </el-affix>
        <el-table :data="[...expense.items]"
                  header-cell-class-name="nordihome-header"
                  style="width: 100%;">
            <el-table-column prop="name" label="Номенклатура" show-overflow-tooltip>
                <template #default="scope">
                    <el-input v-model="scope.row.name"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit"
                    />
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
                        <template #append>ед.</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column prop="cost" label="Цена" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.cost"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit"
                    >
                        <template #append>{{ expense.currency_sign }}</template>
                    </el-input>
                </template>
            </el-table-column>

            <el-table-column label="Сумма" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.quantity * scope.row.cost, expense.currency_sign) }}
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
    </el-config-provider>
    <DeleteEntityModal name_entity="Расход из документа" />
</template>

<script lang="ts" setup>
import {inject, ref, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import ExpenseInfo from './Blocks/Info.vue'
import ExpenseActions from './Blocks/Actions.vue'

const props = defineProps({
    expense: Object,
    title: {
        type: String,
        default: 'Дополнительные расходы',
    },
    printed: Object,
})
provide('$printed', props.printed) //Для печати
provide('$accounting', props.expense) //Для общих действий
const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.expense.completed && !props.expense.trashed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.arrival.expense.set-item', {item: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            cost: row.cost,
            name: row.name,
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.arrival.expense.del-item', {item: row.id}));
}
</script>
