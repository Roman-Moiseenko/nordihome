<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>
        <TableRelation entity="brand" :id="brandId" />
    </el-tab-pane>
</template>

<script lang="ts" setup>
import {defineProps} from "vue";
import TableRelation from "@Comp/Product/TableRelation.vue";

const props = defineProps({
    brandId: Number,
})

</script>
