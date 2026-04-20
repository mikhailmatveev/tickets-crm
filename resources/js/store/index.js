import { createStore } from 'vuex'
import user from './modules/user'
import roles from './modules/roles'
import filters from './modules/filters'

const store = createStore({
  modules: {
    user,
    roles,
    filters
  }
})

export default store
