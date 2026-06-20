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
        path: '/ticket/:id',
        name: 'TicketDetails',
        component: () => import(/* webpackChunkName: "home" */ '../views/TicketDetails.vue'),
        meta: {
          title: 'Информация по тикету',
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
    },
  }, {
    path: '/verify-email',
    name: 'VerifyEmail',
    component: () => import('../views/mail/VerifyEmail.vue'),
    meta: {
      title: 'Подтвердите email',
      requiresAuth: true
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

  // Грузим список ролей, только если пользователь авторизован
  // и стор ещё пустой
  if (isAuthenticated) {
    const roles = store.getters['roles/getRoles']
    if (!roles.length) {
      await store.dispatch('roles/fetchRoles')
    }
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return {
      name: 'Login',
      query: { redirect: to.fullPath }
    }
  }

  // Редирект на верификацию, если email не подтверждён
  const user = store.state.user.user
  if (isAuthenticated && user && !user.email_verified_at && to.name !== 'VerifyEmail') {
    return { name: 'VerifyEmail' }
  }

  if (to.meta.guestOnly && isAuthenticated) {
    return { name: 'Tickets' }
  }

  // Проверка роли для доступа к страницам
  if (to.meta.roles && !to.meta.roles.includes(userRole.name)) {
    return { name: 'NotFound' }
  }
})

export default router
