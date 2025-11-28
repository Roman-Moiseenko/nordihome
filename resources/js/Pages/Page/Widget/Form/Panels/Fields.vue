<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-file-invoice"></i>
                <span> Поля (* для лидов)</span>
            </span>
        </template>

        <el-button type="primary" size="small" @click="AddField" class="ml-3">Добавить поле</el-button>
        <el-table
            :data="tableFields"
            :max-height="600"
            style="width: 100%;"
            :row-class-name="classes.TableActive"
        >
            <el-table-column sortable prop="index" label="" width="40"/>

            <el-table-column sortable prop="value" label="value" width="160">
                <template #default="scope">
                    <el-input v-model="scope.row.value" @change="OnChange"/>
                </template>
            </el-table-column>
            <el-table-column sortable prop="label" label="label" width="200">
                <template #default="scope">
                    <el-input v-model="scope.row.label" @change="OnChange"/>
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right">
                <template #default="scope">
                    <el-button
                        size="small"
                        type="danger"
                        @click.stop="OnDelete(scope.row)"
                    >
                        Delete
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <div>
            <el-button v-if="notSave" type="info" class="mt-2" @click="OnCancel">Отменить</el-button>
            <el-button v-if="notSave" type="danger" class="mt-2" @click="OnSave">Сохранить</el-button>

        </div>
    </el-tab-pane>
</template>

<script lang="ts" setup>
import axios from "axios";
import {func} from '@Res/func.js'
import {defineProps, onMounted, reactive, ref} from "vue";
import {classes} from "@Res/className.js";
import Active from "@Comp/Elements/Active.vue";

import {router} from "@inertiajs/vue3";

const props = defineProps({
        id: Number,
        fields: Object
    }
)
const loadTable = () => {
    tableFields.value = []
    let index = 0;
    for (let value in props.fields) {
        let label = props.fields[value]
        index++
        tableFields.value.push({
            index: index,
            value: value,
            label: label,
        })

    }
}

const notSave = ref(false)
const tableFields = ref<Array>([]);

const form = reactive({})


loadTable()

function OnChange() {
    notSave.value = true
}

function OnCancel() {
    loadTable()
    notSave.value = false
}

function OnSave() {
    router.visit(route('admin.page.widget.form.set-fields', {widget: props.id}), {
        method: "post",
        data: {fields: tableFields.value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            notSave.value = false
        },
    })
}

function OnDelete(row) {
    tableFields.value.forEach(item => {
        if (item.index === row.index) {
            let index = tableFields.value.indexOf(item)
            tableFields.value.splice(index, 1);
            OnChange()
        }
    });
}

function AddField() {
    tableFields.value.push({
        index: tableFields.value.length + 1,
        value: null,
        label: null,
    })
    OnChange()
}

</script>
