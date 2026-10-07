<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Модификация">
                    {{ modification.name }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="16">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item v-for="(item, index) in modification.attributes" :key="item.id" :label-class-name="'index-' + index">
                    <template #label>
                        <div class="flex items-center">
                            <h2 class="font-medium ml-2">{{ item.name }}</h2>
                        </div>
                    </template>

                    <div class="flex flex-wrap mt-1">
                        <el-tag v-for="variant in item.variants" :key="variant.id" class="ml-1">{{ variant.name }}</el-tag>
                    </div>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
</template>

<script setup>
const props = defineProps({
    modification: Object,
})
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
