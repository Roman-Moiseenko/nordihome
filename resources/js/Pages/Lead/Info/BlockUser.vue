<template>
    <div v-if="lead.client" class="my-1">
        <Link :href="route('admin.client.show', {client: lead.client.id})" class="flex items-center w-full text-sm"
              type="primary"> {{ lead.client.fullName }}</Link>
    </div>
    <div v-if="!lead.client_id" class="flex my-1">
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
