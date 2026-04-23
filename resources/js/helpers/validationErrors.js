export function getMessages (error) {
  if (error?.response?.status !== 422) return {}

  const errors = error?.response?.data?.errors
  if (!errors || typeof errors !== 'object') return {}

  return Object.fromEntries(
    Object.entries(errors).map(([field, messages]) => [
      field,
      Array.isArray(messages)
        ? messages[0]
        : messages
    ])
  )
}
