<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">
        Контрагент {{ organization.short_name }}
    </h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-tabs>
            <CommonPanel :organization="organization" :holdings="holdings"/>
            <AddressPanel :organization="organization"/>
            <BankPanel :organization="organization"/>
            <ContactPanel :organization="organization"/>
            <FilePanel :organization="organization"/>
        </el-tabs>

    </div>
    <div class="bg-white rounded-lg mt-3 p-5" v-if="organization.trader">
        <el-tag type="warning" size="large">Компания Продавец</el-tag>
        <Link type="warning" class="ml-3" :href="route('admin.accounting.trader.show', {trader: organization.trader.id})">{{ organization.trader.name }}</Link>
    </div>
    <div class="bg-white rounded-lg mt-3 p-5" v-if="organization.distributor">
        <el-tag type="primary" size="large">Компания Поставщик</el-tag>
        <Link type="primary" class="ml-3" :href="route('admin.accounting.distributor.show', {distributor: organization.distributor.id})">{{ organization.distributor.name }}</Link>
    </div>
    <div class="bg-white rounded-lg mt-3 p-5" v-if="organization.shopper">
        <el-tag type="success" size="large">Компания Покупатель</el-tag>
        <Link type="success" class="ml-3" :href="route('admin.user.show', {user: organization.shopper.id})">{{ func.fullName(organization.shopper.fullname) }}</Link>
    </div>

</template>

<script lang="ts" setup>
import {func} from '@Res/func.js'
import {defineProps} from "vue";
import {Head, router, Link} from '@inertiajs/vue3'

//Панели
import CommonPanel from './Panels/Common.vue'
import AddressPanel from './Panels/Address.vue'
import BankPanel from './Panels/Bank.vue'
import ContactPanel from './Panels/Contact.vue'
import FilePanel from './Panels/File.vue'



const props = defineProps({
    organization: Object,
    title: {
        type: String,
        default: 'Карточка контрагента',
    },
    holdings: Array,
})

</script>
