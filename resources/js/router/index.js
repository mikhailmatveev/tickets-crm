import {createRouter, createWebHistory} from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import(/* webpackChunkName: "home" */ '../views/Home.vue'),
    meta: {
      title: 'Главная'
    }
  }
]

export default createRouter({
  history: createWebHistory(),
  linkActiveClass: 'active',
  routes
})
