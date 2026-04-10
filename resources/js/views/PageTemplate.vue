<template>
  <the-header />
  <div class="flex-wrapper">
    <the-sidebar />
    <main>
      <div class="container-fluid">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script>
import TheHeader from './components/layout/TheHeader.vue'
import TheSidebar from './components/layout/TheSidebar.vue'

export default {
  name: 'PageTemplate',
  components: {
    TheHeader,
    TheSidebar
  }
}
</script>

<style scoped lang="scss">
.flex-wrapper {
  display: flex;
  height: 100%;
  .sidebar {
    width: 280px;
    flex-shrink: 0;
    overflow-y: auto; /* Прокрутка, если меню длинное */
    & + main {
      display: flex;
      padding: 30px 0;
      flex-direction: column;
      flex-grow: 1; /* Занимает всю оставшуюся ширину */
      overflow: hidden;
    }
  }
}
</style>
