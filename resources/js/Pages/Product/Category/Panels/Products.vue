<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>

        <el-table
            :data="[...category.products]"
            header-cell-class-name="nordihome-header"
            style="width: 100%;"

        >
            <el-table-column prop="image" label="IMG" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column sortable prop="code" label="Артикул"  width="160"/>
            <el-table-column prop="name" label="Товар" show-overflow-tooltip>
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
            <el-table-column sortable prop="published" label="Опубликован" width="140" align="center">
                <template #default="scope">
                    <Active :active="scope.row.published"/>
                </template>
            </el-table-column>
            <el-table-column sortable prop="not_sale" label="В продаже" width="140" align="center">
                <template #default="scope">
                    <Active :active="!scope.row.not_sale"/>
                </template>
            </el-table-column>
        </el-table>

    </el-tab-pane>
    <DeleteEntityModal name_entity="Товар" name="product"/>
</template>

<script setup lang="ts">
import {defineProps, inject, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps({
    category: Object,
})
const $delete_entity = inject("$delete_entity", "product")
function routeClick(row) {
    router.get(route('admin.product.edit', {product: row.id}))
}

function onRestore(row) {
    router.post(route('admin.product.restore', {product: row.id}))
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

</script>
