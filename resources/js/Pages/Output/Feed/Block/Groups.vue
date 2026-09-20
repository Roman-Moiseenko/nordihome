<template>
    <Card title="Группы">
        <el-row>
            <el-col :span="12" class="bg-green-50 p-2">
                <div v-for="item in useCatalog.groups" :key="item.id" class="py-1">
                    <el-checkbox
                        :model-value="feed.groupsIn.includes(item.id)"
                        @change="(checked) => toggle(item.id, checked, true)"
                    >{{ item.name }}</el-checkbox>
                </div>
            </el-col>
            <el-col :span="12" class="bg-red-50 p-2">
                <div v-for="item in useCatalog.groups" :key="item.id" class="py-1">
                    <el-checkbox
                        :model-value="feed.groupsOut.includes(item.id)"
                        @change="(checked) => toggle(item.id, checked, false)"
                    >{{ item.name }}</el-checkbox>
                </div>
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

function toggle(id: number, checked: boolean, _in: boolean) {
    props.save('groups', checked ? 'add' : 'remove', _in, [id])
}
</script>
