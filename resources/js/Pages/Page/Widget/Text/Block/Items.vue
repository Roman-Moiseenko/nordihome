<template>
    <el-table :data="[...items]"
              header-cell-class-name="nordihome-header"
              style="width: 100%;">
        <el-table-column type="expand">
            <template #default="scope">
                <el-tag effect="dark" type="info">Текстовый блок</el-tag>
                <div v-html="scope.row.text"></div>
            </template>
        </el-table-column>
        <el-table-column prop="slug" label="Slug" width="200"/>
        <el-table-column prop="caption" label="Заголовок" width="220" />
        <el-table-column prop="description" label="Описание"/>
        <el-table-column label="Действия" align="right" width="200">

            <template #default="scope">
                <el-button size="small" type="primary" dark @click="onUp(scope.row)">
                    <i class="fa-light fa-chevron-up"></i>
                </el-button>
                <el-button size="small" type="primary" dark @click="onDown(scope.row)">
                    <i class="fa-light fa-chevron-down"></i>
                </el-button>
                <el-button size="small" type="success" @click="handleEdit(scope.row)" plain>
                    <el-icon>
                        <Edit/>
                    </el-icon>
                </el-button>
                <el-button size="small" type="danger" @click="handleDeleteEntity(scope.row)" plain>
                    <el-icon>
                        <Delete/>
                    </el-icon>
                </el-button>
            </template>
        </el-table-column>

    </el-table>
    <DeleteEntityModal name_entity="Элемент из баннера"/>

    <el-dialog v-model="ShowEditor"
               :before-close="handleClose"
               title="Изменить текстовый блок" width="1200">
        <el-form>

            <el-form-item label="Slug">
                <el-input v-model="form.slug" autocomplete="off"/>
            </el-form-item>
            <el-form-item label="Заголовок">
                <el-input v-model="form.caption" autocomplete="off"/>
            </el-form-item>
            <el-form-item :rules="{required: true}" label="Описание">
                <el-input v-model="form.description" autocomplete="off"/>
            </el-form-item>
            <editor
                :api-key="tiny_api" v-model="form.text"
                :init="store.tiny"
            />
        </el-form>

        <template #footer>
            <div class="dialog-footer">
                <el-button @click="ShowEditor = false">Отмена</el-button>
                <el-button type="primary" @click="saveTextItem">
                    Сохранить
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import EditField from "@Comp/Elements/EditField.vue";
import {inject, reactive} from "vue";
import {router} from "@inertiajs/vue3";
import Editor from "@tinymce/tinymce-vue";
import {useStore} from '@Res/store.js'
import { ElMessageBox } from 'element-plus'

const store = useStore();


const props = defineProps({
    items: Array,
    tiny_api: String,
})
const form = reactive({
    id: null,
    slug: null,
    caption: null,
    description: null,
    text: null,
})
const $delete_entity = inject("$delete_entity")
const ShowEditor = ref(false);
const handleClose = (done: () => void) => {
    ElMessageBox.confirm('Закрыть окно? Измененые данные не сохраняться')
        .then(() => {
            done()
        })
        .catch(() => {
            // catch error
        })
}
function handleEdit(row) {
    form.id = row.id
    form.slug = row.slug
    form.caption = row.caption
    form.description = row.description
    form.text = row.text
    ShowEditor.value = true
}

function saveTextItem() {
    router.visit(route('admin.page.widget.text.set-item', {item: form.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function onUp(row) {
    router.visit(route('admin.page.widget.text.up-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function onDown(row) {
    router.visit(route('admin.page.widget.text.down-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}



function setItem(row) {
    router.visit(route('admin.page.widget.text.set-item', {item: row.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.widget.text.del-item', {item: row.id}));
}

function saveText(id, val) {
    // console.log(id, val)
}
</script>
