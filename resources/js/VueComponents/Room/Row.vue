<template>
    <div class="bg-white rounded-md flex items-center mb-1 p-2 border border-slate-200">
        <div class="w-11" style="height: 40px;">
            <img v-if="room.image_url" :src="room.image_url" style="width: 40px; height: 40px;">
        </div>
        <div class="w-11 ml-2">
            <img v-if="room.icon_url" :src="room.icon_url" style="width: 40px; height: 40px;">
        </div>
        <div class="ml-4" style="width: 350px;">
            <Link type="primary" :href="route('admin.catalog.room.show', {room: room.id})">{{ room.name }}</Link>
        </div>
        <div class="ml-4" style="width: 350px;">
            <span class="text-cyan-800">/room/{{ room.slug }}</span>
        </div>
        <div class="ml-5 text-center" style="width: 150px;">
            <span v-if="isChildren">
                {{ room.children.length }}
                <el-button type="info" size="small" class="ml-2" plain @click="checkChildren = !checkChildren">
                    <i v-if="checkChildren" class="fa-regular fa-chevron-up"></i>
                    <i v-else class="fa-regular fa-chevron-down"></i>
                </el-button>
            </span>
        </div>
        <div class="flex ml-5">
            <el-button size="small"
                       type="primary"
                       @click.stop="onUp()"
            >
                <i class="fa-light fa-arrow-up"></i>
            </el-button>
            <el-button size="small"
                       type="primary"
                       @click.stop="onDown()"
            >
                <i class="fa-light fa-arrow-down"></i>
            </el-button>
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                        <el-button size="small"
                                   type="success"
                                   @click="visible_create = !visible_create" ref="buttonRef"
                        >
                            <i class="fa-light fa-folder-plus"></i>
                        </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Дочерняя категория"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="handleChild" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <el-button size="small"
                       type="danger"
                       @click.stop="handleDeleteEntity"
            >
                Delete
            </el-button>
        </div>
    </div>
    <div v-if="showChildren" class="pl-5 ml-2 mb-5 pb-2 pt-2">
        <RoomChildren :room="room" />
    </div>

</template>

<script setup lang="ts">
import {router, Link} from "@inertiajs/vue3";
import {computed, inject, reactive, ref} from "vue";
import RoomChildren from "@Comp/Room/Children.vue";

const props = defineProps({
    room: Object,
})
const $emit = defineEmits(['delete:room'])
const visible_create = ref(false)
const form = reactive({
    name: null,
    parent_id: props.room.id,
})
const checkChildren = ref(false)
const isChildren = ref(props.room.children.length > 0)
const $delete_entity = inject("$delete_entity")

const showChildren = computed(() => {
    return isChildren && checkChildren.value
})

function onUp() {
    router.visit(route('admin.catalog.room.up', {room: props.room.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onDown() {
    router.visit(route('admin.catalog.room.down', {room: props.room.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function handleDeleteEntity() {
    $delete_entity.show(route('admin.catalog.room.destroy', {room: props.room.id}), {name: 'room'});

}
function handleChild() {
    //console.log(form)
    router.post(route('admin.catalog.room.store', form))
}

</script>
