<template>
    <Head><title>{{ title + log.date }} </title></Head>
    <h1>История парсера за {{ log.date }}</h1>
    <el-button v-if="!log.read" type="success" size="small" @click="onRead">Прочитано</el-button>
    <div>
        <el-tabs>
            <PanelNew :items="log.new" />
            <PanelChange :items="log.change" />
            <PanelDel :items="log.del" />
        </el-tabs>
    </div>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import PanelNew from './Panels/New.vue'
import PanelChange from './Panels/Change.vue'
import PanelDel from './Panels/Del.vue'

const props = defineProps({
    log: Object,
    title: {
        type: String,
        default: 'История парсера за ',
    },
})

function onRead() {
    router.post(route('admin.parser.log.read', {parser_log: props.log.id}))
}
</script>
