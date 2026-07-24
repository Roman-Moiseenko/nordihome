<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Метки товаров</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="openCreateDialog">
                Создать метку
            </el-button>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="classes.TableCompleted"
                v-loading="store.getLoading"
            >
                <el-table-column prop="name" label="Название Метки" />
                <el-table-column prop="slug" label="Ссылка"/>
                <el-table-column prop="isMain" label="Главная">
                    <template #default="scope">
                        <Active :active="scope.row.isMain" />
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Кол-во товаров" />
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            type="primary"
                            @click.stop="Show(scope.row.id)"
                        >
                            Show
                        </el-button>
                        <el-button
                            size="small"
                            type="success"
                            @click.stop="openEditDialog(scope.row)"
                        >
                            Edit
                        </el-button>
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

        <pagination
            :current_page="tags.current_page"
            :per_page="tags.per_page"
            :total="tags.total"
        />

        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название метки" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder="Название метки"/>
                </el-form-item>
                <el-form-item label="Ссылка (slug)" label-position="top" class="mt-3">
                    <el-input v-model="form.slug" placeholder="slug-metki"/>
                </el-form-item>
                <el-form-item label-position="top" class="mt-3">
                    <el-checkbox v-model="form.isMain">Главная метка</el-checkbox>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogVisible = false">Отмена</el-button>
                    <el-button type="primary" @click="saveTag">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>

    </el-config-provider>
    <DeleteEntityModal name_entity="Метку" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import ru from 'element-plus/dist/locale/ru.mjs'
import {classes} from "@Res/className"
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    tags: Object,
    title: {
        type: String,
        default: 'Метки товаров',
    },
    filters: Array,
})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.tags.data])

const dialogVisible = ref(false)
const dialogTitle = ref('Создать метку')
const form = reactive({
    id: null,
    name: '',
    slug: '',
    isMain: false,
})

function openCreateDialog() {
    dialogTitle.value = 'Создать метку'
    form.id = null
    form.name = ''
    form.slug = ''
    form.isMain = false
    dialogVisible.value = true
}

function openEditDialog(row) {
    dialogTitle.value = 'Редактировать метку'
    form.id = row.id
    form.name = row.name
    form.slug = row.slug
    form.isMain = Boolean(row.isMain)
    dialogVisible.value = true
}

function saveTag() {
    const data = {
        name: form.name,
        slug: form.slug,
        isMain: form.isMain,
    }
    let url
    if (form.id) {
        url = route('admin.catalog.tag.update', {id: form.id})
    } else {
        url = route('admin.catalog.tag.store')
    }
    router.visit(url, {
        method: "post",
        data: data,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogVisible.value = false
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.catalog.tag.destroy', {id: row.id}));
}

function Show(id) {
    router.get(route('admin.catalog.tag.show', {id: id}));
}
</script>
