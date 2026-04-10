import axios from 'axios'

class Http {
  constructor (config) {
    this.client = axios.create(config)
  }

  async getCookie () {
    return await this.client.get('/sanctum/csrf-cookie')
  }

  async getUser () {
    return await this.client.get('/api/user')
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
