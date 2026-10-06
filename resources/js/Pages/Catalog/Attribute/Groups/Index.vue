<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Группы атрибутов</h1>
        <div class="flex items-center">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Добавить группу
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="new_group" placeholder="Группа"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button>
                    <el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                ref="tableRef"
                :data="tableData"
                row-key="id"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                @row-click="routeClick"
            >
                <el-table-column label="" width="48" align="center">
                    <template #default>
                        <i class="fa-light fa-grip-vertical drag-handle"></i>
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Группа" />
                <el-table-column prop="quantity" label="Атрибуты" align="center"/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button size="small"
                                   type="danger"
                                   @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <DeleteEntityModal name_entity="Группу атрибутов" />
    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, nextTick, onBeforeUnmount, onMounted, ref} from "vue";
import {Head, router} from '@inertiajs/vue3'
import axios from 'axios'
import Sortable from 'sortablejs'
import ru from 'element-plus/dist/locale/ru.mjs'

const props = defineProps({
    groups: Array,
    title: {
        type: String,
        default: 'Группы атрибутов',
    },
})
const tableData = ref([...props.groups])
const tableRef = ref()
const visible_create = ref(false)
const new_group = ref('')
const $delete_entity = inject("$delete_entity")
let sortable: Sortable | null = null

function createButton() {
    router.visit(route('admin.catalog.attribute-group.store'), {
        method: "post",
        data: {
            name: new_group.value,
        },
        preserveScroll: true,
        preserveState: false,
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.catalog.attribute-group.destroy', {id: row.id}));
}

function routeClick(row) {
    router.get(route('admin.catalog.attribute-group.show', {id: row.id}))
}

function initSortable() {
    const el = tableRef.value?.$el?.querySelector('.el-table__body-wrapper tbody')
    if (!el) return

    if (sortable) {
        sortable.destroy()
        sortable = null
    }

    sortable = Sortable.create(el, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: (evt) => {
            const oldIndex = evt.oldIndex
            const newIndex = evt.newIndex
            if (oldIndex === undefined || newIndex === undefined || oldIndex === newIndex) return

            const moved = tableData.value.splice(oldIndex, 1)[0]
            tableData.value.splice(newIndex, 0, moved)

            axios.post(route('admin.catalog.attribute-group.sort'), {
                id: moved.id,
                sort: newIndex + 1,
            }).catch(() => {
                router.reload({preserveScroll: true, preserveState: false})
            })
        },
    })
}

onMounted(() => {
    nextTick(initSortable)
})

onBeforeUnmount(() => {
    if (sortable) {
        sortable.destroy()
        sortable = null
    }
})
</script>
