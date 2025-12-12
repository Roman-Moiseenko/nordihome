import {ref, computed, reactive} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'

const useStore = defineStore('table_data', () => {
    const loading = ref(false)
    const _tiny = reactive({
        height: 600,
        min_height: 500,
        language: 'ru',
        resize: true,
        plugins: 'quickbars image anchor link autolink autoresize charmap directionality emoticons ' +
            'fullscreen importcss insertdatetime lists advlist media nonbreaking pagebreak preview ' +
            'searchreplace table visualblocks code',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough ' +
            '| link image anchor media table mergetags | numlist bullist nonbreaking pagebreak ' +
            '| align lineheight | checklist numlist bullist indent outdent | emoticons charmap |' +
            '| removeformat ltr rtl fullscreen preview visualblocks | code',
        image_list: [],
        //tinymceScriptSrc: '/js/tinymce/tinymce.min.js',
    })
    const tiny = computed(() => _tiny)
    loadImageListTiny()

    function beforeLoad() {
        loading.value = true
    }

    function afterLoad() {
        loading.value = false
    }

    function loadImageListTiny() {
        axios.post(route('admin.page.gallery.images')).then(response => {
            response.data.forEach(item => {
                _tiny.image_list.push({
                    title: item.title,
                    value: item.value
                })
            })
        });
    }

    return {loading, tiny, beforeLoad, afterLoad}
})

export {useStore}
