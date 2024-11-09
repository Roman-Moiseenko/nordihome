<template>
    <div>
        <h2 class="font-medium mb-3">Изображение для каталога</h2>
        <div class="lg:w-48 lg:h-48 image-fit relative">
            <el-image
                v-bind:src="$props.image"
                :zoom-rate="1.2"
                :max-scale="3"
                :min-scale="0.2"
                :preview-src-list="[$props.image]"
                :initial-index="0"
                fit="cover"
            />
        </div>
    </div>
    <div>
        <h2 class="font-medium mb-3">Иконка для меню</h2>
        <div class="lg:w-48 lg:h-48 image-fit relative">
            <el-image
                v-bind:src="$props.icon"
                :zoom-rate="1.2"
                :max-scale="1"
                :min-scale="0.2"
                :preview-src-list="[$props.icon]"
                :initial-index="0"
                fit="cover"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps } from 'vue'
const props = defineProps({
    image: String,
    icon: String,
});
</script>
