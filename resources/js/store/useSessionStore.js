import { defineStore } from "pinia";
import api from "../axios";
import Router from "../router/router";

export const useSessionStore = defineStore("session", {
    state: () => ({
        name   : null,
        uType  : null,
        isAuth : null,
        uAccess: null,
        error  : null
    }),
    actions: {
        checkSession(){
            api.get('check_access').then((result)=>{
                let data = result.data;
                console.log('useSessionStore', data)
                this.uType   = data.user_type
                this.uAccess = data.user_access
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
