require('./bootstrap');
import { createApp } from 'vue'
import AppTemplate from './layout/index.vue';

import router from './router/router.js'

createApp(AppTemplate)
.use(router)
.mount('#app');
