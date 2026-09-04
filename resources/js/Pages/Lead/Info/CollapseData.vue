<template>
    <el-collapse :name="'data' + lead.id">
        <el-collapse-item title="Данные запроса" name="1">
            <div v-for="field in lead.data">
                <template v-if="field.slug != 'id'">
                    <el-tag type="info" effect="dark">{{ field.name }}</el-tag>
                    <el-tag type="info">{{ field.value }}</el-tag>
                </template>
            </div>

        </el-collapse-item>
    </el-collapse>
</template>

<script setup lang="ts">
const props = defineProps({
    lead: Object,
})
</script>
<style scoped>
.el-collapse {
    --el-collapse-header-height: 36px;
}
</style>
