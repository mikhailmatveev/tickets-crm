<template>
  <div class="flex-wrapper">
    <div class="verify-email">
      <h2>Подтвердите ваш email</h2>
      <p>Письмо со ссылкой для подтверждения отправлено на <strong>{{ user.email }}</strong></p>
      <p>Если письмо не пришло — запросите повторную отправку.</p>

      <button
        :disabled="loading || sent"
        @click="resend"
      >
        {{ loading ? 'Отправляем...' : sent ? 'Письмо отправлено' : 'Отправить повторно' }}
      </button>

      <p
        v-if="error"
        class="error"
      >
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import http from '../../services/http'
import router from '../../router'
import store from '../../store'

export default {
  name: 'VerifyEmail',

  data() {
    return {
      loading: false,
      sent: false,
      error: null
    }
  },

  computed: {
    ...mapState('user', ['user'])
  },

  async mounted() {
    await store.dispatch('user/fetchUser')
    if (store.state.user.user?.email_verified_at) {
      router.push({name: 'Tickets'})
    }
  },

  methods: {
    async resend() {
      this.loading = true
      this.error = null
      try {
        await http.resendEmail();
        this.sent = true
      } catch (e) {
        if (e.response?.status === 429) {
          this.error = 'Слишком много запросов. Попробуйте позже.'
        } else {
          this.error = 'Что-то пошло не так. Попробуйте позже.'
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped lang="scss">
.flex-wrapper {
  display: flex;
  height: 100%;
  align-items: center;
  justify-content: center;
}
</style>
