<template>
  <div class="aoat-flex aoat-flex-col">
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <select
      v-model="value"
      class="aoat-w-full"
      :style="getWidthStyle"
      :multiple="multiple"
      :class="hasError ? 'aoat-border-red-400' : ''"
    >
      <option :value="null" disabled hidden>{{ object.placeholder }}</option>
      <option v-for="option in options" :key="option.id" :value="option.id">{{
        option.name
      }}</option>
    </select>
    <template v-if="selectedAssessmentForReview">
      <small v-if="multiple" class="aoat-w-full aoat-text-sm aoat-block"
        >Initial data:
        <strong>{{
          getList(selectedAssessmentForReview[object.key])
        }}</strong></small
      >
      <small v-else class="aoat-w-full aoat-text-sm aoat-block"
        >Initial data:
        <strong>{{ selectedAssessmentForReview[object.key] }}</strong></small
      >
    </template>
  </div>
</template>

<script>
export default {
  name: "SelectInput",

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
        if (this.$store.state.assessment[this.object.key]) {
          return this.$store.state.assessment[this.object.key];
        }
        if (this.multiple) {
          return [];
        }
        return null;
      },
      set(newValue) {
        return this.$store.dispatch("updateValue", {
          key: this.object.key,
          value: newValue
        });
      }
    },
    multiple() {
      return this.object.multiple ?? false;
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
      return "";
    }
  },
  methods: {
    getList(items) {
      if (!items) {
        return "";
      }
      // const items = JSON.parse(items);
      return items.join(", ");
    }
  }
};
</script>
