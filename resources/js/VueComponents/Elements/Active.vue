<template>
    <span v-if="active">
        <el-icon class="active-icon"><CircleCheck /></el-icon>
    </span>
    <span v-else>
        <el-icon class="draft-icon"><CircleClose /></el-icon>
    </span>

</template>

<script>
export default {
    props: {
        active: {
            type: Boolean,
            default: false,
        }
    }
}
</script>

<style scoped>
.el-icon.active-icon {
    --color: var(--el-color-success);
}

.el-icon.draft-icon {
    --color: var(--el-color-danger);
}
</style>
