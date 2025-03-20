<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Группы товаров</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать группу
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="new_group" placeholder="Группа" class="mt-1"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.product" placeholder="Товар" class="mt-1"/>
                <el-input v-model="filter.group" placeholder="Группа" class="mt-1"/>
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
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Название группы"  width="280" show-overflow-tooltip/>
                <el-table-column prop="quantity" label="Кол-во товаров" width="180" align="center"/>
                <el-table-column prop="page" label="Страница" width="160" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published" />
                    </template>
                </el-table-column>
                <el-table-column prop="description" label="Описание" show-overflow-tooltip/>
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
            :current_page="groups.current_page"
            :per_page="groups.per_page"
            :total="groups.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Группу" />
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

const props = defineProps({
    groups: Object,
    title: {
        type: String,
        default: 'Группы товаров',
    },
    filters: Array,
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.groups.data])
const filter = reactive({
    product: props.filters.product,
    group: props.filters.group,

})
const new_group = ref('')


function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.group.destroy', {group: row.id}));
}
function createButton() {
    router.post(route('admin.product.group.store', {name: new_group.value}))
}
function routeClick(row) {
    router.get(route('admin.product.group.show', {group: row.id}))
}
</script>

