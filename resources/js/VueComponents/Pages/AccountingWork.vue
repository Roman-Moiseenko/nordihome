<template>
    <el-button type="danger" class="ml-5" @click="onWork">Отмена проведения</el-button>
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
function onWork() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Отмена проведения',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(props.route, {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            loading.close()
        }
    })
}
</script>
