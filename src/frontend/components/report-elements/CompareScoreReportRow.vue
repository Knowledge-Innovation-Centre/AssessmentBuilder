<template>
  <tbody>
    <tr>
      <th :colspan="results[0].pages.length + 1">{{ title }}</th>
    </tr>
    <tr v-for="result of results" :key="result.id">
      <template v-if="bold || result.id === assessmentId">
        <th class="aoat-text-left" v-html="result.title" />
        <th
          v-for="(page, index) of result.pages"
          :key="index"
          class="aoat-text-center"
        >
          {{ page.score }}/{{ page.totalScore }}
        </th>
      </template>
      <template v-else>
        <td class="aoat-text-left"><strong v-html="result.title" /></td>
        <td
          v-for="(page, index) of result.pages"
          :key="index"
          class="aoat-text-center"
        >
          <template v-if="index === result.pages.length - 1">
            <strong> {{ page.score }}/{{ page.totalScore }}</strong>
          </template>
          <template v-else> {{ page.score }}/{{ page.totalScore }} </template>
        </td>
      </template>
    </tr>
  </tbody>
</template>

<script>
export default {
  name: "CompareScoreReportRow",

  props: {
    results: {
      type: Array,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    bold: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    assessmentObject() {
      return this.$store.state.assessmentObject;
    },
    assessmentId() {
      return this.assessmentObject.ID;
    }
  }
};
</script>
