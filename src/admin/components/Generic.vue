<template>
  <fieldset
    :class="{
      'border-orange': form.conditions && form.conditions.length,
      'border-red': form.hidden,
      page: form.type === 'page',
      column: form.type === 'column',
      row: form.type === 'row',
      'aoat-bg-white': ['page', 'form', 'report'].includes(form.type),
      'aoat-bg-blue-300': !['page', 'form', 'report'].includes(form.type)
    }"
    class="generic aoat-flex-1 aoat-flex-wrap aoat-p-2 aoat-m-1"
  >
    <legend>{{ form.name }}</legend>
    <template v-if="isReport">
      <common-options-report
        v-if="form.type !== 'report'"
        :depth="depth"
        :object="form"
      />
    </template>
    <template v-else>
      <common-options
        v-if="form.type !== 'form'"
        :depth="depth"
        :object="form"
      />
    </template>
    <component
      :is="component"
      v-show="!form.hideInForm"
      :depth="depth"
      :object="form"
      class="inner"
    />
    <slot />
  </fieldset>
</template>

<script>
import Row from "./Row.vue";
import Column from "./Column.vue";
import Page from "./Page.vue";
import Paragraph from "./Paragraph.vue";
import TextInput from "../form-elements/TextInput.vue";
import HiddenInput from "../form-elements/HiddenInput.vue";
import DateInput from "../form-elements/DateInput.vue";
import TextareaInput from "../form-elements/TextareaInput.vue";
import RadioInput from "../form-elements/RadioInput.vue";
import RadioGridInput from "../form-elements/RadioGridInput.vue";
import SelectInput from "../form-elements/SelectInput.vue";
import LOCSelectInput from "../form-elements/LOCSelectInput.vue";
import AssessmentsInput from "../form-elements/AssessmentsInput.vue";
import ExternalElement from "../form-elements/ExternalElement.vue";
import CountryInput from "../form-elements/CountryInput.vue";
import FileUpload from "../form-elements/FileUpload.vue";
import Form from "../form-elements/Form.vue";
import Report from "../report-elements/Report.vue";
import SelectInputReport from "../report-elements/SelectInputReport.vue";
import LOCSelectInputReport from "../report-elements/LOCSelectInputReport.vue";
import CountryInputReport from "../report-elements/CountryInputReport.vue";
import RadioGridInputReport from "../report-elements/RadioGridInputReport.vue";
import RadioInputReport from "../report-elements/RadioInputReport.vue";
import TextInputReport from "../report-elements/TextInputReport.vue";
import HiddenInputReport from "../report-elements/HiddenInputReport.vue";
import TextareaInputReport from "../report-elements/TextareaInputReport.vue";
import DateInputReport from "../report-elements/DateInputReport.vue";
import FileUploadReport from "../report-elements/FileUploadReport.vue";
import PartScoreReport from "../report-elements/PartScoreReport.vue";
import TotalScoreReport from "../report-elements/TotalScoreReport.vue";
import CompareScoreReport from "../report-elements/CompareScoreReport.vue";
import LegendReport from "../report-elements/LegendReport.vue";
import AggregationReport from "../report-elements/AggregationReport.vue";
import FlatAggregationReport from "../report-elements/FlatAggregationReport.vue";
import LOCItemsReport from "../report-elements/LOCItemsReport.vue";
import CommonOptions from "../form-elements/CommonOptions.vue";
import CommonOptionsReport from "../report-elements/CommonOptionsReport.vue";

export default {
  name: "Generic",
  components: {
    Row,
    Column,
    Page,
    Paragraph,
    SelectInput,
    LOCSelectInput,
    AssessmentsInput,
    ExternalElement,
    CountryInput,
    DateInput,
    TextareaInput,
    TextInput,
    HiddenInput,
    RadioInput,
    RadioGridInput,
    FileUpload,
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
    CommonOptions,
    CommonOptionsReport
  },
  props: {
    form: {
      type: Object,
      required: true
    },
    depth: {
      type: Number,
      required: true
    }
  },
  data() {
    return {};
  },
  computed: {
    component() {
      if (this.isReport) {
        return this.form.reportComponent;
      }
      return this.form.component;
    }
  },
  mounted() {},
  methods: {}
};
</script>

<style lang="scss" scoped>
.generic {
  /*flex: 1;*/
  /*display: flex;*/
  /*align-items: stretch;*/
  /*margin: 10px;*/
  border: lightgray solid 1px;
  /*padding: 10px;*/
  position: relative;

  &.page,
  &.column {
    border: lightgray dashed 2px;
  }

  &.page {
    margin-bottom: 20px;
  }

  &.row {
    margin-bottom: 20px;
  }
}

.border-orange {
  border-color: orange !important;
}

.border-red {
  border-color: orangered !important;
}

.background-grey {
  background: #eee;
}

.inner {
  width: 100%;
  height: 100%;
}
</style>
