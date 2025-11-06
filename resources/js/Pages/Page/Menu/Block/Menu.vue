<template>
    <el-tag size="large" effect="dark" type="warning">{{ menu.name }}</el-tag>
    <el-tag>{{ menu.slug }}</el-tag>
    <el-button type="success" size="small" class="ml-4" @click="editDialog = true">Edit</el-button>
    <el-button type="danger" size="small" class="ml-4" @click="handleDeleteEntity">Delete</el-button>
    <div>
        <el-row :gutter="10">
            <el-col :span="6">
                <div :id="'menu-items-' + index" style="cursor: pointer; ">
                    <div v-for="(item, index) in items" class="border p-1">
                        <MenuItem :item="item" :index="index" @del:item="reloadItems"/>
                    </div>
                </div>
            </el-col>
            <el-col :span="18">
                <MenuActions :id="menu.id" @add:item="reloadItems"/>
            </el-col>
        </el-row>
    </div>

    <DeleteEntityModal name_entity="Меню"/>

    <el-dialog v-model="editDialog" :title="'Меню \'' + menu.name + '\''" width="300">
        <el-form label-width="auto">
            <el-form-item label="Название" label-position="top" class="mt-3">
                <el-input v-model="form.name" placeholder=""/>
            </el-form-item>
            <el-form-item label="Ссылка" label-position="top" class="mt-3">
                <el-input v-model="form.slug" placeholder="Заполнится автоматически" clearable/>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="editDialog = false">Отмена</el-button>
                <el-button type="primary" @click="saveMenu">Сохранить</el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script setup lang="ts">
import Sortable from 'sortablejs';
import {defineProps, inject, onMounted, reactive, ref} from "vue";
import {route} from "ziggy-js";
import MenuItem from "./MenuItem.vue";
import {router} from "@inertiajs/vue3";
import MenuActions from "./MenuActions.vue";
import axios from "axios";

const props = defineProps({
    menu: Object,
    index: Number
})

const items = ref([...props.menu.items])

const editDialog = ref(false)
const $delete_entity = inject("$delete_entity")
const form = reactive({
    name: props.menu.name,
    slug: props.menu.slug,
})
onMounted(() => {
    initDragSort()
});
const itemList = ref(props.menu.items);

function saveMenu() {
    router.visit(route('admin.page.menu.set-info', {menu: props.menu.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            editDialog.value = false;
            //menu = {... page.props.menu}
        }
    })
}

function handleDeleteEntity() {
    $delete_entity.show(route('admin.page.menu.destroy', {menu: props.menu.id}));
}

function initDragSort() {
    const el = document.getElementById('menu-items-' + props.index);
    Sortable.create(el, {
        onEnd: ({oldIndex, newIndex}) => {
            const page = itemList.value[oldIndex];
            itemList.value.splice(oldIndex, 1);
            itemList.value.splice(newIndex, 0, page);
            let new_sort = itemList.value.map(item => item.id)

            router.visit(route('admin.page.menu.move-item', {menu: props.menu.id}), {
                method: "post",
                data: {new_sort: new_sort},
                preserveScroll: true,
                preserveState: false,
                onSuccess: page => {
                //    items.value = [...page.props.menus[0].items]
                }
            })
        }
    });
}

function reloadItems() {
    axios.post(route('admin.page.menu.get-items', {menu: props.menu.id}))
        .then(response => {
            items.value = [...response.data.items]
        })
}

</script>


<style scoped>

</style>
