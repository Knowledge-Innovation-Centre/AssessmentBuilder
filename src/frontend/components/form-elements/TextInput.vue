<template>
  <div class="aoat-flex aoat-flex-col">
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <input
      v-model="value"
      class="aoat-w-full"
      :style="getWidthStyle"
      :class="hasError ? 'aoat-border-red-400' : ''"
      :placeholder="object.placeholder"
      type="text"
    />
    <small
      v-if="selectedAssessmentForReview"
      class="aoat-w-full aoat-text-sm aoat-block"
      >Initial data:
      <strong>
        <a
          v-if="object.isUrl"
          target="_blank"
          :href="selectedAssessmentForReview[object.key]"
          >{{ selectedAssessmentForReview[object.key] }}</a
        >
        <span v-else>{{
          selectedAssessmentForReview[object.key]
        }}</span></strong
      ></small
    >
  </div>
</template>

<script>
export default {
  name: "TextInput",

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
    user() {
      return this.$store.state.user;
    },
    multiple() {
      return this.object.multiple ?? false;
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
    user: {
      deep: true,
      handler() {
        this.setInitialData();
      }
    },
    selectedAssessmentForReview: {
      deep: true,
      handler() {
        this.setDataFromInitialData();
      }
    }
  },
  mounted() {
    if (this.user) {
      this.setInitialData();
    }
  },

  methods: {
    setInitialData() {
      if (this.object.type !== "first_last_name") {
        this.setDataFromInitialData();
        return;
      }
      if (!this.user) {
        return;
      }
      if (this.$store.state.assessment[this.object.key]) {
        return;
      }
      this.$store.dispatch("updateValue", {
        key: this.object.key,
        value: this.user.first_name + " " + this.user.last_name
      });
    },
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
