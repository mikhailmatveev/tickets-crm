import http from '../../services/http'

export default {
  namespaced: true,
  state: {
    roles: [],
    fetching: false
  },
  mutations: {
    setRoles (state, roles) {
      state.roles = roles
    },
    setFetching (state, value) {
      state.fetching = value
    }
  },
  actions: {
    async fetchRoles ({ commit }) {
      commit('setFetching', true)
      try {
        const response = await http.getRoles()
        commit('setRoles', response.data)
      } catch {
        commit('setRoles', [])
      } finally {
        commit('setFetching', false)
      }
    }
  },
  getters: {
    getRoles (state) {
      return state.roles
    }
  }
}
