import http from '../../services/http'

export default {
  namespaced: true,
  state: {
    user: null,
    fetching: false
  },
  mutations: {
    setUser (state, user) {
      state.user = user
    },
    setFetching (state, value) {
      state.fetching = value
    }
  },
  actions: {
    async fetchUser ({ commit }) {
      commit('setFetching', true)
      try {
        const response = await http.getUser()
        commit('setUser', response.data)
      } catch {
        commit('setUser', null)
      } finally {
        commit('setFetching', false)
      }
    }
  },
  getters: {
    getUserName (state, getters) {
      if (getters.isAuthenticated) {
        return state.user.name
      }
      return ''
    },
    getUserRole (state, getters) {
      if (getters.isAuthenticated) {
        return state.user.role
      }
      return ''
    },
    isAuthenticated (state) {
      return !!state.user
    }
  }
}
