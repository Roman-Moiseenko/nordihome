<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-file-invoice"></i>
                <span> Списки значений</span>
            </span>
        </template>

        <el-popover :visible="visible_create" placement="bottom-start" :width="246">
            <template #reference>
                <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create"
                           ref="buttonRef">
                    Добавить список
                    <el-icon class="ml-1">
                        <ArrowDown/>
                    </el-icon>
                </el-button>
            </template>
            <el-input v-model="new_slug"/>

            <div class="mt-2">
                <el-button @click="visible_create = false">Отмена</el-button>
                <el-button @click="AddList" type="primary">Создать</el-button>
            </div>
        </el-popover>

        <div v-for="(items, slug) in tableLists" class="mb-4">
            <el-tag type="success" effect="dark" size="large">Список {{ slug}}</el-tag>
            <el-button type="primary" size="small" @click="AddItem(slug)" class="ml-3">Добавить значение</el-button>
            <el-table
                :data="items"
                :max-height="600"
                style="width: 100%;"
                :row-class-name="classes.TableActive"
            >
                <el-table-column sortable prop="index" label="" width="20"/>
                <el-table-column sortable prop="label" label="Значение" width="340">
                    <template #default="scope">
                        <el-input v-model="scope.row.label" @change="OnChange"/>
                    </template>
                </el-table-column>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            type="danger"
                            @click.stop="OnDelete(slug, scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-button v-if="notSave" type="info" class="mt-2" @click="OnCancel">Отменить</el-button>
        <el-button v-if="notSave" type="danger" class="mt-2" @click="OnSave">Сохранить</el-button>
    </el-tab-pane>
</template>

<script lang="ts" setup>
import axios from "axios";
import {func} from '@Res/func.js'
import {defineProps, onMounted, ref} from "vue";
import {classes} from "@Res/className.js";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    id: Number,
    lists: Array
})

console.log("1", props.lists)
console.log("2", props.lists.length)

const tableLists = ref<Array>([])
const visible_create = ref(false)

const notSave = ref(false)
const new_slug = ref<String>("")


const loadTable = () => {
    tableLists.value = {}

    if (props.lists.length === 0) return;
    for (let key in props.lists) {
        let list = props.lists[key]
        console.log("3", key)
        console.log("4", list)
        let index = list.slug

        const tableItems = ref<Array>([])

        let i = 0;
        list.items.forEach(item => {
            i++;
            tableItems.value.push({
                index: i,
                label: item
            })
        })
        console.log("5", tableItems.value)
        tableLists.value[index] = tableItems.value;
    }
}

loadTable()

function OnChange() {
    notSave.value = true
}
function AddList() {
    console.log(new_slug.value)
    if (new_slug.value === "") return;
    visible_create.value = false
    tableLists.value[new_slug.value] = [];
    AddItem(new_slug.value);
}
function AddItem(slug) {
    tableLists.value[slug].push({
        index: tableLists.value[slug].length + 1,
        label: null
    })
    OnChange()
}
function OnCancel() {
    loadTable()
    notSave.value = false
}
function OnSave() {
    router.visit(route('admin.page.widget.form.set-lists', {widget: props.id}), {
        method: "post",
        data: {lists: tableLists.value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            notSave.value = false
        },
    })
}
function OnDelete(slug, row) {
    tableLists.value[slug].forEach(item => {
        if (item.index === row.index) {
            let index = tableLists.value[slug].indexOf(item)
            tableLists.value[slug].splice(index, 1);
            OnChange()
        }
    });
}

function OnDeleteList(slug) {
    delete tableLists.value[slug]

}

</script>
