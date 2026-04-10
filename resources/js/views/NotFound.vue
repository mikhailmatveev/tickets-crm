<template>
  <div class="flex-wrapper">
    <div>
      <hgroup>
        <h1>404</h1>
        <h3>Страница не найдена</h3>
        <p>Запрошенный адрес не существует или был перемещен</p>
      </hgroup>
      <router-link :to="{ name: 'Tickets' }">
        На главную
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
}
</script>

<style scoped lang="scss">
.flex-wrapper {
  display: flex;
  height: 100%;
  align-items: center;
  justify-content: center;
}

hgroup h1 {
  margin-bottom: 36px;
}
</style>
