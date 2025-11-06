<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <el-radio-group v-model="selectedUrlType" size="large" fill="#6cf" @change="onChangeType">
                <div v-for="(type, index) in urlTypes">
                    <el-radio-button :label="type.name" :value="index"/>
                </div>
            </el-radio-group>

            <div class="mt-2">
                <el-select v-model="newItem" style="width: 200px"
                           placeholder="Выберите ссылку">
                    <el-option v-for="item in urlItems" :value="item" :key="item.url" :label="item.name"/>
                </el-select>

                <el-button v-if="showAddUrlItem" type="success" @click="onAddItem">Добавить элемент</el-button>
            </div>
        </el-col>
        <el-col :span="12">
            <el-form style="width: 300px;">
                <el-form-item label="Ссылка">
                    <el-input v-model="form.url" />
                </el-form-item>
                <el-form-item label="Подпись">
                    <el-input v-model="form.name" />
                </el-form-item>
            </el-form>
            <el-button v-if="showAddUrl" type="success" @click="onAddUrl">Добавить Url</el-button>
        </el-col>
    </el-row>
</template>

<script setup lang="ts">
import {reactive, ref, onMounted, defineProps, watch, computed} from "vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import { menuStore } from './store.js'

const store = menuStore()
const props = defineProps({
    id: Number,
})
const urlTypes = ref();
const newItem = ref();
const urlItems = ref();
const selectedUrlType = ref(0);
const form = reactive({
    name: "",
    url: ""
})
const showAddUrlItem = ref(false)
const showAddUrl = computed(() => {
    return form.name !== "" && form.url !== "";
});
const emit = defineEmits(['add:item']);
const loadUrls = () => {
    axios.post(route('admin.page.menu.get-urls', {menu: props.id})).then(response => {
        urlTypes.value = response.data
        onChangeType()
    }).catch(response => {
        //console.log(response)
    })

}

onMounted(loadUrls)

watch(() => newItem.value, (newValue) => {
    showAddUrlItem.value = newValue !== null
})
watch(() => store.reload, (newValue) => {
    if (newValue) {
        loadUrls()
        menuStore().afterReload()
    }
})

function onChangeType() {
    urlItems.value = [...urlTypes.value[selectedUrlType.value].items]
    newItem.value = null
}
function onAddItem() {
    router.visit(route('admin.page.menu.add-item', {menu: props.id}), {
        method: "post",
        data: newItem.value,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            loadUrls()
            newItem.value = null
            emit('add:item', true)
        }
    })
}
function onAddUrl() {
    router.visit(route('admin.page.menu.add-item', {menu: props.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            form.name = ""
            form.url = ""
            emit('add:item', true)
        }
    })
}
</script>

