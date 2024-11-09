import {defineStore} from 'pinia'
export const useStore = defineStore('table_data', {
    state: () => ({
        loading: false,
        tiny: {
            height: 600,
            min_height: 500,
            language: 'ru',
            resize: true,
            plugins: 'quickbars image anchor link autolink autoresize charmap directionality emoticons ' +
                'fullscreen importcss insertdatetime lists advlist media nonbreaking pagebreak preview ' +
                'searchreplace table visualblocks',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough ' +
                '| link image anchor media table mergetags | numlist bullist nonbreaking pagebreak ' +
                '| align lineheight | checklist numlist bullist indent outdent | emoticons charmap | | removeformat ltr rtl fullscreen preview visualblocks',
        }
    }),
    getters: {
        getLoading: (state) => state.loading,
    },
    actions: {
        beforeLoad() {
            this.loading = true;
        },
        afterLoad() {
            this.loading = false;
        }
    },
})
