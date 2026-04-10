<template>
  <aside
    class="sidebar"
  >
    <div class="container-fluid">
      <nav>
        <ul>
          <li
            v-for="item in availableNavigation"
            :key="item.name"
          >
            <router-link :to="{ name: item.name }">{{ item.label }}</router-link>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script>
import navigation from '../../../constants/navigation.js'

export default {
  name: 'TheSidebar',
  computed: {
    availableNavigation () {
      const role = this.$store.getters['user/getUserRole']

      return navigation.filter(item => item.roles.includes(role))
    }
  }
}
</script>

<style scoped lang="scss">
.sidebar {
  padding: 30px 0;
}
</style>
