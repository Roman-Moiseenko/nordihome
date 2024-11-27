<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-files"></i>
                <span> Документы</span>
            </span>
        </template>
        <el-row :gutter="10">
            <el-col :span="8">
                <h2 class="font-medium">Договора</h2>
                <el-upload
                    v-model:file-list="contractList"
                    action="#"
                    :on-preview="handlePreview"
                    :on-remove="handleRemove"
                    :auto-upload="false"
                    @input="upload($event.target.files[0], 'contract')"
                    :on-error="handleError"
                >
                    <template #trigger>
                        <el-button type="info" circle>
                            <el-icon>
                                <Paperclip/>
                            </el-icon>
                        </el-button>
                    </template>
                    <template #file="scope">

                    </template>
                </el-upload>
            </el-col>
            <el-col :span="8">
                <h2 class="font-medium">Файлы</h2>
                <el-upload
                    v-model:file-list="documentList"
                    action="#"
                    :on-preview="handlePreview"
                    :on-remove="handleRemove"
                    :auto-upload="false"
                    @input="upload($event.target.files[0], '')"
                    :on-error="handleError"
                >
                    <template #trigger>
                        <el-button type="info" circle>
                            <el-icon>
                                <Paperclip/>
                            </el-icon>
                        </el-button>
                    </template>
                    <template #file="scope">

                    </template>
                </el-upload>

            </el-col>
        </el-row>
    </el-tab-pane>
</template>

<script lang="ts" setup>
import {defineProps, handleError, reactive, ref} from "vue";
import {func} from '@Res/func.js'
import {router} from '@inertiajs/vue3'
import {UploadUserFile, UploadInstance} from "element-plus";
import axios from "axios";

const props = defineProps({
    organization: Array,
})

const contractList = ref<UploadUserFile[]>([]);
const documentList = ref<UploadUserFile[]>([]);

for (let key in props.organization.contracts) {
    contractList.value.push({
        url: props.organization.contracts[key].url,
        id: props.organization.contracts[key].id,
        name: props.organization.contracts[key].title,
    });
}
for (let key in props.organization.documents) {
    documentList.value.push({
        url: props.organization.documents[key].url,
        id: props.organization.documents[key].id,
        name: props.organization.documents[key].title,
    });
}

function upload(file, type) {
    router.visit(route('admin.accounting.organization.upload', {organization: props.organization.id}), {
        method: "post",
        data: {
            file: file,
            type: type,
        },
        preserveScroll: true,
        preserveState: true,
    })
}

const handleRemove: UploadProps['onRemove'] = (file, uploadFiles) => {
    router.post(route('admin.file.remove-file'), {id: file.id})
}
const handlePreview: UploadProps['onPreview'] = (uploadFile) => {
    axios.post(route('admin.file.download'),null,
        {
            responseType: 'arraybuffer',
            params: {id: uploadFile.id},
        }
    ).then(res=>{
        let blob = new Blob([res.data], {type: 'application/*'})
        let link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = uploadFile.name
        link._target = 'blank'
        document.body.appendChild(link);
        link.click();
        URL.revokeObjectURL(link.href)
    })
}

const handleError: UploadProps['onError'] = (error: Error, uploadFile: UploadFile, uploadFiles: UploadFiles) => {
    console.log(error)
}

</script>
