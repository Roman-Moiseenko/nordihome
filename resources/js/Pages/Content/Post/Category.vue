<template>
    <Head><title>{{ title }}</title></Head>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <InfoCategory :category="category" :templates="templates" />
    </div>

    <el-popover :visible="visible_create" placement="bottom-start" :width="246">
        <template #reference>
            <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                Добавить запись
                <el-icon class="ml-1"><ArrowDown /></el-icon>
            </el-button>
        </template>
        <el-input v-model="form.name" placeholder="Название"/>
        <div class="mt-2">
            <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
        </div>
    </el-popover>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <el-tabs>
            <PanelPosts :posts="category.posts" />
        </el-tabs>
    </div>
</template>

<script setup lang="ts">
import {ref, defineProps, reactive} from "vue";
import {Head, router, usePage} from "@inertiajs/vue3";
import {useStore} from '@Res/store.js'
import {ISelectItem} from '@Res/interface.d.ts'

import InfoCategory from "./Block/InfoCategory.vue";
import PanelPosts from "./Panels/AllPosts.vue"

const store = useStore();
const notSave = ref(false)
const props = defineProps({
    category: Object,
    templates: Array<ISelectItem>,
  //  post_templates: Array<ISelectItem>,
    title: {
        type: String,
        default: 'Рубрика',
    },

})
const visible_create = ref(false)
const form = reactive({
    name: null,
    categoryId: props.category.id,
})

function createButton() {
    router.visit(route('admin.content.post.store'), {
        method: "post",
        data:  form,
        onSuccess: page => {
            visible_create.value = false
        }
    })
}
</script>

<style scoped>

</style>
