<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-images"></i>
                <span> Изображения</span>
            </span>
        </template>
        <PhotoDTO model-type="catalog.product" :entity-id="productId" type="gallery"/>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps} from "vue"
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    name: {
        type: String,
        default: 'image',
    },
})
</script>
