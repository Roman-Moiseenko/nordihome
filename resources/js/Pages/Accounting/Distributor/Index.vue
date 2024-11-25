<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Поставщики</h1>
        <div class="flex my-3">
            <el-popover :visible="visible_create" placement="bottom-start" :width="400">
                <template #reference>
                    <el-button type="primary" class="p-4" @click="visible_create = !visible_create" ref="buttonRef">
                        Добавить поставщика
                        <el-icon class="ml-1">
                            <ArrowDown/>
                        </el-icon>
                    </el-button>
                </template>
                <el-form label-width="auto">
                    <el-form-item label="Название в CRM">
                        <el-input v-model="create.name" placeholder="Название в CRM" class="mt-1"/>
                    </el-form-item>
                    <el-form-item label="Валюта">
                        <el-select v-model="create.currency" placeholder="Валюта" class="mt-1">
                            <el-option v-for="item in currencies" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                    </el-form-item>
                    <!--
                    <el-form-item label="ИНН">
                        <el-input v-model="create.inn" placeholder="ИНН" class="mt-1"/>
                    </el-form-item>
                    <el-form-item label="БИК">
                        <el-input v-model="create.bik" placeholder="БИК" class="mt-1"/>
                    </el-form-item>
                    <el-form-item label="Расчетный счет">
                        <el-input v-model="create.account" placeholder="Расчетный счет" class="mt-1"/>
                    </el-form-item>
                    -->
                </el-form>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button>
                    <el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <!--el-button type="primary" plain >Холдинги</el-button-->
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Организация, ИНН, email, тел" class="mt-1"/>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="name" label="Название"/>
                <el-table-column prop="name" label="Иностранный" width="160">
                    <template #default="scope">
                        <Active :active="scope.row.foreign"/>
                    </template>
                </el-table-column>

                <el-table-column label="Организация">
                    <template #default="scope">
                        <span v-if="scope.row.organization">{{ scope.row.organization.short_name }}</span>
                    </template>
                </el-table-column>

                <el-table-column prop="debit" label="Долг" width="160">
                    <template #default="scope">
                        {{ func.price(scope.row.debit, scope.row.currency.sign) }}
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <!--el-button v-if="!scope.row.completed"
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button-->
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="distributors.current_page"
            :per_page="distributors.per_page"
            :total="distributors.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Контрагента"/>
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    distributors: Object,
    title: {
        type: String,
        default: 'Список поставщиков',
    },
    filters: Array,
    currencies: Object,
})

const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.distributors.data])
const filter = reactive({
    name: props.filters.name,


})
const create = reactive({
    name: null,
    inn: null,
    bik: null,
    account: null,
    currency: null,
})

function createButton() {
    router.post(route('admin.accounting.distributor.store', create))
}

function routeClick(row) {
    router.get(route('admin.accounting.distributor.show', {distributor: row.id}))
}
</script>
