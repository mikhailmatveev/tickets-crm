<template>
  <details>
    <summary role="button">
      {{ formatDate(createdAt) }}
    </summary>
    <blockquote>
      {{ text }}
    </blockquote>
  </details>
</template>

<script>
import { formatDate } from '../../../mixins/helper'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Reply',
  props: {
    createdAt: {
      type: String,
      required: true
    },
    text: {
      type: String,
      required: true
    }
  },
  methods: {
    formatDate
  }
}
</script>

<style scoped lang="scss">

</style>
