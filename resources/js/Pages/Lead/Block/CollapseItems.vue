<template>
    <el-collapse :name="'items' + lead.id">
        <el-collapse-item name="1" class="items-lead">
            <template #title >
                    <span v-if="lead.comment">
                            {{ lead.comment }} <el-tag v-if="lead.finished_at" type="danger" effect="dark">{{ func.date(lead.finished_at) }}</el-tag>
                        </span>
                <span v-else><el-tag type="info" effect="plain">Нет комментариев</el-tag></span>
            </template>
            <div v-for="item in lead.items" class="border border-1 border-dotted p-1">
                <el-tag type="info" >{{ (item.created_at) }}</el-tag> <br>
                {{ item.comment }}
                <el-tag v-if="item.finished_at" type="danger" effect="light">{{ func.date(item.finished_at) }}</el-tag>
            </div>
            <el-tooltip v-if="lead.staff_id != null" effect="dark" content="Добавить комментарий">
                <el-button  type="warning" @click="onAddItem"><i class="fa-light fa-comment"></i></el-button>
            </el-tooltip>
        </el-collapse-item>
    </el-collapse>
</template>

<script setup lang="ts">
import {func} from "@Res/func.js"

const props = defineProps({
    lead: Object,
    })
const $emit = defineEmits(['add:item'])

function onAddItem() {
    $emit('add:item', props.lead.id)
}
</script>

<style scoped>
.el-collapse {
    --el-collapse-header-height: 24px;
}
</style>
