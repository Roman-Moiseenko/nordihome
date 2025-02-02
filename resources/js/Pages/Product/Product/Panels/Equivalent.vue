<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-balloons"></i>
                <span> Аналоги</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-checkbox v-if="product.modification"
                     v-model="form.modification"
                     :checked="form.modification" class="checkbox-warning">
            Сохранять для всех товаров из Модификации
        </el-checkbox>
        <el-row :gutter="20" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="6">
                <el-form label-width="auto">
                    <el-form-item label="Связанная группа аналогичных товаров" label-position="top">
                        <el-select v-model="form.equivalent_id" @change="onAutoSave" :disabled="isSaving">
                            <el-option :key="null" :value="null" label="~ Без группы ~"/>
                            <el-option v-for="item in equivalents" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.field" class="text-red-700">{{ errors.field }}</div>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12">
                <el-form label-width="auto">
                    <div v-if="product.equivalent">
                        <h2>Товары из группы</h2>
                        <div v-for="equi_prod in product.equivalent.products" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md">
                            <img v-if="equi_prod.image" :src="equi_prod.image" width="40" height="40"/>
                            <span class="font-medium ml-3" style="width: 120px;">{{ equi_prod.code }}</span>
                            <span class="font-medium ml-2">
                                <span v-if="equi_prod.id === product.id">{{ equi_prod.name }}</span>
                                <Link v-else type="primary" :href="route('admin.product.edit', {product: equi_prod.id})">{{ equi_prod.name }}</Link>
                            </span>
                        </div>
                    </div>
                    <!-- Повторить -->
                </el-form>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps } from "vue"
import {Link, router} from "@inertiajs/vue3"

const props = defineProps({
    product: Object,
    errors: Object,
    equivalents: Array,
})
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    equivalent_id: !props.product.equivalent ? null : props.product.equivalent.id,
    modification: props.product.modification,

})

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.equivalent', {product: props.product.id}), {
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
