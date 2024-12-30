
<template>
    <div v-if="user_id">
        //TODO
        Клиент назначен. Менять данные
        В отдельный компоненент
    </div>
    <div v-else>
        <SearchUser :route="route" />
    </div>
</template>

<script setup lang="ts">

import {reactive, ref, watch} from "vue";
import axios from "axios";
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";
import SearchUser from "@Comp/User/Search.vue"

const props = defineProps({
    user_id: Number,
    route: String,
})
const form = reactive({
    user_id: props.user_id,
})

if (props.user_id) {
    loadUserData();
}
watch(() => props.user_id, (newValues, oldValues) => {
    loadUserData();
});


function loadUserData() {
    //TODO Загрузка через axios Данных о клиенте
}

function updateUserData() {

}

</script>
