import {createRouter, createWebHistory} from 'vue-router';
// Layout
import AdminLayout from '../layout/AdminLayout/AdminLayout.vue';
// Pages
import Dashboard from '../pages/Dashboard.vue';
import UserManagement from '../pages/UserManagement.vue';

// Interceptors
import Forbidden from '../pages/interceptors/403.vue';

import api from '../axios';

const isLoggedIn = async () => { // * TO VALIDATE IF SESSION STILL EXIST
    await api.get('check_user').then((result) => {
        if(result.data == 1){
            return true;
        }
        else{
            return window.location.href = '/RapidX';
        }
    });
}

const routes = [
    {
        path: "/RFQv4/",
        component: AdminLayout,
        beforeEnter: isLoggedIn,
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: Dashboard
            },
            {
                path: 'user',
                name: 'UserManagement',
                component: UserManagement
            },
        ],
    },
    {
        path: "/RFQv4/",
        // component: AdminLayout,
        // beforeEnter: isLoggedIn,
        children: [
            {
                path: '403',
                name: '403',
                component: Forbidden
            },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
