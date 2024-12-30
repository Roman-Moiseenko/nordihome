<template>
    <el-descriptions :column="1" border class="mb-1" :size="small ? 'small' : 'default'">
        <el-descriptions-item label="ФИО">
            {{ func.fullName(user.fullname) }}
        </el-descriptions-item>
        <el-descriptions-item label="Телефон">
            {{ func.phone(user.phone) }}
        </el-descriptions-item>
        <el-descriptions-item label="Email">
            {{ user.email }}
        </el-descriptions-item>
        <el-descriptions-item label="Доставка">
            {{ user.delivery_name }}
        </el-descriptions-item>
        <el-descriptions-item label="Адрес">
            {{ user.address.post }} {{ user.address.region }} {{ user.address.address }}
        </el-descriptions-item>
        <el-descriptions-item label="Цена">
            {{ user.pricing }}
        </el-descriptions-item>
        <el-descriptions-item v-if="organization.name" label="Юридическое лицо">
            {{ organization.name }} ({{ organization.inn }})
        </el-descriptions-item>
    </el-descriptions>
    <Link type="warning" :href="route('admin.user.show', {user: user.id})">Карточка клиента</Link>

</template>


<script setup lang="ts">
import {reactive, ref} from "vue";
import {func} from '@Res/func.js'
import {router, Link} from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
    small: {
        type: Boolean,
        default: false,
    }
})

const organization = reactive({
    name: null,
    inn: null,
})

props.user.organizations.forEach(function (item) {
    if (item.active) {
        organization.name = item.short_name
        organization.inn = item.inn
    }
})
console.log(props.user)




</script>
