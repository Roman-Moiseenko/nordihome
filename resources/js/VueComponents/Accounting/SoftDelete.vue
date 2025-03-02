<template>
    <el-button type="success" class="ml-5" @click="onRestore">Восстановить</el-button>
    <el-button v-if="staff.is_chief" type="danger" class="ml-5" @click="onDelete">Удалить</el-button>


    <DeleteEntityModal name_entity="Документ" name="document"/>
</template>

<script lang="ts" setup>
import {ElLoading} from "element-plus";
import {router, usePage} from "@inertiajs/vue3";
import {inject} from "vue";

const props = defineProps({
    destroy: {
        required: true,
        type: String,
    },
    restore: {
        required: true,
        type: String,
    },
})
const staff = usePage().props.auth.user
const $delete_entity = inject("$delete_entity")

function onDelete() {
    $delete_entity.show(props.destroy, 'document');

}
function onRestore() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Идет восстановление',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(props.restore, {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            loading.close()
        },
        onFinish: page => {
            loading.close()
        },
    })
}

function onWork() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Отмена проведения',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(props.route, {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            loading.close()
        },
        onFinish: page => {
            loading.close()
        },
    })
}
</script>
