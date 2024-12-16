<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Акция {{ promotion.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <PromotionInfo :promotion="promotion"/>
        </div>
        <div class="flex mt-5">
            <SearchAddProduct :route="route('admin.discount.promotion.add-product', {promotion: promotion.id})"/>
            <SearchAddProducts :route="route('admin.discount.promotion.add-products', {promotion: promotion.id})" class="ml-3"/>
        </div>
        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                @row-click="routeClick"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар" width="300" show-overflow-tooltip/>
                <el-table-column label="Ценообразование" width="">
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
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из акции"/>

</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3"
import ru from 'element-plus/dist/locale/ru.mjs'
import PromotionInfo from "./Block/Info.vue"
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import {inject, ref} from "vue";
import { func } from "@Res/func"

const props = defineProps({
    promotion: Object,
    title: {
        type: String,
        default: 'Карточка акции',
    },
})
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.promotion.products])
const isSaving = ref(false)
function setProduct(row, val) {
    isSaving.value = true
    router.visit(route('admin.discount.promotion.set-product', {promotion: props.promotion.id}), {
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
    $delete_entity.show(route('admin.discount.promotion.del-product', {
        promotion: props.promotion.id,
        product_id: row.id
    }));
}
</script>
