import Axios from 'axios';
import Router from './router/router';
import { inject } from 'vue';

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
const axios = Axios.create()
axios.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        if(error.response && error.response.status === 401){
            Router.push({name : '403'});

            console.log('401');
        }

        if(error.response && error.response.status === 403){
            Toast.fire({
                icon: "error",
                title: 'You are not allowed to make this transaction!'
            });
            console.log('403');
        }

        if(error.response && error.response.status === 404){
            console.log('404');
        }
        if(error.response && error.response.status === 405){
            console.log('405');
        }
        if(error.response && error.response.status === 302){
            console.log('302');
        }
        return Promise.reject(error)
    }
)

const api = {... axios}
// api.defaults.baseURL = import.meta.env.VITE_APP_LARAVEL_SERVER
// api.defaults.baseURL = 'http://rapidx/ARRS_rev/';
// api.defaults.withCredentials = true;
api.defaults.baseURL = 'http://rapidx/RFQv4/';
// api.get('/csrf-cookie')

export default api