import {createRouter, createWebHistory} from 'vue-router';
// Layout
import AdminLayout from '../layout/AdminLayout/AdminLayout.vue';
// Pages
import Dashboard from '../pages/Dashboard.vue';
import UserManagement from '../pages/UserManagement.vue';
import CategoryManagement from '../pages/CategoryManagement.vue';
import CurrencyManagement from '../pages/CurrencyManagement.vue';
import UomManagement from '../pages/UomManagement.vue';
import Request from '../pages/Requestor.vue';
import LogisticsPurchasing from '../pages/LogisticsPurchasing.vue';
import LogisticsHead from '../pages/LogisticsHead.vue';
import Mailer from '../pages/Mailer.vue';
import Export from '../pages/Export.vue';

// Interceptors
import Forbidden from '../pages/interceptors/403.vue';

import api from '../axios';
import { useSessionStore } from '../store/index';



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

const checkUser = () => {
    const localStore = useSessionStore();
    if(localStore.uAccess == 0 && localStore.uType == 0){
        router.push({ name: 'Request' });
    }
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
                beforeEnter: checkUser,
                component: Dashboard
            },
            {
                path: 'user',
                name: 'UserManagement',
                beforeEnter: checkUser,
                component: UserManagement
            },
            {
                path: 'category',
                name: 'CategoryManagement',
                beforeEnter: checkUser,
                component: CategoryManagement
            },
            {
                path: 'currency',
                name: 'CurrencyManagement',
                beforeEnter: checkUser,
                component: CurrencyManagement
            },
            {
                path: 'uom',
                name: 'UomManagement',
                beforeEnter: checkUser,
                component: UomManagement
            },
            {
                path: 'request',
                name: 'Request',
                component: Request
            },
            {
                path: 'logistics_purch',
                name: 'LogisticsPurchasing',
                beforeEnter: checkUser,
                component: LogisticsPurchasing
            },
            {
                path: 'logistics_head',
                name: 'LogisticsHead',
                beforeEnter: checkUser,
                component: LogisticsHead
            },
            {
                path: 'export',
                name: 'Export',
                // beforeEnter: checkUser,
                component: Export
            },
        ],
    },
    {
        path: "/RFQv4/",
        children: [
            {
                path: '403',
                name: '403',
                component: Forbidden
            },
        ]
    },
    {
        path: '/RFQv4/mailer/mail',
        name: 'mail',
        component: Mailer,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
