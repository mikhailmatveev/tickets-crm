import { createRouter, createWebHistory } from 'vue-router'
import roles from '../constants/roles'
import store from '../store'

const routes = [
  {
    path: '/',
    component: () => import(/* webpackChunkName: "home" */ '../views/PageTemplate.vue'),
    children: [
      {
        path: '',
        name: 'Tickets',
        component: () => import(/* webpackChunkName: "home" */ '../views/Tickets.vue'),
        meta: {
          title: 'Тикеты',
          requiresAuth: true,
          roles: [roles.ADMIN, roles.MANAGER]
        }
      }, {
        path: '/statistics',
        name: 'Statistics',
        component: () => import(/* webpackChunkName: "home" */ '../views/Statistics.vue'),
        meta: {
          title: 'Статистика',
          requiresAuth: true,
          roles: [roles.ADMIN, roles.MANAGER]
        }
      }, {
        path: 'users',
        name: 'Users',
        component: () => import(/* webpackChunkName: "home" */ '../views/Users.vue'),
        meta: {
          title: 'Пользователи',
          requiresAuth: true,
          roles: [roles.ADMIN]
        }
      }
    ]
  }, {
    path: '/login',
    name: 'Login',
    component: () => import(/* webpackChunkName: "home" */ '../views/Login.vue'),
    meta: {
      title: 'Войти',
      guestOnly: true
    }
  }, {
    path: '/404',
    name: 'NotFound',
    component: () => import(/* webpackChunkName: "not-found" */ '../views/NotFound.vue'),
    meta: {
      title: '404'
    }
  }, {
    path: '/:pathMatch(.*)*',
    redirect: { name: 'NotFound' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  linkActiveClass: 'active',
  routes
})

router.beforeEach(async (to) => {
  if (!store.state.user.user) {
    try {
      await store.dispatch('user/fetchUser')
    } catch (e) {
      console.error(e)
    }
  }

  const isAuthenticated = store.getters['user/isAuthenticated']
  const userRole = store.getters['user/getUserRole']

  if (to.meta.requiresAuth && !isAuthenticated) {
    return {
      name: 'Login',
      query: { redirect: to.fullPath }
    }
  }

  if (to.meta.guestOnly && isAuthenticated) {
    return { name: 'Tickets' }
  }

  // Проверка роли для доступа к страницам
  if (to.meta.roles && !to.meta.roles.includes(userRole)) {
    return { name: 'NotFound' }
  }
})

export default router
