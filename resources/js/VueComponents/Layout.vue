<template>
    <div>
        <div id="dropdown"/>
        <div class="md:flex md:flex-col">
            <div class="md:flex md:flex-col md:h-screen">
                <div class="md:flex md:shrink-0">
                    <!-- Logo -->
                    <div
                        class="flex items-center justify-between px-6 py-4 bg-teal-950 md:shrink-0 md:justify-center md:w-64">
                        <Link class="mt-1 flex" href="/admin">
                            <logo class="fill-white" width="28" height="28"/>
                            <span class="text-white text-lg ml-3 font-medium">{{ app_name }}</span>
                        </Link>

                        <dropdown class="md:hidden" placement="bottom-end">
                            <template #default>
                                <svg class="w-6 h-6 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                                </svg>
                            </template>
                            <template #dropdown>
                                <div class="mt-2 px-8 py-4 bg-teal-950rounded shadow-lg">
                                    <!--main-menu :menus="usePage().props.menus"/-->
                                </div>
                            </template>
                        </dropdown>
                    </div>
                    <!-- BreadCrumbs -->
                    <div
                        class="bg-teal-950 text-white md:text-md flex items-center p-4 w-full text-sm border-b md:px-12 md:py-0">
                        <div class="ml-4 mt-1">
                            <bread-crumbs />
                        </div>

                        <!--el-button @click="goForward" type="primary" plain><i class="fa-light fa-right"></i></el-button-->
                        <dropdown class="ml-auto mt-1" placement="bottom-end">
                            <template #default>
                                <div class="group flex items-center cursor-pointer select-none">
                                    <div
                                        class="mr-1 text-gray-50 group-hover:text-white focus:text-indigo-600 whitespace-nowrap">
                                        <template v-if="usePage().props.auth">
                                        <span>{{ usePage().props.auth.user.first_name }}</span>
                                        <span class="hidden md:inline">&nbsp;{{ usePage().props.auth.user.last_name }}</span>
                                        </template>
                                    </div>
                                    <el-icon>
                                        <ArrowDown/>
                                    </el-icon>

                                </div>
                            </template>
                            <template #dropdown>
                                <div class="mt-2 py-2 text-sm bg-white rounded shadow-xl">
                                    <Link class="block px-6 py-2 hover:text-white hover:bg-indigo-500"
                                          :href="`/admin/staff/${usePage().props.auth.user.id}/edit`">My Profile
                                    </Link>
                                    <Link class="block px-6 py-2 hover:text-white hover:bg-indigo-500" href="/users">
                                        Manage Users
                                    </Link>
                                    <el-link class="block px-6 py-2 w-full text-left hover:text-white hover:bg-indigo-500"
                                          href="/admin/logout" method="delete" as="button">Logout
                                    </el-link>
                                    <!-- TODO Вернуть Link class="block px-6 py-2 w-full text-left hover:text-white hover:bg-indigo-500"
                                          href="/admin/logout" method="delete" as="button">Logout
                                    </Link-->
                                </div>
                            </template>
                        </dropdown>
                    </div>
                </div>
                <div class="md:flex md:grow md:overflow-hidden">
                    <main-menu class="hidden shrink-0 pt-3 w-64 overflow-y-auto md:block" :menus="usePage().props.menus"/>
                    <div class="px-4 py-8 md:flex-1 md:p-4 md:overflow-y-auto bg-slate-50 affix-container" scroll-region>
                        <flash-messages :errors="usePage().props.errors" :flash="usePage().props.flash"/>
                        <slot/>
                        <el-backtop target=".affix-container" :visibility-height="100" :right="30" :bottom="20" />
                        <el-button @click="goBack" type="info" dark class="mt-5  shadow shadow-slate-400">
                            <i class="fa-light fa-left mr-2"></i>Назад</el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script lang="ts" setup>
import {ArrowDown} from "@element-plus/icons-vue";
import { usePage } from '@inertiajs/vue3'
import {Link} from '@inertiajs/vue3'
import Logo from '@Comp/Logo.vue'
import Dropdown from '@Comp/Dropdown.vue'
import MainMenu from '@Comp/Menu/MainMenu.vue'
import FlashMessages from '@Comp/FlashMessages.vue'
import BreadCrumbs from '@Comp/BreadCrumbs.vue';
import {defineProps} from "vue";
const app_name = import.meta.env.VITE_APP_NAME

function goBack() {
    window.history.back()
}
function goForward() {
    window.history.forward()
}

</script>
<style>
.bottom-fix {
    position: fixed;
    bottom: 0;
    left: 281px;
    right: 20px;
    background: #0a3622;
}
</style>
