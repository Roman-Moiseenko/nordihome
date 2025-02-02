<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-business-time"></i>
                <span> Управление</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-checkbox v-if="product.modification"
                     v-model="form.modification"
                     :checked="form.modification" class="checkbox-warning">
            Сохранять для всех товаров из Модификации
        </el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Товар опубликован">
                        <el-checkbox v-model="form.published" :checked="form.published" @change="onAutoSave" :disabled="isSaving"/>
                    </el-form-item>

                    <el-form-item label="Товар снят с продажи">
                        <el-checkbox v-model="form.not_sale" :checked="form.not_sale" @change="onAutoSave" :disabled="isSaving"/>
                    </el-form-item>
                    <el-form-item label="Приоритетный показ">
                        <el-checkbox v-model="form.priority" :checked="form.priority" @change="onAutoSave" :disabled="isSaving"/>
                    </el-form-item>
                    <el-form-item label="Скрывать в прайс-листах">
                        <el-checkbox v-model="form.hide_price" :checked="form.hide_price" @change="onAutoSave" :disabled="isSaving"/>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <h2>Хранение</h2>

                    <el-form-item v-for="storage in form.storages" :label="storage.name">
                        <el-input v-model="storage.cell" placeholder="Ячейка" @change="onAutoSave" :disabled="isSaving" style="width: 200px;"/>
                    </el-form-item>


                    <h2 class="mt-3">Баланс</h2>
                    <div class="flex items-center">
                        <el-form-item label="Мин.кол-во" label-position="top">
                            <el-input v-model="form.balance.min" @change="onAutoSave" :disabled="isSaving"
                                      style="width: 100px;"/>
                        </el-form-item>
                        <el-form-item label="Макс.кол-во" label-position="top">
                            <el-input v-model="form.balance.max" @change="onAutoSave" :disabled="isSaving"
                                      style="width: 100px;" class="ml-2" clearable/>
                        </el-form-item>
                        <el-form-item label="Закупать" label-position="top">
                        <el-switch
                            v-model="form.balance.buy"
                            inline-prompt
                            style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ccc"
                            active-text="Да"
                            inactive-text="Нет"
                            class="ml-2"
                            @change="onAutoSave" :disabled="isSaving"
                        />
                        </el-form-item>
                    </div>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 3 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <h2>Периодичность покупки</h2>
                    <el-form-item label-position="top" label="Для расчета показов">
                        <el-radio-group v-model="form.frequency" style="display: block;" @change="onAutoSave" :disabled="isSaving">
                            <div v-for="item in frequencies">
                            <el-radio :value="item.value" :label="item.label" />
                            </div>
                        </el-radio-group>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps } from "vue"
import {router} from "@inertiajs/vue3"

const props = defineProps({
    product: Object,
    errors: Object,
    frequencies: Array,
})
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    published: props.product.published,
    not_sale: props.product.not_sale,
    priority: props.product.priority,
    hide_price: props.product.hide_price,
    storages: [...props.product.storages],
    balance: props.product.balance,
    frequency: props.product.frequency,
    modification: props.product.modification,
})

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.management', {product: props.product.id}), {
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
