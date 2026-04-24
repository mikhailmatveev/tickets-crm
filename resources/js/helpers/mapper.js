export function createMapper (map, keyName = 'value') {
  const reverse = Object.fromEntries(
    Object.entries(map).map(([value, text]) => [text, value])
  )

  return {
    toOptions () {
      return Object.entries(map).map(([value, text]) => ({ [keyName]: value, text }))
    },
    toText (value) {
      return map[value] ?? null
    },
    fromText (text) {
      return reverse[text] ?? null
    }
  }
}
