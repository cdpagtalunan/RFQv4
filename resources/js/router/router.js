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
            {
                path: 'category',
                name: 'CategoryManagement',
                component: CategoryManagement
            },
            {
                path: 'currency',
                name: 'CurrencyManagement',
                component: CurrencyManagement
            },
            {
                path: 'uom',
                name: 'UomManagement',
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
                component: LogisticsPurchasing
            },
            {
                path: 'logistics_head',
                name: 'LogisticsHead',
                component: LogisticsHead
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
