export default {
  namespaced: true,
  state: {
    filters: {
      email: '',
      phone: '',
      date: '',
      status: ''
    }
  },
  mutations: {
    setFilters (state, value) {
      state.filters = value
    }
  },
  getters: {
    getFilters (state) {
      return state.filters
    }
  }
}
