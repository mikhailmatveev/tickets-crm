import axios from 'axios'

class Http {
  constructor (config) {
    this.client = axios.create(config)
  }

  async createUser (requestData) {
    return await this.client.post('/api/user', {
      ...requestData
    })
  }

  async deleteUser (id) {
    return await this.client.delete(`/api/user/${id}`)
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

  async updateUserRole (id, requestData) {
    return await this.client.put(`/api/user/${id}/role`, {
      ...requestData
    })
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

  async updateTicket (id, requestData) {
    return await this.client.put(`/api/ticket/${id}`, {
      ...requestData
    })
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
