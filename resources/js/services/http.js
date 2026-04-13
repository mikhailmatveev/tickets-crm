import axios from 'axios'

class Http {
  constructor (config) {
    this.client = axios.create(config)
  }

  async getCookie () {
    return await this.client.get('/sanctum/csrf-cookie')
  }

  async getRoles () {
    return await this.client.get('/api/roles')
  }

  async getTickets () {
    return await this.client.get('/api/tickets')
  }

  async getStatistics (period) {
    return await this.client.get('/api/tickets/statistics', {
      params: {
        period
      }
    })
  }

  async getTicketDetails (id) {
    return await this.client.get(`/api/ticket/${id}`)
  }

  async getUser () {
    return await this.client.get('/api/user')
  }

  async getUsers () {
    return await this.client.get('/api/users')
  }

  async login (email, password) {
    return await this.client.post('/auth/login', {
      email,
      password
    })
  }

  async logout () {
    return await this.client.post('/auth/logout')
  }
}

export default new Http({
  responseType: 'json',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})
