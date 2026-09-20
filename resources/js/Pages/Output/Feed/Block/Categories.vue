<template>
    <Card title="Категории">
        <el-row>
            <el-col :span="12">
                <el-tree
                    class="!bg-green-50"
                    style="max-width: 600px"
                    :data="useCatalog.categoriesTree"
                    show-checkbox
                    check-strictly
                    node-key="id"
                    :props="defaultProps"
                    :default-checked-keys="[...feed.categoriesIn]"
                    @check="(node, info) => onCheck(info, true)"
                />
            </el-col>
            <el-col :span="12">
                <el-tree
                    class="!bg-red-50"
                    style="max-width: 600px"
                    :data="useCatalog.categoriesTree"
                    show-checkbox
                    check-strictly
                    node-key="id"
                    :props="defaultProps"
                    :default-checked-keys="[...feed.categoriesOut]"
                    @check="(node, info) => onCheck(info, false)"
                />
            </el-col>
        </el-row>
    </Card>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import Card from "./Card.vue";
import {useCatalogStore} from "@Res/catalogStore";

const useCatalog = useCatalogStore()
const props = defineProps({
    feed: {type: Object, required: true},
    save: {type: Function, required: true},
})
const defaultProps = {
    children: 'children',
    label: 'name',
}

function onCheck(info, _in: boolean) {
    const prev = _in ? props.feed.categoriesIn : props.feed.categoriesOut
    const next = info.checkedKeys
    const added = next.filter((id) => !prev.includes(id))
    const removed = prev.filter((id) => !next.includes(id))

    if (added.length) props.save('categories', 'add', _in, added)
    if (removed.length) props.save('categories', 'remove', _in, removed)
}
</script>
