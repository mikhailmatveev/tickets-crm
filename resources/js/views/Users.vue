<template>
  <h1>Пользователи</h1>
  <button
    type="button"
    @click="openModal"
  >
    Добавить пользователя
  </button>
  <user-item
    v-for="(item) in users"
    :key="item.id"
    :user="item"
    @user-deleted="onUserDeleted"
  />
  <add-user-modal
    :is-open="isModalOpen"
    @add-user-submit="onUserCreateSubmit"
    @add-user-cancel="onUserCreateCancel"
  />
</template>

<script>
import UserItem from './components/UserItem.vue'
import AddUserModal from './components/AddUserModal.vue'
import http from '../services/http'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Users',
  components: {
    UserItem,
    AddUserModal
  },
  data () {
    return {
      isModalOpen: false,
      users: []
    }
  },
  mounted () {
    this.getUsers()
  },
  methods: {
    openModal () {
      if (!this.isModalOpen) {
        this.isModalOpen = true
      }
    },
    closeModal () {
      if (this.isModalOpen) {
        this.isModalOpen = false
      }
    },
    async getUsers () {
      try {
        const response = await http.getUsers()
        this.users = response.data
      } catch (e) {
        console.log(e)
      }
    },
    onUserCreateSubmit (user) {
      // Добавляем нового пользователя в модель
      this.users.push(user)
      this.closeModal()
    },
    onUserCreateCancel () {
      this.closeModal()
    },
    onUserDeleted (id) {
      this.users = this.users.filter(user => user.id !== id)
    }
  }
}
</script>

<style scoped lang="scss">

</style>
