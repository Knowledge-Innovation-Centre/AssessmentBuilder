<template>
  <div class="aoat-flex aoat-flex-col">
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <date-picker
      v-model="value"
      :class="hasError ? 'aoat-border-red-400' : ''"
      :style="getWidthStyle"
      :placeholder="object.placeholder"
      value-type="format"
      format="YYYY-MM-DD"
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
import DatePicker from "vue2-datepicker";
import "vue2-datepicker/index.css";
import format from "date-fns/format";

export default {
  name: "TextInput",

  components: {
    DatePicker
  },

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

  computed: {
    selectedAssessmentForReview() {
      return this.$store.state.selectedAssessmentForReview;
    },
    getWidthStyle() {
      if (this.object.maxWidth) {
        return (
          "max-width:" + this.object.maxWidth + this.object.maxWidthUnit + ";"
        );
      }
      return "";
    },
    value: {
      get() {
        return this.$store.state.assessment[this.object.key];
      },
      set(newValue) {
        return this.$store.dispatch("updateValue", {
          key: this.object.key,
          value: newValue
        });
      }
    }
  },

  mounted() {
    this.setDefaultValue();
  },

  methods: {
    setDefaultValue() {
      if (!this.$store.state.assessment[this.object.key]) {
        if (this.object.defaultValueCurrentDate) {
          this.$store.dispatch("updateValue", {
            key: this.object.key,
            value: format(new Date(), "yyyy-MM-dd")
          });
        }
      }
    }
  }
};
</script>
