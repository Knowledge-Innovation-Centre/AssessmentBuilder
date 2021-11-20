<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <a v-if="isUrl" target="_blank" :href="getReportValue(object)">{{
      getReportValue(object)
    }}</a>
    <span v-else>
      {{ getReportValue(object) }}
    </span>
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
  name: "TextInputReport",

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
    formData() {
      return this.$store.state.assessmentObject.form.form_data[0];
    },
    isUrl() {
      const formObject = this.getFormItemData(
        this.formData.items,
        this.object.reportItemKey
      );
      return !!formObject.isUrl;
    }
  }
};
</script>
