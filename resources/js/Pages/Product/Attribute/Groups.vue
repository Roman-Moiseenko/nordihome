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
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column prop="name" label="Группа" >
                    <template #default="scope">
                        <EditField :field="scope.row.name" @update:field="val => onRename(val, scope.row)" />
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Атрибуты" align="center"/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button size="small"
                                   type="primary"
                                   @click.stop="onUp(scope.row)"
                        >
                            <i class="fa-light fa-arrow-up"></i>
                        </el-button>
                        <el-button size="small"
                                   type="primary"
                                   @click.stop="onDown(scope.row)"
                        >
                            <i class="fa-light fa-arrow-down"></i>
                        </el-button>

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
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from "@Comp/Elements/Active.vue";
import EditField from "@Comp/Elements/EditField.vue";


const props = defineProps({
    groups: Object,
    title: {
        type: String,
        default: 'Группы атрибутов',
    },
})
const tableData = ref([...props.groups])
const visible_create = ref(false)
const new_group = ref('')
const $delete_entity = inject("$delete_entity")
function createButton() {
    router.visit(route('admin.product.attribute.group-add'), {
        method: "post",
        data: {
            name: new_group.value,
        },
        preserveScroll: true,
        preserveState: false,
    })
}
function onRename(val, row) {
    router.visit(route('admin.product.attribute.group-rename', {group: row.id}), {
        method: "post",
        data: {
            name: val,
        },
        preserveScroll: true,
        preserveState: false,
    })
}
function onUp(row) {
    router.visit(route('admin.product.attribute.group-up', {group: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onDown(row) {
    router.visit(route('admin.product.attribute.group-down', {group: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.attribute.group-destroy', {group: row.id}));
}
</script>
