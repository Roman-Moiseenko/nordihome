<template>
    <Head><title>Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Авито</h1>

        <div class="flex my-3">
            <el-button @click="router.get(route('admin.marketplace.avito.categories'))">
                Категории
            </el-button>
            <el-button class="ml-2" @click="router.get(route('admin.marketplace.avito.settings'))">
                Настройки
            </el-button>
            <el-button class="ml-2" @click="router.get(route('admin.marketplace.avito.operations'))">
                Операции
            </el-button>
        </div>

        <div class="flex my-3">
            <SearchAddProduct
                :route="route('admin.marketplace.avito.product.store')"
                caption="Добавить в Авито"
            />
            <div class="ml-3 my-auto">
                <Link :href="route('admin.marketplace.avito.index', { show: 'active' })">
                    Опубликованные
                </Link>
                ({{ filters.active }}) |
                <Link :href="route('admin.marketplace.avito.index', { show: 'archive' })">
                    В архиве
                </Link>
                ({{ filters.archive }})
            </div>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.search" placeholder="Поиск"/>
                <el-select v-model="filter.show" placeholder="Показать" class="mt-1">
                    <el-option key="active" value="active" label="Опубликованные"/>
                    <el-option key="archive" value="archive" label="В архиве"/>
                </el-select>
                <el-select v-model="filter.delivery" placeholder="Доставка" class="mt-1">
                    <el-option key="" value="" label="Все"/>
                    <el-option key="with" value="with" label="С доставкой"/>
                    <el-option key="without" value="without" label="Без доставки"/>
                </el-select>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                row-key="id"
                @row-click="routeClick"
            >
                <el-table-column prop="avitoId" label="Номер объявления" width="150"/>
                <el-table-column prop="name" label="Название" show-overflow-tooltip/>
                <el-table-column prop="code" label="Артикул" width="140"/>
                <el-table-column label="Категория" width="280" show-overflow-tooltip>
                    <template #default="scope">
                        {{ func.truncateMiddle(scope.row.categoryPath) }}
                    </template>
                </el-table-column>
                <el-table-column label="Автодоставка" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.delivery"/>
                    </template>
                </el-table-column>
                <el-table-column label="Активен" width="100" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active"/>
                    </template>
                </el-table-column>
                <el-table-column label="ТН ВЭД" width="100" align="center">
                    <template #default="scope">
                        <Active :active="!!scope.row.tnVed"/>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" width="220" align="center">
                    <template #default="scope">
                        <el-link
                            type="primary"
                            :underline="false"
                            @click.stop="toggleDelivery(scope.row)"
                        >
                            {{ scope.row.delivery ? 'Выкл доставку' : 'Вкл доставку' }}
                        </el-link>
                        &nbsp;|&nbsp;
                        <el-link
                            :type="scope.row.active ? 'warning' : 'success'"
                            :underline="false"
                            @click.stop="setActive(scope.row)"
                        >
                            {{ scope.row.active ? 'В архив' : 'Опубликовать' }}
                        </el-link>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, Link, router} from "@inertiajs/vue3";
import {reactive, ref} from "vue";
import {route} from "ziggy-js";
import {func} from '@Res/func.js'
import Active from "@Comp/Elements/Active.vue";
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";

const props = defineProps({
    products: Object,
    filters: Object,
})

const tableData = ref([...props.products.data])

const filter = reactive({
    search: props.filters.search ?? null,
    show: props.filters.show ?? null,
    delivery: props.filters.delivery ?? null,
})

function routeClick(row) {
    router.get(route('admin.marketplace.avito.product.show', {id: row.id}))
}

function toggleDelivery(row) {
    router.visit(route('admin.marketplace.avito.product.delivery', {id: row.id}), {
        method: 'post',
        preserveScroll: true,
        preserveState: false,
    })
}

function setActive(row) {
    router.visit(route('admin.marketplace.avito.product.active', {id: row.id}), {
        method: 'post',
        data: {active: !row.active},
        preserveScroll: true,
        preserveState: false,
    })
}
</script>
