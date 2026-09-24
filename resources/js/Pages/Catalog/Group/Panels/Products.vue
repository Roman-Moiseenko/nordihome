<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>
        <div class="flex mt-5">
            <SearchAddProduct
                :route="route('admin.catalog.group.add-product', {group: group.id})"
                :search="route('admin.catalog.group.search', {group: group.id})"
            />
            <SearchAddProducts :route="route('admin.catalog.group.add-products', {group: group.id})" class="ml-3"/>
        </div>

        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                v-loading="store.getLoading"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар" width="300" show-overflow-tooltip/>
                <el-table-column prop="category" label="Основная категория" width="" show-overflow-tooltip />

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
        <DeleteEntityModal name_entity="Товар из группы" />
    </el-tab-pane>
</template>

<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {useStore} from "@Res/store.js"
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'

const props = defineProps({
    group: Object,
})

const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.group.products.data])

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.catalog.group.del-product', {group: props.group.id, product_id: row.id}));
}

</script>
