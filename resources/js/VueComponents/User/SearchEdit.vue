
<template>
    <div v-if="loadData">
        <!--EditUser :user="data.user" :deliveries="data.deliveries" :type_pricing="data.type_pricing" :small="true"/-->
        <ViewUser :user="user" :small="true"/>
    </div>
    <div v-else v-loading="true">
        <span class="font-medium text-lg">Загрузка данных</span>
    </div>
    <div v-if="!user_id">
        <SearchUser :route="route" />
    </div>
</template>

<script setup lang="ts">

import {reactive, ref, watch} from "vue";
import axios from "axios";
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";
import SearchUser from "@Comp/User/Search.vue"
import EditUser from "@Comp/User/Edit.vue"
import ViewUser from "@Comp/User/View.vue"

const props = defineProps({
    user_id: Number,
    route: String,
})
const form = reactive({
    user_id: props.user_id,
})
const loadData = ref(false)
const user = ref(null)
if (props.user_id) {
    loadUserData();
}
watch(() => props.user_id, (newValues, oldValues) => {
    loadUserData();
});


function loadUserData() {
    //Загрузка через axios Данных о клиенте
    axios.post(route('admin.user.get-edit-data'), {user_id: props.user_id}).then(response => {
        if (response.data.error !== undefined) console.log(response.data.error)
        //console.log(response.data)
        user.value = response.data
        loadData.value = true
        //loading.value = false
    }).catch(reason => {
        console.log('reason', reason)
    });
}

function updateUserData() {

}

</script>
