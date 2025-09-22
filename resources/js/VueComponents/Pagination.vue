<template>
    <div class="mt-3">
        <el-pagination
            class=""
            layout="total, sizes, prev, pager, next, jumper"
            :page-sizes="[20, 100, 200, 500]"
            :page-size="PageSize"
            :current-page="CurrentPage"
            :total="Total"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
        >
        </el-pagination>
    </div>
</template>

<script lang="ts" setup>
import {router, usePage} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import {defineProps, ref} from "vue";

const props = defineProps({
    current_page: Number,
    per_page: Number,
    total: Number,
    loading: Boolean,
})

const PageSize = ref(props.per_page)
const CurrentPage = ref(props.current_page)
const Total = ref(props.total)

function handleSizeChange(val) {
    PageSize.value = val;
    router.visit(usePage().url,
        {
            method: 'get',
            data: {page: CurrentPage.value, size: val},
            preserveScroll: true,
            preserveState: true,
            onBefore: visit => {
                useStore().beforeLoad();
            },
            onFinish: visit => {
                useStore().afterLoad();
            },
        }
    );
}
function handleCurrentChange(val) {
    CurrentPage.value = val;
    router.visit(usePage().url,
        {
            method: 'get',
            data: {page: val, size: PageSize.value},
            preserveScroll: true,
            preserveState: false,
            onBefore: visit => {
                useStore().beforeLoad();
            },
            onFinish: visit => {
                useStore().afterLoad();
            },
        }
    );
}
</script>

