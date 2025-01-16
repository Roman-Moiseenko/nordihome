<template>
    <Head title="Login in I-Shop-L" />

    <div class="flex items-center justify-center p-6 min-h-screen bg-cyan-600">
        <flash-messages />
        <div class="w-full max-w-md">
            <div class="block mx-auto w-full max-w-xs fill-white flex text-gray-50" >
                <logo height="100" />
                <span class="text-4xl my-auto font-semibold pt-3">Online Shop</span>
            </div>


            <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
                <div class="px-10 py-12">
                    <h1 class="text-center text-3xl font-bold">Welcome Back!</h1>
                    <div class="mt-6 mx-auto w-24 border-b-2" />

                    <el-input v-model="form.name" class="mt-10" label="Login" type="text" autofocus autocapitalize="off" />
                    <el-input v-model="form.password" class="mt-6" label="Password" type="password" />
                    <el-checkbox v-model="form.remember" :checked="true">Запомнить меня</el-checkbox>
                </div>
                <div class="flex px-10 py-4 bg-gray-100 border-t border-gray-100">
                    <el-button :loading="form.processing" class="ml-auto mt-1" type="primary" @click="login">Login</el-button>
                </div>
            </form>
        </div>
    </div>

    <flash-messages :errors="usePage().props.errors" :flash="usePage().props.flash"/>
</template>
<script lang="ts" setup>

//TODO Перенести
import { Head } from '@inertiajs/vue3'
import Logo from '@Comp/Logo.vue'
import {usePage, router} from "@inertiajs/vue3";
import FlashMessages from "@Comp/FlashMessages.vue";
import {defineProps, reactive} from 'vue'

const props = defineProps({
    errors: Object,
})
const form = reactive({
    name: '',
    password: '',
    remember: false,
})
function login() {
    router.post(route('admin.login'), form)
}
</script>



<style scoped>

</style>
