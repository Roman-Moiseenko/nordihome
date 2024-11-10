<template>
    <el-breadcrumb :separator-icon="ArrowRight">
        <template v-for="item in $page.props.breadcrumbs">
        <el-breadcrumb-item :to="{ path: item.url }">
            <Link :href="item.url" class="flex items-center text-white hover:text-teal-600 ">
            {{ item.title}}
            </Link>
        </el-breadcrumb-item>
        </template>
    </el-breadcrumb>

</template>

<script lang="ts" setup>
import { ArrowRight } from '@element-plus/icons-vue'
import { Link } from '@inertiajs/vue3'
</script>


<style>
.el-breadcrumb__inner a, .el-breadcrumb__inner.is-link {
    --el-text-color-primary: var(--el-color-white);
}
.el-breadcrumb__separator {
    --el-text-color-placeholder: var(--el-color-white);
}
.el-breadcrumb__item:last-child .el-breadcrumb__inner {
    --el-text-color-regular: rgb(161 161 170);

}
.el-breadcrumb__inner a:hover {
    --el-color-primary: rgb(161 161 170);
}
</style>
