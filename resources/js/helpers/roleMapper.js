import roles from '../constants/roles'
import { createMapper } from './mapper'

const map = {
  [roles.ADMIN]: 'Администратор',
  [roles.MANAGER]: 'Менеджер'
}

const toTextMap = createMapper(map)

export function toRoleMap () {
  return Object.entries(map).map(([role, text]) => ({ role, text }))
}

export function roleToText (role) {
  return map[role] ?? null
}

export function textToRole (text) {
  return toTextMap[text] ?? null
}
