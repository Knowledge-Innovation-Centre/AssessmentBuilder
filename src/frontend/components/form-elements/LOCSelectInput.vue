<template>
  <div class="aoat-flex aoat-flex-col">
    {{ object.label }} <template v-if="object.required">*</template>
    <label
      v-for="option in options"
      :key="option.id"
      class="aoat-w-full aoat-block"
    >
      <input
        v-model="value"
        type="radio"
        :class="hasError ? 'aoat-border-red-400' : ''"
        :name="object.key"
        :value="option.id"
      />
      {{ option.name }}
    </label>
    <small
      v-if="selectedAssessmentForReview"
      class="aoat-w-full aoat-text-sm aoat-block"
      >Initial data:<strong>
        {{ selectedAssessmentForReview[object.key] }}</strong
      ></small
    >
  </div>
</template>

<script>
export default {
  name: "LOCSelectInput",

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
    }
  },
  methods: {}
};
</script>
