<template>
    <el-menu :default-active="getUrl($page.url)" class=""
             active-text-color="rgb(217 119 6)" text-color="rgb(19 78 74)"
                 background-color="rgb(226 232 240)"
    >
        <template v-for="(item, index) in menus">
            <template v-if="item.sub_menu">
                <el-sub-menu :index="index">
                    <template #title>
                        <ItemMenu
                            :title="item.title"
                            :font_awesome="item.font_awesome"
                        />
                    </template>
                    <template v-for="subitem in item.sub_menu">
                        <el-menu-item :index="route(subitem.route_name, undefined, false)">
                            <ItemMenu
                                :route_name="subitem.route_name"
                                :title="subitem.title"
                                :font_awesome="subitem.font_awesome"
                                :vue="subitem.vue"
                            />
                        </el-menu-item>
                    </template>
                </el-sub-menu>
            </template>
            <template v-else-if="item.title">
                <el-menu-item :index="route(item.route_name, undefined, false)">
                    <ItemMenu
                        :route_name="item.route_name"
                        :title="item.title"
                        :font_awesome="item.font_awesome"
                        :vue="item.vue"
                    />
                </el-menu-item>
            </template>
            <template v-else>
                <el-divider/>
            </template>
        </template>
    </el-menu>
</template>

<script lang="ts" setup>
import {Link, usePage} from '@inertiajs/vue3'
import ItemMenu from "@Comp/Menu/ItemMenu.vue";

const menus = usePage().props.menus

function getUrl(url) {
    let t = url.split('?')[0]
    let tt = t.match(/^(.+?)\/[0-9]/, 'gm');
    if (tt === null) return t;
    return tt[1];
}
</script>
<style lang="scss">
:root {
    --el-menu-item-height: 48px;
}

.el-sub-menu > el-menu {
    background-color: rgb(241 245 249) !important;
    color: rgb(19 78 74);
    border-radius: 12px;
}
</style>
