<template>
  <dialog :open="isOpen">
    <article>
      <h2>
        Добавить пользователя
      </h2>
      <form v-if="mode === 'form'">
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
              :aria-invalid="validationErrors.name !== undefined ? true : undefined"
            >
            <small v-if="validationErrors.name">
              {{ validationErrors.name }}
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
              :aria-invalid="validationErrors.email !== undefined ? true : undefined"
            >
            <small v-if="validationErrors.email">
              {{ validationErrors.email }}
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
              :aria-invalid="validationErrors.password !== undefined ? true : undefined"
            >
            <small v-if="validationErrors.password">
              {{ validationErrors.password }}
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
      <p v-if="mode === 'error'">
        {{ error }}
      </p>
      <footer>
        <button
          role="button"
          :disabled="fetchingCreateUser"
          @click="submit"
        >
          OK
        </button>
        <button
          v-if="mode !== 'error'"
          role="button"
          class="secondary"
          @click="cancel"
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
import { getMessage } from '../../helpers/apiErrors'
import { getMessages } from '../../helpers/validationErrors'
import { toRoleMap } from './../../helpers/roleMapper'

export default {
  name: 'AddUserModal',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    'add-user-submit',
    'add-user-cancel'
  ],
  data () {
    return {
      error: null,
      fetchingCreateUser: false,
      mode: 'form',
      user: this.initUser(),
      validationErrors: {}
    }
  },
  computed: {
    roles () {
      return toRoleMap()
    }
  },
  methods: {
    async createUser () {
      this.fetchingCreateUser = true
      try {
        // Сброс ошибок, если были ранее
        this.resetErrors()
        const response = await http.createUser(this.user)
        // При успешном создании пользователя нужно сбросить поля формы
        this.resetForm()
        this.onAddModalSubmit(response.data)
      } catch (e) {
        if (e.response && e.response.status) {
          if (e.response.status === 422) {
            this.validationErrors = getMessages(e)
          } else {
            this.error = getMessage(e)
            this.mode = 'error'
          }
        } else {
          this.error = 'Ошибка выполнения запроса'
          this.mode = 'error'
        }
      } finally {
        this.fetchingCreateUser = false
      }
    },
    cancel () {
      this.resetErrors()
      this.resetForm()
      this.onAddModalCancel()
    },
    initUser () {
      return {
        name: '',
        email: '',
        password: '',
        role: roleConstants.MANAGER
      }
    },
    async submit () {
      switch (this.mode) {
        case 'form': await this.createUser()
          break
        case 'error': this.cancel()
          break
        default: this.cancel()
      }
    },
    onAddModalSubmit (user) {
      this.$emit('add-user-submit', user)
    },
    onAddModalCancel () {
      this.$emit('add-user-cancel')
    },
    resetErrors () {
      this.mode = 'form'
      this.error = null
      this.validationErrors = {}
    },
    resetForm () {
      this.user = this.initUser()
    }
  }
}
</script>

<style scoped lang="scss">

</style>
