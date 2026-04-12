<template>
  <h1>Тикеты</h1>
  <table>
    <thead>
      <tr>
        <th>Тема</th>
        <th>Имя клиента</th>
        <th>E-mail клиента</th>
        <th>Телефон клиента</th>
        <th>Статус</th>
        <th>Дата ответа менеджера</th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="(item) in tickets"
        :key="item.id"
      >
        <td>{{ item.subject }}</td>
        <td>{{ item.customer.name }}</td>
        <td>{{ item.customer.email }}</td>
        <td>{{ item.customer.phone }}</td>
        <td>{{ item.status }}</td>
        <td>{{ formatDate(item.manager_replied_at) }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import http from '../services/http'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Tickets',
  data () {
    return {
      tickets: []
    }
  },
  mounted () {
    this.getTickets()
  },
  methods: {
    formatDate (date) {
      if (!date) {
        return null
      }
      const d = new Date(date)
      return d.toLocaleString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    },
    async getTickets () {
      try {
        const response = await http.getTickets()
        this.tickets = response.data
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>
