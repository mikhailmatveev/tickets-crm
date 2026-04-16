import roles from '../constants/roles'

const map = {
  [roles.ADMIN]: 'Администратор',
  [roles.MANAGER]: 'Менеджер'
}

const toTextMap = Object.fromEntries(
  Object.entries(map).map(([role, text]) => [text, role])
)

export function toRoleMap () {
  return Object.entries(map).map(([role, text]) => ({ role, text }))
}

export function roleToText (role) {
  return map[role] ?? null
}

export function textToRole (text) {
  return toTextMap[text] ?? null
}
