<template>
  <div class="flex-wrapper">
    <form @submit.prevent="login">
      <fieldset>
        <label>
          Ваш E-mail
          <input
            v-model="email"
            type="email"
            name="email"
            placeholder="mail@example.com"
            aria-label="Ваш E-mail"
            autocomplete="email"
            required
            :aria-invalid="validationErrors.email !== undefined ? true : undefined"
          >
          <small v-if="validationErrors.email">
            {{ validationErrors.email }}
          </small>
        </label>
        <label>
          Введите пароль
          <input
            v-model="password"
            type="password"
            name="password"
            placeholder="Введите пароль"
            aria-label="Введите пароль"
            required
            :aria-invalid="validationErrors.password !== undefined ? true : undefined"
          >
          <small v-if="validationErrors.password">
            {{ validationErrors.password }}
          </small>
        </label>
      </fieldset>
      <button type="submit">
        Войти
      </button>
    </form>
  </div>
  <modal
    header-text="Ошибка входа"
    :is-open="error !== null"
    :body-text="error"
    @submit="onModalSubmit"
  />
</template>

<script>
import { getMessage } from '../helpers/apiErrors'
import { getMessages } from '../helpers/validationErrors'
import http from '../services/http'
import Modal from './components/Modal.vue'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Login',
  components: {
    Modal
  },
  data () {
    return {
      email: '',
      password: '',
      error: null,
      validationErrors: {}
    }
  },
  methods: {
    async login () {
      try {
        // Сброс ошибок, если были ранее
        this.resetErrors()
        // Валидация сессии
        await http.getCookie()
        // Авторизация
        await http.login(this.email, this.password)
        // Получение объекта user
        await this.$store.dispatch('user/fetchUser')
        // Логика редиректа на последнюю просмотренную страницу, либо на главную
        const redirect = this.$route.query.redirect || '/'
        // Редирект
        await this.$router.push(redirect)
      } catch (e) {
        if (e.response && e.response.status) {
          if (e.response.status === 422) {
            this.validationErrors = getMessages(e)
          } else {
            this.error = getMessage(e)
          }
        } else {
          this.error = 'Ошибка входа'
        }
      }
    },
    resetErrors () {
      this.error = null
      this.validationErrors = {}
    },
    onModalSubmit () {
      this.resetErrors()
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
