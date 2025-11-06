<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Все записи</span>
            </span>
        </template>

        <el-table
            :data="tableData"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >

            <el-table-column prop="image" label="IMG" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column prop="name" label="Запись"  width="160"/>
            <el-table-column prop="published" label="Опубликована" width="140" align="center">
                <template #default="scope">
                    <Active :active="scope.row.published"/>
                    <br>
                    {{ func.date(scope.row.published_at) }}
                </template>
            </el-table-column>
            <el-table-column prop="title" label="Заголовок" />


            <el-table-column  width="180" align="right">
                <template #default="scope">
                    <el-button size="small"
                               :type="scope.row.published ? 'warning' : 'success'"
                               @click.stop="onToggle(scope.row)"
                    >
                        {{ scope.row.published ? 'Draft' : 'Published' }}
                    </el-button>
                    <el-button v-if="!scope.row.published" size="small"
                               type="danger"
                               @click.stop="handleDeleteEntity(scope.row)"
                    >
                        Delete
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

    </el-tab-pane>
    <DeleteEntityModal name_entity="Запись" name="post"/>
</template>

<script setup lang="ts">
import {defineProps, inject, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import {func} from "@Res/func.js"

const props = defineProps({
    posts: Array,
})
const tableData = ref([...props.posts]);
const $delete_entity = inject("$delete_entity", "post")
function routeClick(row) {
    router.get(route('admin.page.post.show', {post: row.id}))
}

function onToggle(row) {
    router.visit(route('admin.page.post.toggle', {post: row.id}), {
        method: "post",
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            console.log(page.props.category.posts)
            tableData.value = [...page.props.category.posts]
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.post.destroy', {post: row.id}), {name: 'post'});

}
</script>
