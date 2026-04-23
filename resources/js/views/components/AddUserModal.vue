<template>
  <dialog :open="isOpen">
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
      <footer>
        <button
          role="button"
          @click="onAddModalSubmit"
        >
          OK
        </button>
        <button
          role="button"
          class="secondary"
          @click="onAddModalCancel"
        >
          Отмена
        </button>
      </footer>
    </article>
  </dialog>
  <modal
    header-text="Ошибка при создании пользователя"
    :is-open="error !== null"
    :body-text="error"
    @submit="onModalSubmit"
  />
</template>

<script>
import http from './../../services/http'
import roleConstants from './../../constants/roles'
import { getMessage } from '../../helpers/apiErrors'
import { getMessages } from '../../helpers/validationErrors'
import { toRoleMap } from './../../helpers/roleMapper'
import Modal from './Modal.vue'

export default {
  name: 'AddUserModal',
  components: {
    Modal
  },
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
      user: {
        name: '',
        email: '',
        password: '',
        role: roleConstants.MANAGER
      },
      validationErrors: {}
    }
  },
  computed: {
    roles () {
      return toRoleMap()
    }
  },
  methods: {
    async onAddModalSubmit () {
      this.fetchingCreateUser = true
      try {
        // Сброс ошибок, если были ранее
        this.resetErrors()
        const response = await http.createUser(this.user)
        this.$emit('add-user-submit', response.data)
      } catch (e) {
        if (e.response && e.response.status) {
          if (e.response.status === 422) {
            this.validationErrors = getMessages(e)
          } else {
            this.error = getMessage(e)
          }
        } else {
          this.error = 'Ошибка выполнения запроса'
        }
      } finally {
        this.fetchingCreateUser = false
      }
    },
    onAddModalCancel () {
      this.$emit('add-user-cancel')
    },
    onModalSubmit () {
      this.resetErrors()
    },
    resetErrors () {
      this.error = null
      this.validationErrors = {}
    }
  }
}
</script>

<style scoped lang="scss">

</style>
