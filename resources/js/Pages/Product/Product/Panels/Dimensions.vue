<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-truck-ramp-box"></i>
                <span> Габариты</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-checkbox v-if="product.modification"
                     v-model="form.modification"
                     :checked="form.modification" class="checkbox-warning">
            Сохранять для всех товаров из Модификации
        </el-checkbox>
        <el-row :gutter="10" class="mt-2 dimensions-block">
            <!-- Колонка 1 -->
            <el-col :span="16">
                <el-form label-width="auto">
                    <h2>Габариты товара в собранном виде</h2>
                    <div class="flex mt-2">
                        <el-form-item label="Тип" label-position="top">
                            <el-select v-model="form.dimensions.type" @change="onTypeDimensions" :disabled="isSaving">
                                <el-option v-for="item in dimensions" :key="item.value" :value="item.value"
                                           :label="item.label"/>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="labelDimensions.height + ' (см)'" label-position="top" class="ml-2">
                            <el-input v-model="form.dimensions.height" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                            <div v-if="errors['dimensions.height']" class="text-red-700">{{
                                    errors['dimensions.height']
                                }}
                            </div>
                        </el-form-item>
                        <el-form-item :label="labelDimensions.width + ' (см)'" label-position="top" class="ml-2">
                            <el-input v-model="form.dimensions.width" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                            <div v-if="errors['dimensions.width']" class="text-red-700">{{
                                    errors['dimensions.width']
                                }}
                            </div>
                        </el-form-item>
                        <el-form-item v-if="labelDimensions.depth" :label="labelDimensions.depth + ' (см)'"
                                      label-position="top" class="ml-2">
                            <el-input v-model="form.dimensions.depth" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                            <div v-if="errors['dimensions.depth']" class="text-red-700">{{
                                    errors['dimensions.depth']
                                }}
                            </div>
                        </el-form-item>
                        <el-form-item label="Вес" label-position="top" class="ml-5">
                            <el-input v-model="form.dimensions.weight" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskFloat(val)"/>
                            <div v-if="errors['dimensions.weight']" class="text-red-700">{{
                                    errors['dimensions.weight']
                                }}
                            </div>
                        </el-form-item>

                        <el-form-item label="Ед.изм." label-position="top" class="ml-2">
                            <el-select v-model="form.dimensions.measure" @change="onAutoSave" :disabled="isSaving">
                                <el-option key="г" value="г" label="г"/>
                                <el-option key="кг" value="кг" label="кг"/>
                            </el-select>
                        </el-form-item>
                    </div>
                    <!-- Повторить -->

                    <h2 class="mt-4">Габариты упакованного товара</h2>
                    <div v-for="(item, index) in form.packages" class="flex mt-2">
                        <div class="my-auto">
                            <el-tag type="info">{{ index + 1}}</el-tag>
                        </div>
                        <el-form-item label="Высота (см)" label-position="top" class="ml-2">
                            <el-input v-model="item.height" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                        </el-form-item>
                        <el-form-item label="Ширина (см)" label-position="top" class="ml-2">
                            <el-input v-model="item.width" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                        </el-form-item>
                        <el-form-item label="Длина (см)" label-position="top" class="ml-2">
                            <el-input v-model="item.length" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                        </el-form-item>

                        <el-form-item label="Вес (кг)" label-position="top" class="ml-2">
                            <el-input v-model="item.weight" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskFloat(val)"/>
                        </el-form-item>
                        <el-form-item label="Кол-во" label-position="top" class="ml-2">
                            <el-input v-model="item.quantity" @change="onAutoSave" :disabled="isSaving"
                                      :formatter="val => func.MaskInteger(val)"/>
                        </el-form-item>
                        <el-form-item label="Удалить" label-position="top" class="ml-2">
                            <el-button type="danger" @click="onRemovePackage(index)"><i class="fa-light fa-trash"></i></el-button>
                        </el-form-item>
                    </div>
                    <el-button type="success" @click="onAddPackage" class="mt-1">Добавить</el-button>
                </el-form>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <h2>Доставка товара</h2>

                    <el-form-item label="Доставка в пределах региона">
                        <el-checkbox v-model="form.local" :checked="form.local" @change="onAutoSave" :disabled="isSaving"/>
                    </el-form-item>
                    <el-form-item label="Доставка ТК по России">
                        <el-checkbox v-model="form.delivery" :checked="form.delivery" @change="onAutoSave" :disabled="isSaving"/>
                    </el-form-item>

                    <el-form-item label="Сложность упаковки">
                        <el-select v-model="form.complexity" @change="onAutoSave" :disabled="isSaving">
                            <el-option v-for="item in complexities" :key="item.value" :value="item.value" :label="item.label" />
                        </el-select>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps} from "vue"
import {router} from "@inertiajs/vue3"
import {func} from '@Res/func'

const props = defineProps({
    product: Object,
    errors: Object,
    dimensions: Array,
    complexities: Array,
})
console.log(props.product)
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    dimensions: props.product.dimensions,
    packages: [...props.product.packages.packages],
    local: props.product.local,
    delivery: props.product.delivery,
    complexity: props.product.packages.complexity,
    modification: props.product.modification,
})

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.dimensions', {product: props.product.id}), {
        method: "post",
        data: form,
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
            form.packages = [...page.props.product.packages.packages]
        },
        onError: page => {
            isSaving.value = false
        },
    })
}

const labelDimensions = reactive({
    height: 'Высота',
    width: 'Ширина',
    depth: 'Глубина',
})

function onTypeDimensions(val) {
    if (val === 1) {
        labelDimensions.width = 'Ширина';
        labelDimensions.depth = 'Глубина';
    }
    if (val === 2) {
        labelDimensions.width = 'Ширина';
        labelDimensions.depth = 'Длина';
    }
    if (val === 3) {
        labelDimensions.width = 'Диаметр';
        labelDimensions.depth = null;
    }
    onAutoSave()
}

function onRemovePackage(index) {
    form.packages.splice(index, 1)
    onAutoSave()
}
function onAddPackage() {
    form.packages.push({
        height: 0,
        width: 0,
        length: 0,
        weight: 0,
        quantity: 1,
    })
    onAutoSave()
}

</script>

<style lang="scss">
.dimensions-block {
    .el-input {
        --el-input-width: 100px;
    }

    .el-select {
        --el-select-width: 110px;
    }
}
</style>
