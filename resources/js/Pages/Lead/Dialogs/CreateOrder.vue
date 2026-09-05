<template>
    <el-dialog v-model="dialogOrder" title="Создать заказ" width="400">
        <div class="flex justify-center mb-4 mt-2">
            Создать
            <el-tag type="danger" class="mx-2">Новый заказ</el-tag>
            ?
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="info" class="" @click="dialogOrder = false">
                    Отмена
                </el-button>
                <el-button type="primary" class="" @click="onCreateOrder">
                    Создать
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";

const dialogOrder = ref(false)
const formOrder = reactive({
    lead: null,
})

function open(val) {
    formOrder.lead = val
    dialogOrder.value = true
}

function onCreateOrder() {
    router.visit(route('admin.lead.create-order', {id: formOrder.lead}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogOrder.value = false
        }
    })
}

defineExpose({open})
</script>
