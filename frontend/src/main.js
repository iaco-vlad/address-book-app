import './style.css'

import { createApp } from 'vue';
import App from './App.vue';
import HttpClient from './HttpClient.js';
import router from './router.js';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
import BootstrapVueNext from 'bootstrap-vue-next';

const app = createApp(App);
app.config.globalProperties.$http = new HttpClient();
app.use(router);
app.use(BootstrapVueNext);
app.mount('#app');
