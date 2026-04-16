<template>
  <h1>Пользователи</h1>
  <user-item
    v-for="(item) in users"
    :key="item.id"
    :user="item"
    @user-deleted="onUserDeleted"
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
      users: []
    }
  },
  mounted () {
    this.getUsers()
  },
  methods: {
    async getUsers () {
      try {
        const response = await http.getUsers()
        this.users = response.data
      } catch (e) {
        console.log(e)
      }
    },
    onUserDeleted (id) {
      this.users = this.users.filter(user => user.id !== id)
    }
  }
}
</script>

<style scoped lang="scss">

</style>
