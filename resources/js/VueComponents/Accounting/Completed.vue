<template>
    <el-button type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
</template>

<script lang="ts" setup>
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    route: {
        required: true,
        type: String,
    }
})
function onCompleted() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Проведение документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(props.route, {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            loading.close()
        }
    })
}
</script>
