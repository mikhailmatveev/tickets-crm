<template>
  <h1>Статистика</h1>
  <div
    role="group"
    class="date-filers"
  >
    <button @click="selectPeriod('day')">
      За день
    </button>
    <button @click="selectPeriod('week')">
      За неделю
    </button>
    <button @click="selectPeriod('month')">
      За месяц
    </button>
  </div>
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
      periodSelected: 'day',
      statistics: []
    }
  },
  mounted() {
    this.getStatistics(this.periodSelected)
  },
  methods: {
    async getStatistics() {
      try {
        const response = await http.getStatistics(this.periodSelected)
        this.statistics = response.data
      } catch (e) {
        console.log(e)
      }
    },
    selectPeriod(period) {
      this.periodSelected = period
      this.getStatistics()
    }
  }
}
</script>

<style scoped lang="scss">
.date-filers {
  margin-top: 50px;
  max-width: 540px;
}

table {
  margin-top: 30px;
}
</style>
