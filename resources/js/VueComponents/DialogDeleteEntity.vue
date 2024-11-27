<template>
    <el-dialog v-model="showDialog.value" title="Удалить запись" width="400" center>
        <div class="font-medium text-md mt-2">
            Вы уверены, что хотите удалить {{ entity }}?
        </div>
        <div class="text-red-600 text-md mt-2">
            Восстановить данные будет невозможно!
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="showDialog.value = false">Отмена</el-button>
                <el-button type="danger" @click="removeItem">
                    Удалить
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>
<script lang="ts" setup>
import {reactive, ref} from "vue";
import {func} from '@Res/func.js'
import {router} from "@inertiajs/vue3";

const dialogDelete = ref(false)
const props = defineProps({
    entity: String,
    show: Boolean,
    route: String
})
const showDialog= reactive({value: props.show});
function removeItem() {
    if (props.route !== null) {
        router.visit(props.route, {
            method: 'delete',
            preserveScroll: true,
            preserveState: true,
        });
        showDialog.value = false;
    }
}
</script>
