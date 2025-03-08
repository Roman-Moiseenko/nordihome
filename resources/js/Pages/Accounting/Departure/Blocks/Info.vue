<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Склад списания">
                    <el-select v-model="info.storage_id" @change="setInfo" :disabled="iSavingInfo || notEdit"
                               style="width: 260px">
                        <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"
                                   :readonly="notEdit"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Организация заказчик">
                    <el-select v-model="info.customer_id"  @change="setInfo" :disabled="iSavingInfo || notEdit" filterable>
                        <el-option v-for="item in customers" :key="item.id" :value="item.id" :label="item.short_name + ' (' + item.inn +')'"/>
                    </el-select>
                </el-form-item>
            </el-form>

            <template v-if="departure.completed">
                <div class="font-medium mb-2">Вложенные файлы:</div>
                <div v-for="(item, index) in departure.photos" class="ml-1 mt-1">
                    <el-link :href="item.url" target="_blank">{{ item.name }}</el-link>
                </div>
            </template>
            <template v-else>
                <el-upload
                    ref="upload" action="#"
                    v-model:file-list="fileList"
                    class="lg:w-80"
                    :on-remove="handleRemove"
                    :auto-upload="false"
                    @input="upload"
                >
                    <!-- form.attachments = $event.target.files  multiple -->
                    <template #trigger>
                        <el-button type="info" circle>
                            <el-icon>
                                <Paperclip/>
                            </el-icon>
                        </el-button>
                    </template>
                </el-upload>
            </template>

        </el-col>
    </el-row>
</template>

<script lang="ts" setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import {UploadUserFile, UploadInstance} from "element-plus";
import AccountingDocument from "@Comp/Accounting/Document.vue";

const props = defineProps({
    departure: Object,
    storages: Array,
    customers: Array,
})
const iSavingInfo = ref(false)

const info = reactive({
    document: {
        number: props.departure.number,
        created_at: props.departure.created_at,
        incoming_number: props.departure.incoming_number,
        incoming_at: props.departure.incoming_at,
        comment: props.departure.comment,
    },
    storage_id: props.departure.storage_id,
    customer_id: props.departure.customer_id,

})
const notEdit = computed(() => props.departure.completed || props.departure.trashed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.departure.set-info', {departure: props.departure.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}

//Файлы
const fileList = ref<UploadUserFile[]>([]);
for (let key in props.departure.photos) {
    fileList.value.push({
        name: props.departure.photos[key].name,
        url: props.departure.photos[key].url,
        id: props.departure.photos[key].id,
    });
}
const form = reactive({
    attachments: [],
})


function handleRemove(val) {
    if (val.url !== undefined) {
        router.post(route('admin.accounting.departure.delete-photo', {departure: props.departure.id}), {
            file: val.name,
            url: val.url,
            id: val.id,
        })
    }
}

function upload(val) {
    router.visit(route('admin.accounting.departure.upload', {departure: props.departure.id}), {
        method: "post",
        data: {files: val.target.files,},
        preserveScroll: true,
        preserveState: true,

    })
    //console.log(val.target.files)
}
</script>
