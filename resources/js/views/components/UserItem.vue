<template>
  <form>
    <fieldset class="grid">
      <div role="group">
        <input
          type="text"
          :value="user.name"
          readonly
        >
      </div>
      <div role="group">
        <input
          type="email"
          :value="user.email"
          readonly
        >
      </div>
      <div role="group">
        <input
          type="password"
          :value="user.email"
          readonly
        >
      </div>
      <details class="dropdown">
        <summary>{{ displayRole }}</summary>
        <ul>
          <li
            v-for="item in roles"
            :key="item.id"
          >
            <label>
              <input
                type="radio"
                name="role"
                :value="item.name"
                :checked="user.role?.id === item.id"
                :disabled="fetchingUpdateRole"
                @change="selectRole(item)"
              >
              {{ item.name }}
            </label>
          </li>
        </ul>
      </details>
      <button
        type="button"
        :disabled="fetchingDelete"
        @click="deleteUser(user.id)"
      >
        <i class="fa fa-trash" />
        Удалить
      </button>
    </fieldset>
  </form>
</template>

<script>
import { mapGetters } from 'vuex'
import http from '../../services/http'

export default {
  name: 'UserItem',
  props: {
    user: {
      type: Object,
      default() {
        return {
          id: 0,
          name: '',
          email: '',
          role: {}
        }
      }
    }
  },
  data () {
    return {
      displayRole: '',
      fetchingDelete: false,
      fetchingUpdateRole: false
    }
  },
  computed: {
    ...mapGetters('roles', ['getRoles']),
    roles () {
      return this.getRoles
    }
  },
  mounted () {
    this.displayRole = this.user.role.name || ''
  },
  methods: {
    async selectRole (role) {
      // Обновляем роль через апи-запрос
      const response = await this.updateRole (role.id)
      if (response) {
        this.setDisplayRole (role.name)
      }
    },
    setDisplayRole (roleName) {
      this.displayRole = roleName
    },
    async deleteUser (id) {
      this.fetchingDelete = true
      try {
        await http.deleteUser(id)
        // Отправляем событие в родительский компонент,
        // чтобы удалить запись без дополнительных запросов
        this.$emit('user-deleted', id)
      } catch (e) {
        console.error(e)
      } finally {
        this.fetchingDelete = false
      }
    },
    async updateRole (roleId) {
      this.fetchingUpdateRole = true
      try {
        const response = await http.updateUserRole(this.user.id,
          { role_id: roleId }
        )
        return response
      } catch (e) {
        console.error(e)
        return null
      } finally {
        this.fetchingUpdateRole = false
      }
    }
  }
}
</script>

<style scoped lang="scss">

</style>
