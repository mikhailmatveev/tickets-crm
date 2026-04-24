import statuses from '../constants/statuses'
import { createMapper } from './mapper'

const map = {
  [statuses.DONE]: 'Выполнено',
  [statuses.NEW]: 'Новый',
  [statuses.WORKING]: 'В работе'
}

const toTextMap = createMapper(map)

export function toStatusMap () {
  return Object.entries(map).map(([status, text]) => ({ status, text }))
}

export function statusToText (status) {
  return map[status] ?? null
}

export function textToStatus (text) {
  return toTextMap[text] ?? null
}
