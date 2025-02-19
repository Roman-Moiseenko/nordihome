<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-pallet-boxes"></i>
                <span> Атрибуты</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-checkbox v-if="product.modification"
                     v-model="form.modification"
                     :checked="form.modification" class="checkbox-warning">
            Сохранять для всех товаров из Модификации
        </el-checkbox>
        <el-row :gutter="10" class="mt-2 attribute-block">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-form label-width="auto">

                    <div v-for="(attribute, index) in form.attributes" class="flex mt-2">
                        <el-form-item :label="attribute.group + '\\' + attribute.name" label-position="top"
                                      class="ml-2">
                            <!-- Вариант -->
                            <el-select v-if="attribute.is_variant" v-model="attribute.value"
                                       :multiple="attribute.multiple" @change="onAutoSave" :disabled="isSaving || attribute.is_modification">
                                <el-option v-for="item in attribute.variants" :key="item.id" :value="item.id"
                                           :label="item.name"/>
                            </el-select>
                            <!-- Флажок -->
                            <el-checkbox v-if="attribute.is_bool" v-model="attribute.value" :label="attribute.name"
                                         :checked="attribute.value" @change="onAutoSave" :disabled="isSaving"/>

                            <!-- Число -->
                            <el-input v-if="attribute.is_numeric" v-model="attribute.value" :label="attribute.name"
                                      :formatter="val => func.MaskFloat(val)" @change="onAutoSave" :disabled="isSaving"/>
                            <!-- Строка -->
                            <el-input v-if="attribute.is_string" v-model="attribute.value" :label="attribute.name"
                                      @change="onAutoSave" :disabled="isSaving"/>
                            <!-- //TODO Дата -->

                        </el-form-item>
                        <el-form-item label="Удалить" label-position="top" class="ml-2">
                            <el-button type="danger" @click="onRemoveAttribute(index)" :disabled="attribute.is_modification"><i
                                class="fa-light fa-trash"></i></el-button>
                        </el-form-item>
                    </div>



                </el-form>
                <div class="flex items-center mt-5">
                    <el-select v-model="new_attribute" placeholder="Выбрать атрибут">
                        <el-option v-for="item in product.possible_attributes" :value="item.id" :label="item.name"/>
                    </el-select>
                    <el-button type="success" @click="onAddAttribute" class="ml-2">Добавить</el-button>
                </div>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps} from "vue"
import {router} from "@inertiajs/vue3"
import {func} from "@Res/func"

const props = defineProps({
    product: Object,
    errors: Object,
})
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    attributes: [...props.product.attributes],
    modification: props.product.modification,
})

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}

function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.attribute', {product: props.product.id}), {
        method: "post",
        data: form,
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
            form.attributes = [...page.props.product.attributes]
        },
        onError: page => {
            isSaving.value = false

        },
    })
}

const new_attribute = ref(null)

function onRemoveAttribute(index) {
    form.attributes.splice(index, 1)
    onAutoSave()
}

function onAddAttribute() {
    form.attributes.push({
        id: new_attribute.value,
        group: 'Идет сохранение',
        name: '...',
    })
    onSave()
    new_attribute.value = null;
}

</script>

<style lang="scss">
.attribute-block {
    .el-input {
        --el-input-width: 300px;
    }

    .el-select {
        --el-select-width: 300px;
    }
}
</style>
