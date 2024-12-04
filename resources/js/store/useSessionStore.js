import { defineStore } from "pinia";
import api from "../axios";
import Router from "../router/router";

export const useSessionStore = defineStore("session", {
    state: () => ({
        name   : null,
        uType  : null,
        isApprover : null,
        uAccess: null,
        error  : null
    }),
    actions: {
        checkSession(){
            api.get('check_access').then((result)=>{
                let data = result.data;
                console.log('useSessionStore', data)
                this.uType      = data.rfq_type
                this.uAccess    = data.rfq_access
                this.name       = data.rapidx_name
                this.isApprover = data.rfq_approver
            }).catch((err) => {
                console.log(err);
            });
        },
        resetStore() {
            this.$reset();
        },
    },
    getters: {
        getType(){
            return this.type;
        }
    },
    persist: true,
});
