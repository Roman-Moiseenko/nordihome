<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">
        {{ client.lastName }} {{ client.firstName }} {{ client.middleName }}
    </h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <ClientInfo :client="client" :organizations="organizations" :deliveries="deliveries" :type_pricing="type_pricing"/>
    </div>

    <!-- Через composable подгрузка -->

    Подключить компонент Orders
    <br>
</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head} from '@inertiajs/vue3'

import ClientInfo from './Blocks/Info.vue'


const props = defineProps({
    client: Object,
    title: {
        type: String,
        default: 'Карточка клиента',
    },
    filters: Array,
    organizations: Object,
    deliveries: Array,
    type_pricing: Array,
})


console.log(props.client)
interface IRow {
    amount: number,
    payment: number,
}

</script>
