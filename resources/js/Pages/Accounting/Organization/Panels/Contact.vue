<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-address-card"></i>
                <span> Контакты</span>
            </span>
        </template>
        <div class="grid lg:grid-cols-6 grid-cols-1 divide-x">
            <div class="p-4 col-span-2">
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="Должность">
                        <EditField :field="organization.post" @update:field="savePost"/>
                    </el-descriptions-item>
                    <el-descriptions-item label="ФИО">
                        <EditField :field="organization.chief" @update:field="saveChief" :isFIO="true"/>
                    </el-descriptions-item>
                    <el-descriptions-item label="email">
                        <EditField :field="organization.email" @update:field="saveEmail"/>
                    </el-descriptions-item>
                    <el-descriptions-item label="Телефон">
                        <EditField :field="func.phone(organization.phone)" @update:field="savePhone" :formatter="val => func.MaskPhone(val)"/>
                    </el-descriptions-item>
                </el-descriptions>
            </div>
            <div class="p-4 col-span-4">
                <el-button type="primary" @click="newContact"><i class="fa-light fa-user-plus"></i></el-button>
                <div v-for="contact in organization.contacts" class="mb-2">
                    <div class="">
                        <i class="fa-sharp fa-light fa-flag-pennant text-sky-700"></i>
                        {{ contact.post }}

                        <i class="fa-light fa-user-tie-hair text-sky-700 ml-2"></i>
                        {{ func.fullName(contact.fullname) }}
                        <i class="fa-light fa-circle-envelope text-sky-700 ml-2"></i>
                        {{ contact.email }}
                        <i class="fa-light fa-circle-phone text-sky-700 ml-2"></i> {{ func.phone(contact.phone) }}
                        <el-button class="ml-2" type="warning" size="small" @click="editContact(contact)">
                            <i class="fa-light fa-pen-to-square"></i>
                        </el-button>
                        <el-button class="ml-2" type="danger" size="small" @click="delContact(contact)">
                            <i class="fa-light fa-trash"></i>
                        </el-button>
                    </div>
                </div>
            </div>
        </div>
    </el-tab-pane>


    <el-dialog v-model="dialogContact" title="Контакты контрагента" width="500">
        <el-form>
            <el-form-item>
                <el-input v-model="contactForm.post" placeholder="Должность"/>
            </el-form-item>
            <el-form-item>
                <div class="flex">
                    <el-input v-model="contactForm.fullname.surname" placeholder="Фамилия"/>
                    <el-input v-model="contactForm.fullname.firstname" placeholder="Имя"/>
                    <el-input v-model="contactForm.fullname.secondname" placeholder="Отчество"/>
                </div>
            </el-form-item>
            <el-form-item>
                <el-input v-model="contactForm.email" placeholder="Email"/>
            </el-form-item>
                <el-form-item>
            <el-input v-model="contactForm.phone" placeholder="Телефон" :formatter="val => func.MaskPhone(val)"/>
                </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogContact = false">Отмена</el-button>
                <el-button type="primary" @click="saveContact">Сохранить</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import {ref, reactive} from "vue";
import {func} from '@Res/func.js'
import EditField from "@Comp/Elements/EditField.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    organization: Array,
})
const dialogContact = ref(false)

const initalContact  = () => {
    return {
        id: null,
        post: null,
        phone: null,
        email: null,
        fullname: {
            surname: null,
            firstname: null,
            secondname: null,
        },
    }
}
const contactForm = reactive(initalContact())

function savePost(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {post: val})
}
function saveEmail(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {email: val})
}
function savePhone(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {phone: val})
}
function saveChief(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {chief: val})
}

function newContact() {
    Object.assign(contactForm, initalContact())
    dialogContact.value = true
}
function editContact(val) {
    Object.assign(contactForm, val)
    dialogContact.value = true
}
function delContact(val) {
    router.delete(route('admin.accounting.organization.del-contact', {contact: val.id}));

}
function saveContact() {
    if (contactForm.email === null && contactForm.phone === null) return;
    dialogContact.value = false
    router.post(route('admin.accounting.organization.set-contact', {organization: props.organization.id}), contactForm);
    Object.assign(contactForm, initalContact())
}
</script>
