import roles from './roles'

export default [
  {
    name: 'Tickets',
    label: 'Тикеты',
    roles: [roles.ADMIN, roles.MANAGER]
  },
  {
    name: 'Statistics',
    label: 'Статистика',
    roles: [roles.ADMIN, roles.MANAGER]
  },
  {
    name: 'Users',
    label: 'Пользователи',
    roles: [roles.ADMIN]
  }
]
