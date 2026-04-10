<template>
  <header class="header">
    <div class="container-fluid">
      <nav>
        <ul>
          <li>
            <strong>Tickets CRM</strong>
          </li>
        </ul>
        <ul>
          <li>
            <span class="logout-container">
              {{ userName }}
              <a
                href="#"
                @click.prevent="logout"
              >
                <img
                  src="../../../../img/header/logout.svg"
                  alt="logout"
                >
              </a>
            </span>
          </li>
        </ul>
      </nav>
    </div>
  </header>
</template>

<script>
import {mapGetters} from 'vuex'
import http from '../../../services/http'

export default {
  name: 'TheHeader',
  computed: {
    ...mapGetters({
      userName: 'user/getUserName'
    })
  },
  methods: {
    /**
     * Разлогинивает пользователя
     */
    async logout() {
      try {
        await http.logout()
        window.location.href = '/'
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>

<style scoped lang="scss">
.header {
  border-bottom: 1px #333 solid;
}
</style>
