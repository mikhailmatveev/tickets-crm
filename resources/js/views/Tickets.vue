<template>
  <h1>Тикеты</h1>
  <table>
    <thead>
      <tr>
        <th>#</th>
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
        <td>
          <router-link
            :to="{ name: 'TicketDetails', params: { id: item.id }}"
          >
            {{ item.id }}
          </router-link>
        </td>
        <td>
          {{ item.subject }}
        </td>
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
import { formatDate } from '../mixins/helper'
import http from '../services/http'
import TicketDetails from './TicketDetails.vue'

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Tickets',
  data () {
    return {
      tickets: []
    }
  },
  computed: {
    TicketDetails () {
      return TicketDetails
    }
  },
  mounted () {
    this.getTickets()
  },
  methods: {
    formatDate,
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
