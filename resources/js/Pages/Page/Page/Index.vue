<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Страницы</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onOpenDialog" ref="buttonRef">
                Добавить страницу
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Страница" width="280" show-overflow-tooltip/>
                <el-table-column prop="title" label="Заголовок" width="" />
                <el-table-column prop="template" label="Шаблон" width="120" show-overflow-tooltip/>
                <el-table-column prop="menu" label="Меню" width="100" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.menu" />
                    </template>
                </el-table-column>
                <el-table-column prop="published" label="Опубликована" width="130" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        Up, Down, Draft/Active
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

        <DeleteEntityModal name_entity="Страницу"/>


        <el-dialog v-model="dialogCreate" title="Страница" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder="Подпись, ALT"/>
                </el-form-item>
                <el-form-item label="Класс иконки" label-position="top" class="mt-3">
                    <el-input v-model="form.icon" placeholder="fontawesome 6.0"/>
                </el-form-item>


                <el-form-item label="Ссылка на контакт" label-position="top" class="mt-3">
                    <el-input v-model="form.url" placeholder="https://"/>
                </el-form-item>
                <el-form-item label="Тип для аналитики" label-position="top" class="mt-3">
                    <el-input-number v-model="form.type" min="0"/>
                </el-form-item>

            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveContact">Сохранить</el-button>
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


const props = defineProps({
    pages: Array,
    title: {
        type: String,
        default: 'Сайт. Страницы',
    },
})

const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.pages])


const form = reactive({
    id: null,
    name: null,
    icon: null,
    //TODO
})

function onOpenDialog() {
    form.id = null
    form.name = null

    dialogCreate.value = true
}

function handleGetProduct(val) {
  /*  form.product_id = val

    const getAttributes = route('admin.product.attr-modification', {product: form.product_id});

    axios.post(getAttributes).then(response => {
        console.log(response.data)
        if (response.data.error !== undefined) console.log(response.data.error)
        attributes.value = response.data
        placeholder_name.value = 'Введите название'
        placeholder_attr.value = 'Выберите 1-2 атрибута'
        document.getElementById('name-modif').focus()
    });*/
}

function saveContact() {
    if (form.id === null) {
        router.post(route('admin.page.page.store' ), form)
    } else {
        router.put(route('admin.page.page.update', {page: form.id}) , form)
    }
}

function routeClick(row) {
    //TODO Открыть модальное окно
    form.id = row.id
    dialogCreate.value = true
    //TODO Открыть модальное окно
    //router.get(route('admin.product.modification.show', {modification: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.page.destroy', {page: row.id}));
}
</script>
