<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ getReportValue(object) }}
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
  name: "SelectInputReport",

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
    }
  }
};
</script>
