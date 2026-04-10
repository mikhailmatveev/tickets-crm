import axios from 'axios'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// Настройка Axios для отправки AJAX-запросов
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Инициализация Vue и подключение маршрутизации
const app = createApp(App);
app.use(router);
app.use(store);
app.mount('#app');
