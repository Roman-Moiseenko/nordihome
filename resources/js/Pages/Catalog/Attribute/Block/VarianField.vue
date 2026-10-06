<template>
    <div class="flex">
        <el-input v-model="form.name" placeholder="Вариант" class="input-variant" @change="onEmit"/>

        <PhotoDTO v-if="id" model-type="catalog.attribute-variant" :entity-id="id" type="image" :mini="true" />
        <el-tooltip
            v-else
            content="Сначала сохраните атрибут, чтобы добавить изображение варианта"
            placement="top"
            effect="dark"
        >
            <i class="fa-light fa-floppy-disk variant-image-placeholder"></i>
        </el-tooltip>

        <el-button type="danger" class="button-variant" @click="onRemove">-</el-button>
    </div>

</template>

<script setup lang="ts">
import { reactive } from "vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    id: Number,
    name: String,
})
const $emit = defineEmits(['update:fields', 'remove:fields'])
const form = reactive({
    name: props.name,
    file: null,
    clear_file: false,
})
function onEmit() {
    $emit('update:fields', form)
}

function onRemove() {
    $emit('remove:fields', true)
}

</script>
<style lang="scss">
.input-variant {
    max-height: 32px;
    width: 220px;
    margin: auto 0;
    margin-right: 8px;
}
.button-variant {
    max-height: 32px;
    margin: auto 0;
    margin-left: 8px;
}
.variant-image-placeholder {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    margin: auto 0;
    margin-left: 8px;
    color: var(--el-text-color-secondary);
}
</style>
