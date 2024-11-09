import { reactive } from "vue"
import DeleteEntityModal from "./Modal.vue"
import {router} from "@inertiajs/vue3";

const
    _current = reactive({name:"",resolve:null,reject:null, route: ""}),
    api = {
        active() {return _current.name;},
        show(route, name = 'entity') {
            _current.name = name;
            _current.route = route;
            return new Promise(
                (resolve = null, reject = null) => {
                    _current.resolve = resolve;
                    _current.reject = reject;
                })
        },
        accept() {
            router.visit(_current.route, {
                method: 'delete',
                preserveScroll: true,
                preserveState: false,
            });
            if (_current.resolve !== null) _current.resolve()
            _current.name = ""
            _current.route = ""
        },
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
