export function getMessage (error, fallback = 'Ошибка запроса') {
  return error?.response?.data?.message || fallback
}
