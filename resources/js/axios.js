import Axios from 'axios';
import Router from './router/router';

const axios = Axios.create()
axios.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        if(error.response && error.response.status === 401){
            console.log('401');
        }

        if(error.response && error.response.status === 403){
            Router.push({name : '403'});
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