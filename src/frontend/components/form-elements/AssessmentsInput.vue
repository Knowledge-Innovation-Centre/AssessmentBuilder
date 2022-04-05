<template>
  <div>
    <label>
      {{ object.label }} <template v-if="object.required">*</template>
    </label>
    <select
      v-model="value"
      class="aoat-w-full"
      :class="hasError ? 'aoat-border-red-400' : ''"
      :style="getWidthStyle"
      @change="getAssessmentInfo()"
    >
      <option :value="null" disabled hidden>{{ object.placeholder }}</option>
      <option v-for="option in options" :key="option.ID" :value="option.ID">{{
        option.post_title
      }}</option>
    </select>
  </div>
</template>

<script>
import Api from "../../Api";
import itemsHelper from "../../mixins/itemsHelpers";

export default {
  name: "AssessmentsInput",

  mixins: [itemsHelper],

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
    return {
      options: []
    };
  },

  computed: {
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
  mounted() {
    this.getOptions();
  },
  methods: {
    getOptions() {
      Api.get(
        aoat_config.aoatGetAssessmentsUrl +
          "?assessment_id=" +
          this.$store.state.formId
      ).then(result => {
        this.options = result.data
          .filter(dataItem => {
            return !dataItem.assessment_data[this.object.key];
          })
          .sort((a, b) => {
            const nameA = a.post_title
              .replace("R1 ", "")
              .replace("R2 ", "")
              .toLowerCase(); // ignore upper and lowercase
            const nameB = b.post_title
              .replace("R1 ", "")
              .replace("R2 ", "")
              .toLowerCase(); // ignore upper and lowercase
            if (nameA < nameB) {
              return -1;
            }
            if (nameA > nameB) {
              return 1;
            }

            // names must be equal
            return 0;
          });
      });
    },
    getAssessmentInfo() {
      Api.get(aoat_config.aoatGetAssessmentsUrl + this.value).then(result => {
        this.$store.dispatch(
          "updateSelectedAssessmentForReview",
          result.data.assessment_data
        );
        this.$store.dispatch(
          "updateSelectedAssessmentForReviewId",
          result.data.ID
        );
      });
    }
  }
};
</script>
