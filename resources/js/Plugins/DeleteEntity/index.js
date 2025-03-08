import { reactive } from "vue"
import DeleteEntityModal from "./Modal.vue"
import {router} from "@inertiajs/vue3";
import {ElLoading} from "element-plus";

const
    _current = reactive({name:"",resolve:null,reject:null, route: "", state: false, soft: false}),
    api = {
        active() {return _current.name;},
        show(route, {name = 'entity', state = false, soft = false} = {}) {
            _current.name = name;
            _current.route = route;
            _current.state = state;
            _current.soft = soft;
            return new Promise(
                (resolve = null, reject = null) => {
                    _current.resolve = resolve;
                    _current.reject = reject;
                })
        },
        accept() {
            const loading = ElLoading.service({
                lock: false,
                text: 'Удаление',
                background: 'rgba(0, 0, 0, 0.7)',
            })
            router.visit(_current.route, {
                method: 'delete',
                preserveScroll: true,
                preserveState: _current.state,
                onSuccess: page => {
                    loading.close()
                },
                onFinish: page => {
                    loading.close()
                },

            });
            if (_current.resolve !== null) _current.resolve()
            _current.name = ""
            _current.route = ""
        },
        soft() {return _current.soft},
        cancel() {
            if (_current.reject !== null) _current.reject();
            _current.name = ""
            _current.route = ""
        }
    },
    plugin = {
        install(App, options) {
            App.component("DeleteEntityModal", DeleteEntityModal)
            App.provide("$delete_entity", api)
        }
    }
export default plugin
