<template>
    <div class="generic aoat-flex-1" :class="form.class">
        <component :has-error="hasError" :is="getComponent" :object="form"></component>
        <small class="error aoat-text-red-400" v-if="hasError">
          This is required field
        </small>
        <slot/>
    </div>
</template>

<script>
  import Row from "./Row.vue";
  import Column from "./Column.vue";
  import Page from "./Page.vue";
  import Paragraph from "./Paragraph.vue";
  import TextInput from "./form-elements/TextInput.vue";
  import DateInput from "./form-elements/DateInput.vue";
  import TextareaInput from "./form-elements/TextareaInput.vue";
  import SelectInput from "./form-elements/SelectInput.vue";
  import CountryInput from "./form-elements/CountryInput.vue";
  import RadioGridInput from "./form-elements/RadioGridInput.vue";
  import RadioInput from "./form-elements/RadioInput.vue";
  import Form from "./form-elements/Form.vue";
  import Report from "./report-elements/Report.vue";
  import SelectInputReport from "./report-elements/SelectInputReport.vue";
  import CountryInputReport from "./report-elements/CountryInputReport.vue";
  import RadioGridInputReport from "./report-elements/RadioGridInputReport.vue";
  import RadioInputReport from "./report-elements/RadioInputReport.vue";
  import TextInputReport from "./report-elements/TextInputReport.vue";
  import TextareaInputReport from "./report-elements/TextareaInputReport.vue";
  import DateInputReport from "./report-elements/DateInputReport.vue";
  import FileUploadReport from "./report-elements/FileUploadReport.vue";
  import PartScoreReport from "./report-elements/PartScoreReport.vue";
  import TotalScoreReport from "./report-elements/TotalScoreReport.vue";
  import LegendReport from "./report-elements/LegendReport.vue";
  import FileUpload from "./form-elements/FileUpload.vue";

  export default {
    name: "Generic",
    props: {
      form: {
        type: Object,
        required: true
      }
    },
    components: {
      Row,
      Column,
      Page,
      Paragraph,
      SelectInput,
      CountryInput,
      TextareaInput,
      TextInput,
      RadioInput,
      DateInput,
      RadioGridInput,
      Form,
      Report,
      SelectInputReport,
      CountryInputReport,
      RadioGridInputReport,
      RadioInputReport,
      TextInputReport,
      TextareaInputReport,
      DateInputReport,
      FileUploadReport,
      PartScoreReport,
      TotalScoreReport,
      LegendReport,
      FileUpload,
    },
    computed: {
      getComponent() {
        if (this.isReport) {

        return this.form.reportComponent
        }
        return this.form.component
      },
      assessment() {
        return this.$store.state.assessment
      },
      hasError() {
        return this.$store.state.errors.includes(this.form.key)
      }
    },
    methods: {
    }
  };
</script>

<style scoped>
    .inner {
        width: 100%;
        height: 100%;
    }
</style>
