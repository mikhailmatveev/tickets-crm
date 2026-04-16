<template>
  <dialog :open="isModalOpen">
    <article>
      <h2>
        Добавить пользователя
      </h2>
      <form>
        <fieldset>
          <label>
            Имя
            <input
              v-model="user.name"
              type="text"
              name="name"
              placeholder="Иван Иванов"
              aria-label="Имя"
              autocomplete="name"
              required
              :aria-invalid="error !== null ? true : null"
            >
            <small v-if="error">
              {{ error }}
            </small>
          </label>
          <label>
            E-mail
            <input
              v-model="user.email"
              type="email"
              name="email"
              placeholder="mail@example.com"
              aria-label="E-mail"
              autocomplete="email"
              required
              :aria-invalid="error !== null ? true : null"
            >
            <small v-if="error">
              {{ error }}
            </small>
          </label>
          <label>
            Пароль
            <input
              v-model="user.password"
              type="password"
              name="password"
              placeholder="Пароль"
              aria-label="Пароль"
              required
              :aria-invalid="error !== null ? true : null"
            >
            <small v-if="error">
              {{ error }}
            </small>
          </label>
          <label>
            Роль
            <template
              v-for="item in roles"
              :key="item.role"
            >
              <label>
                <input
                  v-model="user.role"
                  type="radio"
                  name="role"
                  :value="item.role"
                >
                {{ item.text }}
              </label>
            </template>
          </label>
        </fieldset>
      </form>
      <footer>
        <button
          role="button"
          @click="handleSubmit"
        >
          OK
        </button>
        <button
          role="button"
          class="secondary"
          @click="handleCancel"
        >
          Отмена
        </button>
      </footer>
    </article>
  </dialog>
</template>

<script>
import http from './../../services/http'
import roleConstants from './../../constants/roles'
import { toRoleMap } from './../../helpers/roleMapper'

export default {
  name: 'AddUserModal',
  props: {
    isModalOpen: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      error: null,
      fetchingCreateUser: false,
      user: {
        name: '',
        email: '',
        password: '',
        role: roleConstants.MANAGER
      }
    }
  },
  computed: {
    roles () {
      return toRoleMap()
    }
  },
  methods: {
    async handleSubmit () {
      this.fetchingCreateUser = true
      try {
        const response = await http.createUser(this.user)
        this.$emit('add-user-submit', response.data)
      } catch (e) {
        console.error(e)
      } finally {
        this.fetchingCreateUser = false
      }
    },
    handleCancel () {
      this.$emit('add-user-cancel')
    }
  }
}
</script>

<style scoped lang="scss">

</style>
