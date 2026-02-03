<template>
    <div class="flex">
        <el-tag class="mx-2">{{ index }}</el-tag>
        <el-tag v-if="!isEdit" type="info" effect="dark">{{ item.name }}</el-tag>
        <el-tag type="info" effect="plain" class="ml-3"> SVG
        <Active :active="item.svg !== null && item.svg !== ''" />
        </el-tag>
        <el-button type="success" plain size="small" @click="editDialog = true" class="ml-auto">
            <el-icon><i class="fa-light fa-pen-to-square"></i></el-icon>
        </el-button>


        <el-button type="danger" plain size="small" @click="onDelete" class="ml-0">
            <el-icon>
                <Delete/>
            </el-icon>
        </el-button>

        <el-dialog v-model="editDialog" title="Редактирование пункта меню" width="500">
            <div>
                <el-form label-width="auto">
                    <el-form-item label="Заголовок">
                        <el-input v-model="form.name"/>
                    </el-form-item>
                    <el-form-item label="Ссылка">
                        <el-input v-model="form.url"/>
                    </el-form-item>
                    <el-form-item label="SVG">
                        <el-input v-model="form.svg" clearable type="textarea" :rows="3"/>
                    </el-form-item>
                </el-form>
            </div>
            <template #footer>

                <div class="dialog-footer">
                    <el-button @click="editDialog = false">Отмена</el-button>
                    <el-button type="primary" @click="saveItem">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";
import {router} from "@inertiajs/vue3";
import {menuStore} from './store.js'
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    item: Object,
    index: Number
})
const isEdit = ref(false)
const form = reactive({
    name: props.item.name,
    url: props.item.url,
    svg: props.item.svg,
})
const emit = defineEmits(['update:item']);

const editDialog = ref(false);


function saveItem() {
    isEdit.value = false
    router.visit(route('admin.page.menu.set-item', {item: props.item.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            menuStore().beforeReload()
            emit('update:item', true)
            editDialog.value = false
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
            emit('update:item', true)
        }
    })
}

</script>
<style scoped>
span.svg-category::v-deep>svg {
    max-height: 40px;
}
</style>
