<template>

</template>
<script lang="ts" setup>
import 'element-plus/es/components/message/style/css'; // this is only needed if the page also used ElMessage
import 'element-plus/es/components/message-box/style/css';
import { defineProps, watch } from 'vue'
import {ElMessage} from "element-plus";
const props = defineProps({
        errors: Object,
        flash: Object,
    }
)

watch(() => props.flash, (newValues, oldValues) => {
    message();
    //console.log('Prop values changed:', newValues, oldValues);
});


function message() {
    let _type = '', _mes = '', _duration = 3000;
    if (props.flash === undefined) return;

    if (props.flash.info) {
        _type = 'info'
        _mes = props.flash.info
    }
    if (props.flash.success) {
        _type = 'success'
        _mes = props.flash.success
    }
    if (props.flash.warning) {
        _type = 'warning'
        _mes = props.flash.warning
    }
    if (props.flash.error || Object.keys(props.errors).length > 0) {
        _type = 'error'
        if (props.flash.error) {
            _mes = props.flash.error;
        } else if (Object.keys(props.errors).length === 1) {
            _mes = 'Есть ошибки в форме.';
        } else {
            _mes = 'Обнаружено ' + Object.keys(props.errors).length + ' ошибки(ок) в форме.';
        }
        _duration = 7000
    }
    if (_mes !== '')
        ElMessage({
            message: _mes,
            type: _type,
            plain: true,
            showClose: true,
            duration: _duration,
            center: true,
        });
}

message()
</script>

