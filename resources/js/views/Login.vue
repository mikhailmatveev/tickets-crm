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
            :aria-invalid="error !== null ? true : null"
          >
          <small v-if="error">
            {{ error }}
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
            :aria-invalid="error !== null ? true : null"
          >
          <small v-if="error">
            {{ error }}
          </small>
        </label>
      </fieldset>
      <button type="submit">
        Войти
      </button>
    </form>
  </div>
</template>

<script>
import http from '../services/http'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Login',
  data () {
    return {
      email: '',
      password: '',
      error: null
    }
  },
  methods: {
    async login () {
      try {
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
        if (e.response?.data?.message) {
          this.error = e.response.data.message
        } else {
          this.error = 'Ошибка входа'
        }
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
