<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Комната {{ room.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <RoomInfo :room="room" />
        </div>
        <el-tabs>
            <PanelChildren :room="room" />
            <PanelProducts :room-id="room.id" />
        </el-tabs>

        <!-- ContentBlock Editor -->
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <ContentBlockEditor
                :blocks="blocks || []"
                :container-id="room.id"
                container-type="category"
            />
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, ref, defineProps, reactive} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import RoomInfo from "./Block/Info.vue";
import PanelChildren from "./Panels/Children.vue";
import PanelProducts from "./Panels/Products.vue";
import ContentBlockEditor from "@Comp/Content/ContentBlock/ContentBlockEditor.vue";


const props = defineProps({
    room: Object,
    title: {
        type: String,
        default: 'Карточка Комнаты',
    },
    blocks: Array,
})
</script>

<style scoped>

</style>
