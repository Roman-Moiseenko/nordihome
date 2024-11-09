<template>
    <el-form :model="form" label-width="auto">

        <slot />

        <el-button type="primary" plain @click="onSubmit(false)">Сохранить</el-button>
        <el-button type="primary" @click="onSubmit(true)">Сохранить и Закрыть</el-button>
        <div v-if="isUnSave === true" class="text-red-700">Были внесены изменения, данные не сохранены</div>
    </el-form>
</template>



<script lang="ts" setup>
/*import {router} from "@inertiajs/vue3";
import {ref, watch, reactive} from "vue";*/

//TODO https://programmersought.com/article/561511549001/
/*
const isUnSave = ref(false)
const props = defineProps({
    modelForm: Object,
    route: String,
})
const emit = defineEmits(['update:modelForm']);
const form = reactive({...props.modelForm})
*//*
watch(
    form,
    function (newValue, oldValue) {
        console.log(newValue);
        emit('update:modelForm', form);
    },
    {deep: true}
);

*/
</script>
<script lang="ts">
import {router} from "@inertiajs/vue3";

export default {
    props: {
        form: Object,
        route: String,
    },
    data() {
        return {
            isUnSave: false,
            form: {...this.$props.form, close: null},
        }
    },
    watch: {
        form: function (val) {
            this.$data.isUnSave = true
        }
    },
    methods: {


        onSubmit(val) {
            console.log(this.$data.form)
            this.$data.isUnSave = false
            this.$data.form.close = val
            router.post(this.$props.route, this.$data.form);
        }
    }
}
</script>
<style scoped>

</style>
