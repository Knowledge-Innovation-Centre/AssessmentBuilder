<template>

    <fieldset class="generic aoat-flex-1 aoat-flex-wrap aoat-p-2 aoat-m-1" :class="{
      'border-orange': form.showIf && form.showIf.field,
      'border-red': form.hidden,
    }">

        <legend>{{ form.name }}</legend>

        <common-options v-if="form.type !== 'form'" :object="form" />

        <component :is="form.component" :object="form" class="inner"></component>
        <slot/>
    </fieldset>
</template>

<script>
  import Row from "./Row.vue";
  import Column from "./Column.vue";
  import Page from "./Page.vue";
  import Paragraph from "./Paragraph.vue";
  import TextInput from "../form-elements/TextInput.vue";
  import DateInput from "../form-elements/DateInput.vue";
  import TextareaInput from "../form-elements/TextareaInput.vue";
  import RadioInput from "../form-elements/RadioInput.vue";
  import RadioGridInput from "../form-elements/RadioGridInput.vue";
  import SelectInput from "../form-elements/SelectInput.vue";
  import FileUpload from "../form-elements/FileUpload.vue";
  import Form from "../form-elements/Form.vue";
  import Report from "../report-elements/Report.vue";
  import DefaultReportElement from "../report-elements/DefaultReportElement.vue";
  import SelectReportElement from "../report-elements/SelectReportElement.vue";
  import RadioGridReportElement from "../report-elements/RadioGridReportElement.vue";
  import RadioReportElement from "../report-elements/RadioReportElement.vue";
  import TextReportElement from "../report-elements/TextReportElement.vue";
  import TextAreaReportElement from "../report-elements/TextAreaReportElement.vue";
  import DateReportElement from "../report-elements/DateReportElement.vue";
  import FileUploadReportElement from "../report-elements/FileUploadReportElement.vue";
  import CommonOptions from "../form-elements/CommonOptions.vue";

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
      DateInput,
      TextareaInput,
      TextInput,
      RadioInput,
      RadioGridInput,
      FileUpload,
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
      CommonOptions,
    },
    methods: {
    }
  };
</script>

<style scoped>
    .generic {
        /*flex: 1;*/
        /*display: flex;*/
        /*align-items: stretch;*/
        /*margin: 10px;*/
        border: lightgray solid 1px;
        /*padding: 10px;*/
        position: relative;
    }

    .border-orange {
      border: orange solid 1px;
    }

    .border-red {
      border: orangered solid 1px;
    }

    .inner {
        width: 100%;
        height: 100%;
    }
</style>
