<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Галерея</h1>
        <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true">Добавить Галерею</el-button>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column sortable prop="name" label="Название" width="240" />
                <el-table-column prop="slug" label="Ссылка" width="240"/>
                <el-table-column prop="count" label="Изображений" align="center" width="150"/>
                <el-table-column prop="description" label="Описание" />
                <!-- Повторить -->
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
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

    </el-config-provider>
    <DeleteEntityModal name_entity="галерею" />

    <el-dialog v-model="dialogCreate" title="Создать галерею" width="500">
        <el-form label-width="auto">
            <el-form-item label="Название" :rules="{required: true}">
                <el-input v-model="form.name" placeholder="Название"/>
                <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
            </el-form-item>

            <el-form-item label="Ссылка">
                <el-input v-model="form.slug" placeholder="Оставьте пустым для заполнения" :formatter="(val) => func.MaskSlug(val)"/>
                <div v-if="errors.slug" class="text-red-700">{{ errors.slug }}</div>
            </el-form-item>

            <el-form-item label="Описание">
                <el-input v-model="form.description" placeholder="Описание" :rows="5" type="textarea" maxlength="250" show-word-limit/>
                <div v-if="errors.description" class="text-red-700">{{ errors.description }}</div>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogCreate = false">Отмена</el-button>
                <el-button type="primary" @click="onCreate">Сохранить</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";
import { func } from "@Res/func.js"
const props = defineProps({
    galleries: Array,
    errors: Object,
    title: {
        type: String,
        default: 'Сайт. Галерея',
    },

})
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.galleries])

const form = reactive({
    name: null,
    slug: null,
    description: null,
})

function onCreate() {
    router.post(route('admin.page.gallery.store'), form)

}
function routeClick(row) {
    router.get(route('admin.page.gallery.show', {gallery: row.id}))
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.gallery.destroy', {gallery: row.id}));
}

</script>
