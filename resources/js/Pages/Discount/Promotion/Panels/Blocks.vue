<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-photo-film-music"></i>
                <span> Контент блоки</span>
            </span>
        </template>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <ContentBlockEditor
                :blocks="blocks || []"
                :container-id="promotionId"
                container-type="promotion"
            />
        </div>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import ContentBlockEditor from "@Comp/Content/ContentBlock/ContentBlockEditor.vue";

const props = defineProps({
    blocks: Array,
    promotionId: Number,
})

</script>
