<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-images"></i>
                <span> Изображения</span>
            </span>
        </template>
        <PhotoDTO model-type="catalog.product" :entity-id="product.id" type="gallery" />
    </el-tab-pane>
</template>

<script setup lang="ts">
import { defineProps} from "vue"
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    product: Object,
})

</script>
