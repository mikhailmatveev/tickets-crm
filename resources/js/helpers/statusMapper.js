import statuses from '../constants/statuses'

const map = {
  [statuses.DONE]: 'Выполнено',
  [statuses.NEW]: 'Новый',
  [statuses.WORKING]: 'В работе'
}

const toTextMap = Object.fromEntries(
  Object.entries(map).map(([status, text]) => [text, status])
)

export function toStatusMap () {
  return Object.entries(map).map(([status, text]) => ({ status, text }))
}

export function statusToText (status) {
  return map[status] ?? null
}

export function textToStatus (text) {
  return toTextMap[text] ?? null
}
