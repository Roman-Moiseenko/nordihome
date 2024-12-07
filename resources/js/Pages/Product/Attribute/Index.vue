<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Атрибуты товаров</h1>
        <div class="flex items-center">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать атрибут
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Атрибут"/>
                <el-select v-model="form.categories" placeholder="Категории" class="mt-1" filterable multiple>
                    <el-option v-for="item in categories" :value="item.id" :label="item.name"/>
                </el-select>
                <el-select v-model="form.group_id" placeholder="Группа" class="mt-1" filterable>
                    <el-option v-for="item in groups" :value="item.id" :label="item.name"/>
                </el-select>
                <el-select v-model="form.type" placeholder="Тип значения" class="mt-1" filterable>
                    <el-option v-for="item in types" :value="item.value" :label="item.label"/>
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <el-button type="success" plain class="ml-2" @click="handleGroup">Управление группами</el-button>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Атрибут"/>
                <el-select v-model="filter.category_id" placeholder="Категория" class="mt-1">
                    <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name" />
                </el-select>
                <el-select v-model="filter.group_id" placeholder="Группа" class="mt-1">
                    <el-option v-for="item in groups" :key="item.id" :value="item.id" :label="item.name" />
                </el-select>
                <el-select v-model="filter._filter" placeholder="Фильтр" class="mt-1">
                    <el-option :key="null" :value="null" label="Все" />
                    <el-option :key="true" :value="true" label="Да" />
                    <el-option :key="false" :value="false" label="Нет" />
                </el-select>
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
                <el-table-column prop="image" label="Иконка" width="80">
                    <template #default="scope">
                        <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Атрибут"  width="220" show-overflow-tooltip/>
                <el-table-column prop="categories" label="Категории" align="center">
                    <template #default="scope">
                        <el-tag type="info" v-for="item in scope.row.categories" class="ml-1">{{ item.name }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="group" label="Группа" width="220" show-overflow-tooltip/>
                <el-table-column prop="filter" label="Фильтр" width="160" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.filter" />
                    </template>
                </el-table-column>
                <el-table-column prop="type_text" label="Тип" show-overflow-tooltip/>


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
            :current_page="attributes.current_page"
            :per_page="attributes.per_page"
            :total="attributes.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Атрибут" />
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
    attributes: Object,
    title: {
        type: String,
        default: 'Атрибуты товаров',
    },
    filters: Array,
    categories: Array,
    groups: Array,
    types: Array,
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.attributes.data])
const filter = reactive({
    name: props.filters.name,
    group_id: props.filters.group_id,
    category_id: props.filters.category_id,
    _filter: props.filters._filter,
})
const form = reactive({
    name: null,
    categories: [],
    group_id: null,
    type: null,
})


function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.attribute.destroy', {attribute: row.id}));
}
function createButton() {
    router.post(route('admin.product.attribute.store', form))
}
function routeClick(row) {
    router.get(route('admin.product.attribute.show', {attribute: row.id}))
}
function handleGroup() {
    router.get(route('admin.product.attribute.groups'))
}
</script>

