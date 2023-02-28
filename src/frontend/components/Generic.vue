<template>
  <div :class="form.class" class="generic aoat-flex-1">
    <component :is="getComponent" :has-error="hasError" :object="form" />
    <small v-if="hasError" class="error aoat-text-red-400">
      This is required field
    </small>
    <slot />
  </div>
</template>

<script>
import Row from "./Row.vue";
import Column from "./Column.vue";
import Page from "./Page.vue";
import Paragraph from "./Paragraph.vue";
import TextInput from "./form-elements/TextInput.vue";
import HiddenInput from "./form-elements/HiddenInput.vue";
import DateInput from "./form-elements/DateInput.vue";
import TextareaInput from "./form-elements/TextareaInput.vue";
import SelectInput from "./form-elements/SelectInput.vue";
import LOCSelectInput from "./form-elements/LOCSelectInput.vue";
import CountryInput from "./form-elements/CountryInput.vue";
import RadioGridInput from "./form-elements/RadioGridInput.vue";
import RadioInput from "./form-elements/RadioInput.vue";
import AssessmentsInput from "./form-elements/AssessmentsInput.vue";
import ExternalElement from "./form-elements/ExternalElement.vue";
import Form from "./form-elements/Form.vue";
import Report from "./report-elements/Report.vue";
import SelectInputReport from "./report-elements/SelectInputReport.vue";
import LOCSelectInputReport from "./report-elements/LOCSelectInputReport.vue";
import CountryInputReport from "./report-elements/CountryInputReport.vue";
import RadioGridInputReport from "./report-elements/RadioGridInputReport.vue";
import RadioInputReport from "./report-elements/RadioInputReport.vue";
import TextInputReport from "./report-elements/TextInputReport.vue";
import HiddenInputReport from "./report-elements/HiddenInputReport.vue";
import TextareaInputReport from "./report-elements/TextareaInputReport.vue";
import DateInputReport from "./report-elements/DateInputReport.vue";
import FileUploadReport from "./report-elements/FileUploadReport.vue";
import PartScoreReport from "./report-elements/PartScoreReport.vue";
import TotalScoreReport from "./report-elements/TotalScoreReport.vue";
import CompareScoreReport from "./report-elements/CompareScoreReport.vue";
import LegendReport from "./report-elements/LegendReport.vue";
import LOCItemsReport from "./report-elements/LOCItemsReport.vue";
import AggregationReport from "./report-elements/AggregationReport.vue";
import FlatAggregationReport from "./report-elements/FlatAggregationReport.vue";
import FileUpload from "./form-elements/FileUpload.vue";

export default {
  name: "Generic",
  components: {
    Row,
    Column,
    Page,
    Paragraph,
    SelectInput,
    LOCSelectInput,
    CountryInput,
    TextareaInput,
    TextInput,
    HiddenInput,
    RadioInput,
    DateInput,
    RadioGridInput,
    AssessmentsInput,
    ExternalElement,
    Form,
    Report,
    SelectInputReport,
    LOCSelectInputReport,
    CountryInputReport,
    RadioGridInputReport,
    RadioInputReport,
    TextInputReport,
    HiddenInputReport,
    TextareaInputReport,
    DateInputReport,
    FileUploadReport,
    PartScoreReport,
    TotalScoreReport,
    CompareScoreReport,
    LegendReport,
    LOCItemsReport,
    AggregationReport,
    FlatAggregationReport,
    FileUpload
  },
  props: {
    form: {
      type: Object,
      required: true
    }
  },
  computed: {
    getComponent() {
      if (this.isReport) {
        return this.form.reportComponent;
      }
      return this.form.component;
    },
    assessment() {
      return this.$store.state.assessment;
    },
    hasError() {
      return this.$store.state.errors.includes(this.form.key);
    }
  },
  methods: {}
};
</script>

<style scoped>
.inner {
  width: 100%;
  height: 100%;
}
</style>
