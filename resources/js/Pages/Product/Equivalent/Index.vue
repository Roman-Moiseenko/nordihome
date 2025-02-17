<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Группы аналогов</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать группу
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Название"/>
                <el-select v-model="form.category_id" placeholder="Категория" class="mt-1" filterable>
                    <el-option v-for="item in categories" :value="item.id" :label="item.name" />
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.product" placeholder="Товар" class="mt-1"/>
                <el-input v-model="filter.name" placeholder="Группа" class="mt-1"/>
                <el-select v-model="filter.category" placeholder="Категория" class="mt-1">
                    <el-option v-for="item in categories" :value="item.id" :label="item.name" :key="item.id" />
                </el-select>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="name" label="Название Группы">
                    <template #default="scope">
                        <EditField :field="scope.row.name" @update:field="val => onSetName(val, scope.row.id)"/>
                    </template>
                </el-table-column>
                <el-table-column prop="category" label="Категория"/>
                <el-table-column prop="quantity" label="Кол-во товаров" />
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.completed"
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <pagination
            :current_page="equivalents.current_page"
            :per_page="equivalents.per_page"
            :total="equivalents.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Бренд" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    equivalents: Object,
    title: {
        type: String,
        default: 'Группы аналогов',
    },
    filters: Array,
    categories: Array,
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.equivalents.data])
const filter = reactive({
    name: props.filters.name,
    product: props.filters.product,
    category: props.filters.category,
})

const form = reactive({
    name: null,
    category_id: null,
})
interface IRow {
    completed: number
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.completed === 0) {
        return 'warning-row'
    }
    return ''
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.equivalent.destroy', {equivalent: row.id}));
}
function createButton() {
    router.post(route('admin.product.equivalent.store', form))
}
function routeClick(row) {
    router.get(route('admin.product.equivalent.show', {equivalent: row.id}))
}
function onSetName(val, id) {
    router.visit(route('admin.product.equivalent.rename', {equivalent: id, name: val}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
</script>

