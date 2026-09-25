<template>
    <Head><title>{{ title }}</title></Head>
    <div class="flex">
        <h1 class="font-medium text-xl">Рубрика {{ category.name }}</h1>
        <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
            <el-button circle class="ml-2" @click="showHelp = !showHelp">
                <i class="fa-light fa-lightbulb-on text-orange-500"></i>
            </el-button>
        </el-tooltip>
    </div>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <InfoCategory :category="category" :templates="templates" />

        <HelpBlock v-if="showHelp">
            <p><b>Название рубрики</b> является обязательным полем.</p>
            <p><b>Шаблон записи</b> - шаблон по-умолчанию, для каждой записи можно выбрать свой шаблон.</p>
            <p>Поле <b>Slug</b> (ссылка на рубрику) можно не заполнять, тогда оно заполнится автоматически. При
                заполнении использовать латинский алфавит.</p>
            <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
            <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg.
                Разрешение не более 200х200.</p>
            <p>Поля <b>Meta</b> используются в SEO. Для заполнения обязательны.</p>
        </HelpBlock>
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
import HelpBlock from "@Comp/HelpBlock.vue";
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
const showHelp = ref(false);
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
