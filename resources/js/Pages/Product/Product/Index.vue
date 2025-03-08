<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Товары</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onCreateProduct" ref="buttonRef">
                Создать товар
            </el-button>
            <div class="ml-3 my-auto">
                <Link type="primary" :href="route('admin.product.index')">Все</Link>
                ({{ count.all }}) |
                <Link type="primary" :href="route('admin.product.index', {show: 'active'})">Опубликованные</Link>
                ({{ count.active }}) |
                <Link type="primary" :href="route('admin.product.index', {show: 'not_sale'})">Снятые с продажи</Link>
                ({{ count.not_sale }}) |
                <Link type="primary" :href="route('admin.product.index', {show: 'draft'})">Черновики</Link>
                ({{ count.draft }})
                <Link type="primary" :href="route('admin.product.index', {show: 'delete'})">Удаленные</Link>
                ({{ count.delete }})
            </div>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Товар"/>
                <el-select v-model="filter.category" placeholder="Выберите категорию" class="mt-1">
                    <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name"/>
                </el-select>
                <el-select v-model="filter.show" placeholder="Показать" class="mt-1">
                    <el-option key="active" value="active" label="Опубликованные"/>
                    <el-option key="not_sale" value="not_sale" label="Снятые с продажи"/>
                    <el-option key="draft" value="draft" label="Черновики"/>
                    <el-option key="delete" value="delete" label="Удаленные"/>
                </el-select>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <SelectActions type="top" @change:action="onAction"/>
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                v-loading="store.getLoading"
                row-key="id"
                @selection-change="selectedTable"
            >
                <el-table-column type="selection" width="55"/>
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="code" label="Артикул" width="120"/>
                <el-table-column prop="name" label="Название" show-overflow-tooltip>
                    <template #default="scope">
                        <div>
                            {{ scope.row.name }}
                            <el-tag v-if="!scope.row.published" type="info" class="ml-2">Черновик</el-tag>
                            <el-tag v-if="scope.row.not_sale" type="warning" class="ml-1">Снят с продажи</el-tag>
                        </div>
                        <div>
                            <div class="show-on-hover items-center">
                                <!--Для удаленных товаров-->
                                <template v-if="scope.row.trashed">
                                    <el-link type="success" :underline="false" @click="onRestore(scope.row)">
                                        Восстановить
                                    </el-link>
                                    &nbsp;|&nbsp;
                                    <el-link type="danger" :underline="false" @click="onFullDelete(scope.row)">
                                        Удалить окончательно
                                    </el-link>
                                </template>
                                <!--Для видимых товаров-->
                                <template v-else>
                                    <el-link type="primary" :underline="false" @click="onEdit(scope.row)">
                                        Изменить
                                    </el-link>
                                    &nbsp;|&nbsp;
                                    <el-link type="primary" :underline="false" @click="onAnalitics(scope.row)">
                                        Статистика
                                    </el-link>
                                    &nbsp;|&nbsp;
                                    <el-link type="info" :underline="false"
                                             :href="scope.row.published
                                             ? route('shop.product.view', {slug: scope.row.slug})
                                             : route('shop.product.view-draft', {product: scope.row.id})"
                                             target="_blank">
                                        Просмотр
                                    </el-link>
                                    &nbsp;|&nbsp;
                                    <el-link type="warning" :underline="false" @click="onSaleToggle(scope.row)">
                                        {{ scope.row.not_sale ? 'Вернуть в продажу' : 'Снять с продажи' }}
                                    </el-link>
                                    &nbsp;|&nbsp;
                                    <el-link type="success" :underline="false" @click="onPublishedToggle(scope.row)">
                                        {{ scope.row.published ? 'В черновик' : 'Опубликовать' }}
                                    </el-link>
                                </template>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="category_name" label="Категория" width="250" align="center"
                                 show-overflow-tooltip/>
                <el-table-column prop="published" label="Опубликован" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published"/>
                    </template>
                </el-table-column>
                <el-table-column prop="not_sale" label="В продаже" width="100" align="center">
                    <template #default="scope">
                        <Active :active="!scope.row.not_sale"/>
                    </template>
                </el-table-column>

            </el-table>
            <SelectActions type="bottom" @change:action="onAction"/>
        </div>

        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />
        <DeleteEntityModal name_entity="Товар"/>
    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {Head, Link, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";
import SelectActions from "./SelectActions.vue";
import {ElLoading} from "element-plus";

const props = defineProps({
    products: Object,
    title: {
        type: String,
        default: 'Список всех товаров',
    },
    filters: Array,
    categories: Array,
    count: Array,
})

const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.products.data])
const filter = reactive({
    name: props.filters.name,
    category: props.filters.category,
    show: props.filters.show,
})


const formMass = reactive({
    action: null,
    ids: [],
})

function onRestore(row) {
    const loading = ElLoading.service({
        lock: false,
        text: 'Идет восстановление',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.product.restore', {id: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            loading.close()
        },
        onFinish: page => {
            loading.close()
        },
    })
}

function onFullDelete(row) {
    $delete_entity.show(route('admin.product.full-delete', {id: row.id}));
}

function onEdit(row) {
    router.visit(route('admin.product.edit', {product: row.id}), {
        method: "get",
        preserveState: true,
        preserveScroll: true,
    })
}

function onAnalitics(row) {
    router.get(route('admin.product.show', {product: row.id}))
}

function onCreateProduct() {
    router.get(route('admin.product.create'))

}

function onSaleToggle(row) {
    router.visit(route('admin.product.sale', {product: row.id}), {
        method: "post",
        preserveState: false,
        preserveScroll: true,
    })
}

function onPublishedToggle(row) {
    router.visit(route('admin.product.toggle', {product: row.id}), {
        method: "post",
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            tableData.value = [...page.props.products.data]
        }
    })
}


function routeClick(row) {
    router.get(route('admin.product.show', {product: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.destroy', {product: row.id}));
}

///Массовые действия
function selectedTable(val) {
    formMass.ids = [...val.map(item => item.id)]
}

function onAction(val) {
    formMass.action = val
    router.visit(route('admin.product.action'), {
        method: "post",
        data: formMass,
        preserveState: false,
        preserveScroll: true,
        onSuccess: page => {
            formMass.ids = []
            formMass.action = null
        }
    })
}

</script>
<style lang="scss">
.show-on-hover {
    display: none;
}

.el-table__row:hover {
    .show-on-hover {
        display: block;
    }
}
</style>
