import { createStore } from 'vuex'
import user from './modules/user'
import roles from './modules/roles'

const store = createStore({
  modules: {
    user,
    roles
  }
})

export default store
