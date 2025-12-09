<template>
    <div class="flex">
        <el-tag class="mx-2">{{ index }}</el-tag>
        <el-tag v-if="!isEdit" type="info" effect="dark">{{ item.name }}</el-tag>
        <el-form v-if="isEdit" label-width="auto">

                <el-input v-model="form.url" />


                <el-input v-model="form.name" />

        </el-form>
        <el-button v-if="!isEdit" type="success" plain size="small" @click="isEdit = true" class="ml-auto"><el-icon><i class="fa-light fa-pen-to-square"></i></el-icon></el-button>
        <div v-if="isEdit">
            <el-button type="success" size="small" @click.stop="saveItem" class="my-auto ml-1">
                <i class="fa-light fa-floppy-disk"></i>
            </el-button>
            <el-button type="info" size="small" @click.stop="isEdit = false" style="margin-left: 4px" class="my-auto">
                <i class="fa-light fa-xmark"></i>
            </el-button>
        </div>

        <el-button type="danger" plain size="small" @click="onDelete" class="ml-0"><el-icon><Delete /></el-icon>
    </el-button>
    </div>
</template>

<script setup lang="ts">
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";
import {router} from "@inertiajs/vue3";
import { menuStore } from './store.js'

const props = defineProps({
    item: Object,
    index: Number
})
const isEdit = ref(false)
const form = reactive({
    name: props.item.name,
    url: props.item.url,
})
const emit = defineEmits(['del:item']);
function saveItem() {
    isEdit.value = false
    router.visit(route('admin.page.menu.set-item', {item: props.item.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            menuStore().beforeReload()
            emit('del:item', true)
        }
    })
}
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
