<template>
  <h1>Пользователи</h1>
  <user-item
    v-for="(item) in users"
    :key="item.id"
    :roles="roles"
    :user="item"
  />
</template>

<script>
import UserItem from './components/UserItem.vue'
import http from '../services/http'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Users',
  components: {
    UserItem
  },
  data () {
    return {
      roles: [],
      users: []
    }
  },
  mounted () {
    this.getRoles()
    this.getUsers()
  },
  methods: {
    async getRoles () {
      try {
        const response = await http.getRoles()
        this.roles = response.data
      } catch (e) {
        console.log(e)
      }
    },
    async getUsers () {
      try {
        const response = await http.getUsers()
        this.users = response.data
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>

<style scoped lang="scss">

</style>
