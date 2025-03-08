<template>
    <el-button
        type="success"
        :size="small ? 'small' : 'default'"
        :class="classBtn"
        @click.stop="onRestore">{{ textRestore }}</el-button>
    <el-button
        v-if="staff.is_chief" type="danger"
        :size="small ? 'small' : 'default'"
        :class="classBtn"
        @click.stop="onDelete">{{ textDelete}}</el-button>
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
    small: {
        default: false,
        type: Boolean,
    }
})

const textDelete = props.small ? 'Delete' : 'Удалить'
const textRestore = props.small ? 'Restore' : 'Восстановить'
const classBtn = props.small ? '' : 'mr-3'
const staff = usePage().props.auth.user

const $emit = defineEmits(['restore', 'destroy'])

function onDelete() {
    $emit('destroy', true)
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
        preserveState: false,
        onSuccess: page => {
            loading.close()
            $emit('restore', true)
        },
        onFinish: page => {
            loading.close()
        },
    })
}

</script>
