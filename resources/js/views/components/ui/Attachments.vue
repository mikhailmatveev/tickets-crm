<template>
  <details>
    <summary role="button">
      Прикреплённые файлы
    </summary>
    <table>
      <thead>
        <tr>
          <th>Файл</th>
          <th>Тип</th>
          <th>Размер, КБ</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="file in attachments"
          :key="file.id"
        >
          <td>
            <a
              :href="file.url"
              target="_blank"
            >
              {{ file.name }}
            </a>
          </td>
          <td>{{ file.mime_type }}</td>
          <td>{{ toKilobytes(file.size) }}</td>
        </tr>
      </tbody>
    </table>
  </details>
</template>

<script>
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Attachments',
  props: {
    attachments: {
      type: Array,
      default () {
        return []
      }
    }
  },
  methods: {
    toKilobytes (bytes) {
      return (Math.round(bytes / 1024) * 100) / 100;
    }
  }
}
</script>

<style scoped lang="scss">

</style>
