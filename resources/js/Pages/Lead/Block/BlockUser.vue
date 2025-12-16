<template>
    <div v-if="lead.user" class="my-1">
        <Link :href="route('admin.user.show', {user: lead.user.id})" class="flex items-center w-full text-sm"
              type="primary"> {{ func.fullName(lead.user.fullname) }} </Link>
    </div>
    <div v-if="!lead.user" class="flex my-1">
        <EditField :field="lead.name" @update:field="onSetName" class="text-sm font-medium"/>
    </div>

</template>

<script setup lang="ts">

import {route} from "ziggy-js";
import {Link, router} from "@inertiajs/vue3";
import EditField from "@Comp/Elements/EditField.vue";
import {func} from "@Res/func.js"

const props = defineProps({
    lead: Object,
})

function onSetName(value) {
    router.visit(route('admin.lead.set-name', {lead: props.lead.id}), {
        method: "post",
        data: {name: value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
</script>
<style scoped>

</style>
