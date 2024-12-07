<template>
    <h2 class="font-medium mb-3">{{ label }}</h2>
    <!-- FileUpload -->
    <el-upload action="#" list-type="picture-card"
               :limit="1"
               :auto-upload="false"
               v-model:fileList="Images"
               @input="onSelectFile($event.target.files[0])" :on-remove="handleRemoveImages"
               :class="'file-uploader-one' + ((mini) ? ' mini' : '')"
               ref="template"
    >
        <el-icon>
            <Plus/>
        </el-icon>
        <template #file="{ file }">
            <div>
                <img class="el-upload-list__item-thumbnail" v-bind:src="file.url" alt=""/>
                <span class="el-upload-list__item-actions">
                  <span class="el-upload-list__item-preview" @click="handlePictureCardPreview(file)">
                    <el-icon><zoom-in/></el-icon>
                  </span>
                  <span v-if="!disabled" class="el-upload-list__item-delete"
                        @click="handleRemoveImages(file)">
                    <el-icon><Delete/></el-icon>
                  </span>
              </span>
            </div>
        </template>
    </el-upload>

    <!-- File Preview -->
    <el-dialog v-model="dialogVisible">
        <img w-full v-bind:src="dialogImageUrl" alt="Preview Image"/>
    </el-dialog>
</template>

<script lang="ts" setup>
import {defineProps, ref} from 'vue'
import {UploadFile} from "element-plus";

const dialogVisible = ref(false)
const dialogImageUrl = ref('')
const disabled = ref(false)

const props = defineProps({
    label: String,
    image: {
        type: String,
        default: null,
    },
    mini: {
        type: Boolean,
        default: false,
    }
});
const Images = ref<UploadFile>([]);
if (props.image !== null) Images.value.push({name: 'default', url: props.image,});
const emit = defineEmits(['selectImageFile']);

const onSelectFile = function (val) {
    emit('selectImageFile', {
        file: val,
        clear_file: true
    });
}
const handleRemoveImages = (file: UploadFile) => {
    Images.value.splice(0, 1);
    emit('selectImageFile', {
        file: null,
        clear_file: true
    });
}
const handlePictureCardPreview = (file: UploadFile) => {
    dialogImageUrl.value = file.url!
    dialogVisible.value = true
}
</script>

<style lang="scss">
//
.mini {
    .el-upload--picture-card {
        --el-upload-picture-card-size: 48px;
    }
    .el-upload-list__item{
        --el-upload-list-picture-card-size: 48px;
    }
}
</style>
