<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-ear-muffs"></i>
                <span> Сопутствующие</span>
            </span>
        </template>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-checkbox v-if="product.modification"
                             v-model="form.modification"
                             :checked="form.modification" class="checkbox-warning">
                    Сохранять для всех товаров из Модификации
                </el-checkbox>
                <SearchAddProduct  caption="Добавить аксессуар"
                                   :route="route('admin.product.edit.related', {product: product.id})"
                                   :preserveState="true"
                                   :params="form"
                />
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12">
                <div v-for="prod in product.related" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                    <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                    <span class="font-medium ml-3" style="width: 120px;">{{ prod.code }}</span>
                    <span class="font-medium ml-2"><Link type="primary" :href="route('admin.product.edit', prod.id)">{{ prod.name }}</Link></span>
                    <div class="ml-auto">
                        <el-button type="danger" @click="onRemoveRelated(prod.id)" ><i
                            class="fa-light fa-trash"></i></el-button>
                    </div>
                </div>
            </el-col>
        </el-row>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps } from "vue"
import {Link, router} from "@inertiajs/vue3"
import SearchAddProduct from "@Comp/Search/AddProduct.vue"

const props = defineProps({
    product: Object,
    errors: Object,
})

const isSaving = ref(false)
const form = reactive({
    modification: props.product.modification,
})

function onRemoveRelated(id) {
    isSaving.value = true;
    router.visit(route('admin.product.edit.related', {product: props.product.id}), {
        method: "post",
        data: {
            product_id: id,
            action: 'remove',
        },
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
        },
        onError: page => {
            isSaving.value = false

        },
    })
}


</script>

<style scoped>

</style>
