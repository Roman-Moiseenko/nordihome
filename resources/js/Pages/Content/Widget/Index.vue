<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import {defineProps, inject, reactive, ref} from "vue";
import {useContentStore} from "@Res/contentStore";

const props = defineProps({
    widgets: Array,
    title: {
        type: String,
        default: 'Виджеты',
    },
})
const contentStore = useContentStore()
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.widgets])
const form = reactive({
    name: null,
    slug: null,
    category: null,
})

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.content.widget.destroy', {id: row.id}));
}
function saveWidget() {
    router.post(route('admin.content.widget.store' ), form)
}

function routeClick(row) {
    router.get(route('admin.content.widget.show', {id: row.id}))
}
</script>

<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Виджет</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить виджет
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Название" width="200" show-overflow-tooltip/>
                <el-table-column prop="is_container" label="Контейнер" width="120" >
                    <template #default="scope">
                        <Active :active="scope.row.is_container" />
                    </template>
                </el-table-column>
                <el-table-column prop="slug" label="Ссылка на шаблон" width="120"/>
                <el-table-column prop="category" label="Категория" width="180" />
                <el-table-column prop="description" label="Описание" width="280" show-overflow-tooltip/>
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

        <DeleteEntityModal name_entity="Виджет"/>
        <el-dialog v-model="dialogCreate" title="Виджет" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name"/>
                </el-form-item>
                <el-form-item label="Ссылка на шаблон" label-position="top" class="mt-3">
                    <el-input v-model="form.slug"/>
                </el-form-item>
                <el-form-item label="Категория" label-position="top" class="mt-3">
                    <el-select v-model="form.category" >
                        <el-option v-for="(item, index) in contentStore.categories" :value="index" :label="item" />
                    </el-select>
                </el-form-item>

            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveWidget">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>

</template>

<style scoped>

</style>
