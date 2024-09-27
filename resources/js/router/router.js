import {createRouter, createWebHistory} from 'vue-router';
import Dashboard from '../pages/Dashboard.vue';

const routes = [
    {
        path: "/RFQv4/",
        // component: AdminLayout,
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: Dashboard
            },
        ]
    },
    // {
    //     path: '/ARRS/',
    //     // path: '/ARRS_rev/',
    //     component: '',
    //     children:  [
    //         {
    //             path: 'unauthorized',
    //             name: 'Unauthorized',
    //             component: Unauthorized
    //         },
    //         {
    //             path: 'decrypt',
    //             name: 'Decrypt',
    //             component: Decrypt
    //         },
    //     ]
    // },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


export default router;
