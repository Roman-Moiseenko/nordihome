<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="8">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Модификация">
                    <EditField :field="info.name" @update:field="onRename" />
                </el-descriptions-item>
                <el-descriptions-item label="Базовый товар">
                    {{ modification.base_product.name }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="16">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item v-for="(item, index) in modification.attributes" :label-class-name="'index-' + index">
                    <template #label>
                        <div class="flex items-center">
                            <img v-if="item.image" :src="item.image" width="40" height="40"/> <h2 class="font-medium ml-2">{{ item.name }}</h2>
                        </div>
                    </template>

                    <div class="flex flex-wrap mt-1">
                        <div v-for="variant in item.variants" class="flex ml-1">
                            <img v-if="variant.image" :src="variant.image" width="30" height="30"/> <h2 class="font-medium ml-2">{{ variant.name }}</h2>
                        </div>
                    </div>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>

    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import Active from "@Comp/Elements/Active.vue";
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    modification: Object,
})
const iSavingInfo = ref(false)
const info = reactive({
    name: props.modification.name,

})
const showEdit = ref(false)

function onRename(val) {
    info.name = val
    router.visit(
        route('admin.product.modification.rename', {modification: props.modification.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

</script>
<style lang="scss">
.el-descriptions__label.el-descriptions__cell.is-bordered-label {
    &.index-0 {
        --el-descriptions-item-bordered-label-background: var(--el-color-primary-light-9);
    }
    &.index-1 {
        --el-descriptions-item-bordered-label-background: var(--el-color-success-light-9);
    }
    &.index-2 {
        --el-descriptions-item-bordered-label-background: var(--el-color-warning-light-9);
    }
    &.index-3 {
        --el-descriptions-item-bordered-label-background: var(--el-color-info-light-9);
    }
}

</style>
