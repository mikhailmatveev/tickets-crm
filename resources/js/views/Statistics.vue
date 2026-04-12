<template>
  <h1>Статистика</h1>
  <table>
    <thead>
      <tr>
        <th>Имя пользователя</th>
        <th>Роль</th>
        <th>Выполнено тикетов</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="(item) in statistics"
        :key="item.id"
      >
        <td>{{ item.name }}</td>
        <td>{{ item.role }}</td>
        <td>{{ item.tickets_done }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import http from '../services/http'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Statistics',
  data() {
    return {
      statistics: []
    }
  },
  mounted() {
    this.getStatistics()
  },
  methods: {
    async getStatistics() {
      try {
        const response = await http.getStatistics()
        this.statistics = response.data
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>

<style scoped lang="scss">

</style>
