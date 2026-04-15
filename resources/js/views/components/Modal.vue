<template>
  <dialog :open="isModalOpen">
    <article>
      <h2 v-show="headerText && headerText.length > 0">
        {{ headerText }}
      </h2>
      <p v-show="bodyText && bodyText.length > 0">
        {{ bodyText }}
      </p>
      <footer>
        <button
          v-if="showSubmit"
          role="button"
          @click="handleSubmit"
        >
          {{ submitText }}
        </button>
        <button
          v-if="showCancel"
          role="button"
          class="secondary"
          @click="handleCancel"
        >
          {{ cancelText }}
        </button>
      </footer>
    </article>
  </dialog>
</template>

<script>
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Modal',
  props: {
    headerText: {
      type: String,
      default: ''
    },
    bodyText: {
      type: String,
      default: ''
    },
    isOpen: {
      type: Boolean,
      default: false
    },
    showSubmit: {
      type: Boolean,
      default: true
    },
    submitText: {
      type: String,
      default: 'OK'
    },
    showCancel: {
      type: Boolean,
      default: false
    },
    cancelText: {
      type: String,
      default: 'Отмена'
    }
  },
  computed: {
    isModalOpen() {
      return this.isOpen
    }
  },
  methods: {
    handleSubmit() {
      this.$emit('submit');
    },
    handleCancel() {
      this.$emit('close');
    }
  }
}
</script>

<style scoped lang="scss">

</style>
