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
                @change="selectRole(item)"
              >
              {{ item.name }}
            </label>
          </li>
        </ul>
      </details>
      <button type="submit">
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
      displayRole: ''
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
    selectRole (role) {
      // Обновляем роль через апи-запрос
      this.updateRole (role.id)
      this.setDisplayRole(role.name)
    },
    setDisplayRole (roleName) {
      this.displayRole = roleName
    },
    async updateRole (roleId) {
      try {
        const response = await http.updateUserRole(this.user.id,
          { role_id: roleId }
        )
        console.log(response.data)
      } catch (e) {
        console.error(e)
      }
    }
  }
}
</script>

<style scoped lang="scss">

</style>
