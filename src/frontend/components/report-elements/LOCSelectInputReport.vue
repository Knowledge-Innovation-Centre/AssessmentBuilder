<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <div v-html="reportValue" />
    <div v-if="dimensionsLabel">{{ dimensionsLabel }}</div>
    <div v-for="dimension in dimensions" :key="dimension.id">
      {{ dimension.post_title }}
    </div>
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
    dimensionsLabel() {
      return this.object.dimensionsLabel;
    },
    dimensions() {
      if (this.option) {
        return this.option.dimensions.map(dimension => dimension);
      }
      return [];
    },
    option() {
      const assessment = this.$store.state.assessment;

      let result = assessment[this.object.reportItemKey];
      if (!result) {
        return "/";
      }

      const option = this.object.options.find(option => option.id === result);
      if (!option) {
        return null;
      }
      return option;
    },
    reportValue() {
      if (!this.option) {
        return null;
      }
      if (this.object.showAnswer) {
        return this.option.name;
      }

      return "";
    }
  }
};
</script>
