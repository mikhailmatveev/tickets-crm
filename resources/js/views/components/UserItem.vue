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
            v-for="(item, index) in roles"
            :key="index"
          >
            <label>
              <input
                type="radio"
                name="role"
                :value="item"
                :checked="user.role === item"
                @change="selectRole(item)"
              >
              {{ item }}
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
export default {
  name: 'UserItem',
  props: {
    roles: {
      type: Array,
      default () {
        return []
      }
    },
    user: {
      type: Object,
      default() {
        return {
          name: '',
          email: '',
          role: ''
        }
      }
    }
  },
  data () {
    return {
      displayRole: ''
    }
  },
  mounted () {
    this.displayRole = this.user.role || ''
  },
  methods: {
    selectRole (role) {
      this.displayRole = role
    }
  }
}
</script>

<style scoped lang="scss">

</style>
