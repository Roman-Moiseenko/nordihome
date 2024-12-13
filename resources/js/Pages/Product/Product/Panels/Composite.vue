<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-object-exclude"></i>
                <span> Составной товар</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <SearchAddProduct  caption="Добавить"
                                   :route="route('admin.product.edit.composite', {product: product.id})"
                                   :preserveState="true"
                                   :quantity="true"
                />
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12">
                <div v-for="prod in form.composite" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                    <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                    <span class="font-medium ml-3" style="width: 120px;">{{ prod.code }}</span>
                    <span class="font-medium ml-2"><Link type="primary" :href="route('admin.product.edit', prod.id)">{{ prod.name }}</Link></span>
                    <el-input v-model="prod.quantity" @change="onAutoSave" :disabled="isSaving" class="ml-2" style="width: 160px;">
                        <template #append>шт</template>
                    </el-input>
                    <div class="ml-2">
                        <el-button type="danger" @click="onRemoveComposite(prod.id)" ><i
                            class="fa-light fa-trash"></i></el-button>
                    </div>
                </div>
            </el-col>
        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps, watch} from "vue"
import {Link, router} from "@inertiajs/vue3"
import SearchAddProduct from "@Comp/Search/AddProduct.vue";

const props = defineProps({
    product: Object,
    errors: Object,
})
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    composite: [...props.product.composite],
})
watch(() => props.product, (newValues, oldValues) => {
    form.composite = [...props.product.composite]
});
function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onRemoveComposite(id) {
    isSaving.value = true
    router.visit(route('admin.product.edit.composite', {product: props.product.id}), {
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
    isSaving.value = true;
    form.action = 'edit'
    router.visit(route('admin.product.edit.composite', {product: props.product.id}), {
        method: "post",
        data: form,
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
