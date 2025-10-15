<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Новости</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="createNews" ref="buttonRef">
                Добавить новость
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="updateNews"
            >
                <el-table-column prop="title" label="Заголовок" width="280" show-overflow-tooltip/>
                <el-table-column prop="published" label="Опубликована" width="130" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published"/>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">

                        <el-button size="small"
                                   :type="scope.row.published ? 'warning' : 'success'"
                                   @click.stop="onToggle(scope.row)"
                        >
                            {{ scope.row.published ? 'Draft' : 'Published' }}
                        </el-button>
                        <el-button v-if="!scope.row.published"
                                   size="small"
                                   type="danger"
                                   @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="news.current_page"
            :per_page="news.per_page"
            :total="news.total"
        />

        <DeleteEntityModal name_entity="Новость"/>

        <el-dialog v-model="dialogCreate" title="Новость" width="800">
            <el-form label-width="auto">
                <el-row>
                    <el-col :span="18" class="align-items-center !grid">
                        <el-form-item label="Заголовок новости" label-position="top" class="mr-3 align-content-center">
                            <el-input v-model="form.title" placeholder="Заголовок новости"/>
                        </el-form-item>

                    </el-col>
                    <el-col :span="6">
                        <UploadImageFile
                            label="Изображение"
                            v-model:image="form.image"
                            @selectImageFile="onAddItem"
                        />
                    </el-col>
                </el-row>
                <el-form-item label="Текст новости" label-position="top" class="mt-3">
                    <editor
                        :api-key="tiny_api" v-model="form.text"
                        :init="store.tiny"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="savePage">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>

    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";

import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";

import {route} from "ziggy-js";
import axios from "axios";
import Editor from "@tinymce/tinymce-vue";
import Pagination from "@Comp/Pagination.vue";
import UploadImageFile from "@Comp/UploadImageFile.vue";
import {IRowId, IUploadFile} from "@Res/interface"

const store = useStore();
const props = defineProps({
    news: Array,
    tiny_api: String,
    title: {
        type: String,
        default: 'Сайт. Новости',
    },
})
console.log(props.news)
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.news.data])
const form = reactive({
    id: null,
    title: null,
    text: null,

    published_at: null,

    image: null,
    clear_file: false,
    file: null,
})

function savePage() {

    const route_path = form.id === null
        ? route('admin.page.news.store')
        : route('admin.page.news.update', {news: form.id})
    router.visit(route_path, {
        method: form.id === null ? "post" : "put",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogCreate.value = false;
        }
    })
}

function onToggle(row : IRowId) {
    router.visit(route('admin.page.news.toggle', {news: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function createNews(){
    form.id = null;
    form.title = null
    form.published_at = null
    form.image = null
    dialogCreate.value = true;
}

function updateNews(row : Object) {
    form.id = row.id;
    form.title = row.title
    form.published_at = row.published_at
    form.image = row.image
    dialogCreate.value = true;
  //  router.get(route('admin.page.news.show', {news: row.id}))
}

function onAddItem(val : IUploadFile) {
    form.clear_file = val.clear_file;
    form.file = val.file

}

function handleDeleteEntity(row : IRowId) {
    $delete_entity.show(route('admin.page.news.destroy', {news: row.id}));
}
</script>
