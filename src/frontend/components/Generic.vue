<template>
    <div v-if="checkShow"  class="generic aoat-flex-1" :class="form.class">
        <component :is="getComponent" :object="form"></component>
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
  import RadioGridInput from "./form-elements/RadioGridInput.vue";
  import RadioInput from "./form-elements/RadioInput.vue";
  import Form from "./form-elements/Form.vue";
  import Report from "./report-elements/Report.vue";
  import DefaultReportElement from "./report-elements/DefaultReportElement.vue";
  import SelectReportElement from "./report-elements/SelectReportElement.vue";
  import RadioGridReportElement from "./report-elements/RadioGridReportElement.vue";
  import RadioReportElement from "./report-elements/RadioReportElement.vue";
  import TextReportElement from "./report-elements/TextReportElement.vue";
  import TextAreaReportElement from "./report-elements/TextAreaReportElement.vue";
  import DateReportElement from "./report-elements/DateReportElement.vue";
  import FileUploadReportElement from "./report-elements/FileUploadReportElement.vue";
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
      TextareaInput,
      TextInput,
      RadioInput,
      DateInput,
      RadioGridInput,
      Form,
      Report,
      DefaultReportElement,
      SelectReportElement,
      RadioGridReportElement,
      RadioReportElement,
      TextReportElement,
      TextAreaReportElement,
      DateReportElement,
      FileUploadReportElement,
      FileUpload,
    },
    computed: {
      getComponent() {
        return this.form.component
      },
      checkShow() {
        console.log('not');
        if (!this.form.showIf) {
          return true
        }
        if (!this.form.showIf.field) {
          return true
        }
        let field = this.form.showIf.field;
        let value = this.form.showIf.value;
        let assessment = this.$store.state.assessment;
        if (!assessment[field]) {
          return false
        }

        return assessment[field] === value
      }
    }
  };
</script>

<style scoped>
    .inner {
        width: 100%;
        height: 100%;
    }
</style>
