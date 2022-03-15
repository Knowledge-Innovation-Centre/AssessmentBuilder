<template>
  <div class="aoat-flex aoat-flex-col">
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <textarea
      v-model="value"
      class="aoat-w-full"
      :style="getWidthStyle"
      :class="hasError ? 'aoat-border-red-400' : ''"
      :placeholder="object.placeholder"
    />
    <small
      v-if="selectedAssessmentForReview"
      class="aoat-w-full aoat-text-sm aoat-block"
      >Initial data:
      <strong>{{ selectedAssessmentForReview[object.key] }}</strong></small
    >
  </div>
</template>

<script>
export default {
  name: "TextareaInput",

  components: {},
  props: {
    object: {
      type: Object,
      required: true
    },
    hasError: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data() {
    return {};
  },

  computed: {
    selectedAssessmentForReview() {
      return this.$store.state.selectedAssessmentForReview;
    },
    value: {
      get() {
        if (!this.$store.state.assessment[this.object.key]) {
          return this.object.defaultValue;
        }
        return this.$store.state.assessment[this.object.key];
      },
      set(newValue) {
        return this.$store.dispatch("updateValue", {
          key: this.object.key,
          value: newValue
        });
      }
    },
    getWidthStyle() {
      if (this.object.maxWidth) {
        return (
          "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ";"
        );
      }
      return "";
    }
  },
  watch: {
    selectedAssessmentForReview: {
      deep: true,
      handler() {
        this.setDataFromInitialData();
      }
    }
  },
  mounted() {
    this.setDataFromInitialData();
  },
  methods: {
    setDataFromInitialData() {
      if (!this.selectedAssessmentForReview) {
        return;
      }
      if (!this.selectedAssessmentForReview[this.object.key]) {
        return;
      }
      if (this.$store.state.assessment[this.object.key]) {
        return;
      }
      if (!this.object.copyTextFromInitialAssessment) {
        return;
      }

      this.$store.dispatch("updateValue", {
        key: this.object.key,
        value: this.selectedAssessmentForReview[this.object.key]
      });
    }
  }
};
</script>
