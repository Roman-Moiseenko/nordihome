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
                :route="route('admin.discount.promotion.products.attach', {id: promotionId})"
            />
            <SearchAddProducts
                :route="route('admin.discount.promotion.products.attach', {id: promotionId})"
                class="ml-3"/>

        </div>
        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар"  show-overflow-tooltip/>
                <el-table-column label="Ценообразование" width="260" align="center">
                    <template #default="scope">
                        <div class="flex">
                            <span class="ml-auto text-red-800 line-through my-auto font-medium">{{ func.price(scope.row.price) }}</span>
                            <el-input class=" ml-2" style="width: 160px;"
                                      v-model="scope.row.discount"
                                      @change="val => setProduct(scope.row, val)"
                                      :disabled="isSaving">
                                <template #append>₽</template>
                            </el-input>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Остаток" width="120"  align="center"/>
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
        <DeleteEntityModal name_entity="Товар из акции"/>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps, inject, onMounted, ref} from "vue";
import {route} from "ziggy-js";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import {func} from "@Res/func";
import {router} from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    promotionId: Number,
})
const $delete_entity = inject("$delete_entity")
const tableData = ref<Array>([])
const isSaving = ref(false)
const showHelp = ref(false);
interface Product {
    id: number
    code: string
    name: string
    image: string | null
    price: number,
    discount: number,
    quantity: number | null
}
interface Pagination {
    current_page: number
    last_page: number
    per_page: number
    total: number
    data: Product[]
}
const loading = ref(false)
const pagination = ref<Pagination>({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    data: [],
})
onMounted(() => {
    fetchProducts(1)
})
function fetchProducts(page: number = 1) {
    loading.value = true
    axios.get(route(`admin.discount.promotion.products`, {id: props.promotionId, page}))
        .then(response => {
            pagination.value = response.data
            tableData.value = response.data.data

            console.log(response.data.data)
            // Загружаем изображения для полученных товаров
            const ids = response.data.data.map((p: Product) => p.id)
            if (ids.length > 0) {
                axios.get(route('admin.photo.get-by-ids'), {
                    params: {
                        imageableIds: ids,
                        modelType: 'catalog.product',
                        type: 'gallery',
                    }
                }).then(photoResponse => {
                    tableData.value = tableData.value.map((product: Product) => ({
                        ...product,
                        image: photoResponse.data[product.id] || null,
                    }))
                })
            }
        })
        .finally(() => {
            loading.value = false
        })
}

function setProduct(row, val) {
    isSaving.value = true
    router.visit(route('admin.discount.promotion.set-product', {id: props.promotionId}), {
        method: "post",
        data: {
            product_id: row.id,
            price: val
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            isSaving.value = false
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.discount.promotion.products.detach', {
        id: props.promotionId,
        product_id: row.id
    }));
}
</script>
