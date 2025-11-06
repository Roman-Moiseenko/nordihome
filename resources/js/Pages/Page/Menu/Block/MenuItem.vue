<template>
    <el-tag class="mx-2">{{ index }}</el-tag> <el-tag type="info" effect="dark">{{ item.name }}</el-tag>
    <el-button type="danger" plain size="small" @click="onDelete"><el-icon><Delete /></el-icon>
    </el-button>
</template>

<script setup lang="ts">
import {defineProps, inject, ref} from "vue";
import {route} from "ziggy-js";
import {router} from "@inertiajs/vue3";
import { menuStore } from './store.js'

const props = defineProps({
    item: Object,
    index: Number
})
const emit = defineEmits(['del:item']);
function onDelete() {
    router.visit(route('admin.page.menu.delete-item', {item: props.item.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            menuStore().beforeReload()
            emit('del:item', true)
        }
    })
}

</script>
<style scoped>

</style>
