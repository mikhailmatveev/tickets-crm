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
  <h3 v-if="details.replies && details.replies.length">
    Ответы менеджера
  </h3>
  <reply
    v-for="(item) in details.replies"
    :key="item.id"
    :created-at="item.created_at"
    :text="item.text"
  />
  <textarea
    v-if="details.status === 'working'"
    v-model="replyText"
    placeholder="Ответ по тикету"
    rows="5"
  />
  <div class="button-wrapper">
    <button
      v-if="details.status !== 'done'"
      role="button"
      :disabled="details.status === 'working' && replyText.length === 0"
      @click="updateTicket"
    >
      {{ getNextTicketStatusText() }}
    </button>
  </div>
  <modal
    :open="modal.isOpen"
    :header-text="modal.headerText"
    :body-text="modal.bodyText"
    @submit="closeModal"
  />
</template>

<script>
import http from '../services/http'
import Modal from './components/Modal.vue'
import Reply from './components/ui/Reply.vue'

export default {
  name: 'TicketDetails',
  components: {
    Modal,
    Reply
  },
  data () {
    return {
      details: {},
      replyText: '',
      modal: {
        isOpen: false,
        headerText: '',
        bodyText: ''
      }
    }
  },
  mounted () {
    this.getTicketDetails(this.$route.params.id)
  },
  methods: {
    closeModal () {
      this.modal.isOpen = false
    },
    showModalAtError (message) {
      this.modal.headerText = 'Ошибка'
      this.modal.bodyText = message
      this.modal.isOpen = true
    },
    getNextTicketStatus () {
      switch (this.details.status) {
        case 'new': return 'working'
        case 'working': return 'done'
        case 'done': return 'done'
      }
    },
    getNextTicketStatusText () {
      switch (this.details.status) {
        case 'new': return 'Начать работу'
        case 'working': return 'Отправить'
        case 'done': return 'Отправить'
      }
    },
    getRequestData() {
      if (this.details.status === 'done') {
        return {
          status: this.details.status,
          reply_text: this.replyText
        }
      }
      return {
        status: this.details.status
      }
    },
    async getTicketDetails (id) {
      try {
        const response = await http.getTicketDetails(id)
        this.details = response.data
      } catch (e) {
        this.showModalAtError(e.message)
      }
    },
    async updateTicket () {
      this.updateStatus()
      try {
        const response = await http.updateTicket(
          this.details.id,
          this.getRequestData()
        )
        // Обновляем данные компонента после обновления
        this.details = response.data
      } catch (e) {
        this.showModalAtError(e.message)
      }
    },
    updateStatus () {
      this.details.status = this.getNextTicketStatus()
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
