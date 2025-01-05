<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-face-smile-plus"></i>
                <span> Бонусные товары</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <SearchAddProduct  caption="Добавить бонус"
                                   :route="route('admin.product.edit.bonus', {product: product.id})"
                                   :preserveState="true"
                />
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12">
                <div v-for="prod in form.bonus" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                    <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                    <span class="font-medium ml-3" style="width: 120px;">{{ prod.code }}</span>
                    <span class="font-medium ml-2"><Link type="primary" :href="route('admin.product.edit', prod.id)">{{ prod.name }}</Link></span>
                    <span class="text-red-800 line-through ml-auto">{{ func.price(prod.price) }}</span>
                    <el-input v-model="prod.discount" @change="onAutoSave" :disabled="isSaving" class="ml-2" style="width: 160px;">
                        <template #append>₽</template>
                    </el-input>
                    <div class="ml-2">
                        <el-button type="danger" @click="onRemoveBonus(prod.id)" ><i
                            class="fa-light fa-trash"></i></el-button>
                    </div>
                </div>
            </el-col>


        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps, watch } from "vue"
import {Link, router} from "@inertiajs/vue3"
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import {func} from "@Res/func"

const props = defineProps({
    product: Object,
    errors: Object,
})
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    bonus: [...props.product.bonus],
    action: null,
})
watch(() => props.product, (newValues, oldValues) => {
    form.bonus = [...props.product.bonus]
});
function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onRemoveBonus(id) {
    isSaving.value = true
    router.visit(route('admin.product.edit.bonus', {product: props.product.id}), {
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
function onSave() {
    isSaving.value = true
    form.action = 'edit'
    router.visit(route('admin.product.edit.bonus', {product: props.product.id}), {
        method: "post",
        data: form,
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
            console.log(page.props.product)
        },
        onError: page => {
            isSaving.value = false

        },
    })
}

</script>

<style scoped>

</style>
