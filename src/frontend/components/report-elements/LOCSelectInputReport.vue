<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ getReportValue(object) }}

    <div v-for="dimension in dimensions" :key="dimension.id">
      {{ dimension.post_title }}
    </div>
    <small
      v-if="selectedAssessmentForReview"
      class="aoat-w-full aoat-text-sm aoat-block"
      >Initial data:
      <strong>{{
        getReportValue(object, selectedAssessmentForReview)
      }}</strong></small
    >
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "LOCSelectInputReport",

  mixins: [labelMixin, itemsHelper],
  props: {
    object: {
      type: Object,
      required: true
    }
  },

  computed: {
    selectedAssessmentForReview() {
      return this.$store.state.selectedAssessmentForReview;
    },
    dimensions() {
      const option = this.object.options.find(
        option =>
          option.id === this.$store.state.assessment[this.object.reportItemKey]
      );
      if (option) {
        return option.dimensions.map(dimension => dimension);
      }
      return [];
    }
  }
};
</script>
