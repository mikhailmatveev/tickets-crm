<template>
  <details>
    <summary
      role="button"
      class="outline"
    >
      Фильтр
    </summary>
    <form @submit.prevent="submitFilters">
      <fieldset class="grid">
        <input
          v-model="filters.email"
          type="text"
          name="email"
          placeholder="i.ivanov@example.com"
          aria-label="email"
          autocomplete="email"
        >
        <input
          v-model="filters.phone"
          type="text"
          name="phone"
          placeholder="+79991234567"
          aria-label="phone"
          autocomplete="phone"
        >
        <input
          v-model="filters.date"
          type="date"
          name="date"
          placeholder=""
          aria-label="date"
          autocomplete="date"
        >
        <details class="dropdown">
          <summary>{{ statusToText(filters.status) || 'Выберите статус' }}</summary>
          <ul>
            <li
              v-for="(item, index) in statuses"
              :key="index"
            >
              <label>
                <input
                  v-model="filters.status"
                  type="radio"
                  name="status"
                  :value="item"
                >
                {{ statusToText(item) }}
              </label>
            </li>
          </ul>
        </details>
        <button
          type="button"
          class="secondary"
          @click="resetFilters"
        >
          Сбросить
        </button>
        <button type="submit">
          Применить
        </button>
      </fieldset>
    </form>
  </details>
</template>

<script>
import { statusToText } from '../../helpers/statusMapper'
import { mapGetters, mapMutations } from 'vuex'
import statusConstants from '../../constants/statuses'

export default {
  name: 'TicketFilter',
  computed: {
    ...mapGetters({
      getFilters: 'filters/getFilters'
    }),
    statuses () {
      return statusConstants
    },
    filters: {
      get () {
        return this.getFilters
      },
      set (value) {
        this.setFilters({ ...value })
      }
    }
  },
  methods: {
    ...mapMutations({
      setFilters: 'filters/setFilters'
    }),
    submitFilters () {
      this.$emit('submit-filters')
    },
    resetFilters () {
      this.setFilters({
        email: '',
        phone: '',
        date: '',
        status: ''
      })
      this.$emit('reset-filters')
    },
    statusToText
  }
}
</script>

<style scoped lang="scss">

</style>
