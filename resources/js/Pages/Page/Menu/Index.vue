<template>
    <Head><title>{{ title }}</title></Head>
    <el-popover :visible="visible_create" placement="bottom-start" :width="246">
        <template #reference>
            <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                Добавить меню
                <el-icon class="ml-1"><ArrowDown /></el-icon>
            </el-button>
        </template>
        <el-input v-model="form.name" placeholder="Название"/>
        <el-input v-model="form.slug" placeholder="Slug" class="mt-2"/>

        <div class="mt-2">
            <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
        </div>
    </el-popover>
    <div v-for="(menu, index) in page_menus" class="mt-2 p-5 bg-white rounded-md">
        <Menu :menu="menu" :index="index"/>
    </div>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps, reactive, ref} from "vue";
import Menu from "./Block/Menu.vue";

const props = defineProps({
    page_menus: Array,
    title: {
        type: String,
        default: 'Сайт. Меню',
    },
})
const visible_create = ref(false)
const form = reactive({
    name: null,
    slug: null,
})

function createButton() {
    router.visit(route('admin.page.menu.store'), {
        method: "post",
        data:  form,
        onSuccess: page => {
            visible_create.value = false
        }
    })
}

</script>
