<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    {{ date }}

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
  name: "DateInputReport",

  components: {},

  mixins: [labelMixin, itemsHelper],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      date: ""
    };
  },

  computed: {
    selectedAssessmentForReview() {
      return this.$store.state.selectedAssessmentForReview;
    },
    value() {
      return this.$store.state.assessment[this.object.reportItemKey];
    }
  },

  mounted() {
    this.setDate();
  },

  methods: {
    setDate() {
      if (this.value) {
        let date = new Date(this.value);
        this.date = [
          date.getDate(),
          date.getMonth() + 1,
          date.getFullYear()
        ].join(".");
      }
    }
  }
};
</script>
