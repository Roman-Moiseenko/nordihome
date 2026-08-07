<template>
    <div class="flex">
        <el-input v-model="form.name" placeholder="Вариант" class="input-variant" @change="onEmit"/>

        <PhotoDTO model-type="catalog.attribute-variant" :entity-id="id" type="image" :mini="true" />
        <el-button type="danger" class="button-variant" @click="onRemove">-</el-button>
    </div>

</template>

<script setup lang="ts">
import { reactive } from "vue";
import UploadImageFile from "@Comp/UploadImageFile.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    id: Number,
    name: String,
    image: String,

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
function onSelectImage(val) {
    form.clear_file = val.clear_file;
    form.file = val.file
    onEmit();
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
</style>
