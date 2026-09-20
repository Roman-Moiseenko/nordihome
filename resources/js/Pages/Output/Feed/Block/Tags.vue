<template>
    <Card title="Теги">
        <el-row>
            <el-col :span="12" class="bg-green-50 p-2">
                <div class="flex bg-white rounded-lg p-2">
                    <el-select v-model="tagInId" clearable filterable placeholder="Выберите тег">
                        <el-option v-for="tag in useCatalog.tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
                    </el-select>
                    <el-button type="success" class="ml-3" @click="add(true)">
                        <i class="fa-light fa-tags mr-2"></i>
                        Включить
                    </el-button>
                </div>
                <el-tag
                    v-for="tag in feed.tagsIn"
                    :key="tag.id"
                    type="success"
                    effect="plain"
                    closable
                    @close="remove(tag.id, true)"
                >{{ tag.name }}</el-tag>
            </el-col>
            <el-col :span="12" class="bg-red-50 p-2">
                <div class="flex bg-white rounded-lg p-2">
                    <el-select v-model="tagOutId" clearable filterable placeholder="Выберите тег">
                        <el-option v-for="tag in useCatalog.tags" :key="tag.id" :label="tag.name" :value="tag.id"/>
                    </el-select>
                    <el-button type="danger" class="ml-3" @click="add(false)">
                        <i class="fa-light fa-tags mr-2"></i>
                        Исключить
                    </el-button>
                </div>
                <el-tag
                    v-for="tag in feed.tagsOut"
                    :key="tag.id"
                    type="danger"
                    effect="plain"
                    closable
                    @close="remove(tag.id, false)"
                >{{ tag.name }}</el-tag>
            </el-col>
        </el-row>
    </Card>
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import Card from "./Card.vue";
import {useCatalogStore} from "@Res/catalogStore";

const useCatalog = useCatalogStore()
const props = defineProps({
    feed: {type: Object, required: true},
    save: {type: Function, required: true},
})

const tagInId = ref(null)
const tagOutId = ref(null)

function add(_in: boolean) {
    const id = _in ? tagInId.value : tagOutId.value
    if (!id) return
    props.save('tags', 'add', _in, [id])
    if (_in) tagInId.value = null
    else tagOutId.value = null
}

function remove(id: number, _in: boolean) {
    props.save('tags', 'remove', _in, [id])
}
</script>
