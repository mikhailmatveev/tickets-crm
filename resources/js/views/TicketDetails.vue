<template>
  <h1>{{ details.subject }}</h1>
  <p>
    <mark>{{ details.status }}</mark>
  </p>
  <hr>
  <div v-if="details.customer">
    <article>E-mail: {{ details.customer.email }}</article>
    <article>Телефон: {{ details.customer.phone }}</article>
  </div>
  <h3>Описание проблемы</h3>
  <blockquote v-if="details.customer">
    {{ details.text }}
    <footer>
      <cite>{{ details.customer.name }}</cite>
    </footer>
  </blockquote>
  <h3 v-if="details.replies">
    Ответы менеджера
  </h3>
  <details
    v-for="(item) in details.replies"
    :key="item.id"
  >
    <summary role="button">
      {{ formatDate(item.updated_at) }}
    </summary>
    <blockquote>
      {{ item.text }}
    </blockquote>
  </details>
  <textarea
    v-show="false"
    v-model="replyText"
    placeholder="Ответ по тикету"
    rows="5"
  />
  <div class="button-wrapper">
    <button
      v-show="false"
      role="button"
      :disabled="replyText.length === 0"
    >
      {{ details.status === 'new' ? 'Начать работу' : 'Отправить' }}
    </button>
  </div>
</template>

<script>
import { formatDate } from '../mixins/helper'
import http from '../services/http'

export default {
  name: 'TicketDetails',
  data () {
    return {
      details: {},
      replyText: ''
    }
  },
  mounted () {
    this.getTicketDetails(this.$route.params.id)
  },
  methods: {
    formatDate,
    async getTicketDetails (id) {
      try {
        const response = await http.getTicketDetails(id)
        this.details = response.data
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>

<style scoped lang="scss">
textarea {
  margin-top: 50px;
}

.button-wrapper {
  text-align: right;
}
</style>
