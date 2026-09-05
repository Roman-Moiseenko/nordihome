<template>
    <el-dialog v-model="dialogItem" title="Добавить Комментарий" width="400">
        <el-form label-width="auto">
            <el-form-item label="Комментарий">
                <el-input v-model="formItem.comment" type="textarea" :rows="4"/>
            </el-form-item>
            <el-form-item label="Дата ограничения">
                <el-date-picker v-model="formItem.finished_at" type="date"/>
            </el-form-item>

        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="info" class="" @click="dialogItem = false">
                    Отмена
                </el-button>
                <el-button type="primary" class="" @click="onAddItem">
                    Создать
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"

const dialogItem = ref(false)
const formItem = reactive({
    lead: null,
    type: null,
    finishedAt: null,
    comment: null,
})

function open(val) {
    formItem.lead = val
    formItem.comment = null
    formItem.finishedAt = null
    formItem.type = null
    dialogItem.value = true
}

function onAddItem() {
    if (formItem.finishedAt !== null) formItem.finishedAt = func.date(formItem.finishedAt)
    router.visit(route('admin.lead.add-comment', {id: formItem.lead}), {
        method: "post",
        data: formItem,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogItem.value = false
        }
    })
}

defineExpose({open})
</script>
