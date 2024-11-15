require('./bootstrap');
import { createApp } from 'vue'
import AppTemplate from './layout/index.vue';
import { pinia,useSessionStore } from './store/index';
import router from './router/router.js'

/**
    * Using of jquery is not an option unless it is the only solution.
    * on this instance, adminLTE burger menu cant be click unless I install the jQuery.
    * DONT USE jQuery on other functionalities. use only VUE.
*/ 
import $ from 'jquery';
window.jQuery = $;

// * Fontawesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';

library.add(far, fas, fab);

// * Components
import Breadcrumbs from './pages/components/Breadcrumbs.vue'
import Card from './pages/components/Card.vue'
import Modal from './pages/components/Modal.vue'

import VueMultiselect from 'vue-multiselect'

import Swal from 'sweetalert2';
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    iconColor: 'white',
    customClass: {
        popup: 'colored-toast',
    },
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
import Vue3FormWizard from 'vue3-form-wizard'
import 'vue3-form-wizard/dist/style.css'



createApp(AppTemplate)
.use(pinia)
.use(router)
.component('icons', FontAwesomeIcon)
.component('Breadcrumbs', Breadcrumbs)
.component('Card', Card)
.component('Modal', Modal)
.component('VueMultiselect', VueMultiselect)
.provide('Swal',Swal)
.provide('Toast',Toast)
.use(Vue3FormWizard)
.mount('#app');

const store = useSessionStore();
store.checkSession();
