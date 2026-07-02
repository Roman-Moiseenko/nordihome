<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-tree"></i>
                <span> Подкатегории</span>
            </span>
        </template>
        <el-popover :visible="visible_create" placement="bottom-start" :width="246">
            <template #reference>
                <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                    Добавить комнату
                    <el-icon class="ml-1"><ArrowDown /></el-icon>
                </el-button>
            </template>
            <el-input v-model="form.name" placeholder="Название"/>
            <div class="mt-2">
                <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
            </div>
        </el-popover>
        <RoomChildren :room="room"  />
    </el-tab-pane>
    <DeleteEntityModal name_entity="Комнту" name="room"/>
</template>
<script setup lang="ts">
import {inject, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import RoomChildren from "@Comp/Room/Children.vue";

const props = defineProps({
    room: Object,
})
const visible_create = ref(false)

const form = reactive({
    name: null,
    parent_id: props.room.id,
})
function createButton() {
    router.post(route('admin.catalog.room.store', form))
    visible_create.value = false
}

</script>
