<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Комната {{ room.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <RoomInfo :room="room" />
        </div>
        <el-tabs>

        </el-tabs>
    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, ref, defineProps, reactive} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import RoomInfo from "./Block/Info.vue";




const props = defineProps({
    room: Object,
    rooms: Array,
    title: {
        type: String,
        default: 'Карточка категории',
    },
})

</script>

<style scoped>

</style>
