<template>
  <div class="aoat-flex aoat-flex-col">
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <select
      v-model="value"
      class="aoat-w-full"
      :class="hasError ? 'aoat-border-red-400' : ''"
      :style="getWidthStyle"
    >
      <option :value="null" disabled hidden>{{ object.placeholder }}</option>
      <option v-for="option in options" :key="option.id" :value="option.id">{{
        getOptionName(option)
      }}</option>
    </select>
    <small
      v-if="selectedAssessmentForReview"
      class="aoat-w-full aoat-text-sm aoat-block"
      >Initial data:
      <strong>{{ selectedAssessmentForReview[object.key] }}</strong>
    </small>
  </div>
</template>

<script>
export default {
  name: "CountryInput",

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
    options() {
      return this.object.options;
    },
    getWidthStyle() {
      if (this.object.maxWidth) {
        return (
          "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ";"
        );
      }
    }
  },
  methods: {
    getOptionName(option) {
      if (!this.object.labelParts) {
        return option.name;
      }
      let labels = [];
      for (let labelPart of this.object.labelParts) {
        labels.push(option[labelPart.key]);
      }
      return labels.join(" " + this.object.labelPartsSeperator + " ");
    }
  }
};
</script>
