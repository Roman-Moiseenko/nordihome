<template>
    <el-dropdown class="ml-3">
        <el-button type="success" plain>
            Связанные документы<el-icon class="el-icon--right"><arrow-down /></el-icon>
        </el-button>
        <template #dropdown>
            <el-dropdown-menu>
                <el-dropdown-item>Сделать дерево всех документов</el-dropdown-item>
            </el-dropdown-menu>
        </template>
    </el-dropdown>
</template>

<script lang="ts" setup>
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    based: Array,
})
console.log('onBase', props.based)
</script>
