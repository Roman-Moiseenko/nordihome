<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Комнаты</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать комнату
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Название"/>
                <el-select v-model="form.parentId" placeholder="Родительская комната" class="mt-1" filterable clearable>
                    <el-option v-for="item in rooms" :value="item.id" :label="item.name" />
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>
        <RoomChildren :categories="rooms" />
        <DeleteEntityModal name_entity="Комнату" name="room"/>
    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'

import RoomChildren from "@Comp/Room/Children.vue";

const props = defineProps({
    rooms: Object,
    title: {
        type: String,
        default: 'Комнаты',
    },
})

const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
console.log(props.rooms)

const form = reactive({
    name: null,
    parentId: null,
})
function createButton() {
    router.post(route('admin.catalog.room.store', form))
}

function routeClick(row) {
    router.get(route('admin.catalog.room.show', {room: row.id}))
}
</script>
