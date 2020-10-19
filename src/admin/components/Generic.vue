<template>
    <fieldset class="generic aoat-flex-1 aoat-flex-wrap aoat-p-2 aoat-m-1" :class="{
      'border-orange': form.conditions && form.conditions.length,
      'border-red': form.hidden,
      'page':  form.type === 'page',
      'column':  form.type === 'column',
      'row':  form.type === 'row',
      'aoat-bg-white': ['page','form','report'].includes(form.type),
      'background-grey': !['page','form','report'].includes(form.type),
    }">
        <legend>{{ form.name }}</legend>
        <template v-if="isReport">
          <common-options-report :depth="depth" v-if="form.type !== 'report'" :object="form" />
        </template>
        <template v-else>
          <common-options :depth="depth" v-if="form.type !== 'form'" :object="form" />
        </template>
        <component :depth="depth" :is="form.component" :object="form" class="inner"></component>
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
  import CountryInput from "../form-elements/CountryInput.vue";
  import FileUpload from "../form-elements/FileUpload.vue";
  import Form from "../form-elements/Form.vue";
  import Report from "../report-elements/Report.vue";
  import DefaultReportElement from "../report-elements/DefaultReportElement.vue";
  import SelectReportElement from "../report-elements/SelectReportElement.vue";
  import CountryReportElement from "../report-elements/CountryReportElement.vue";
  import RadioGridReportElement from "../report-elements/RadioGridReportElement.vue";
  import RadioReportElement from "../report-elements/RadioReportElement.vue";
  import TextReportElement from "../report-elements/TextReportElement.vue";
  import TextareaReportElement from "../report-elements/TextareaReportElement.vue";
  import DateReportElement from "../report-elements/DateReportElement.vue";
  import FileUploadReportElement from "../report-elements/FileUploadReportElement.vue";
  import CommonOptions from "../form-elements/CommonOptions.vue";
  import CommonOptionsReport from "../report-elements/CommonOptionsReport.vue";

  export default {
    name: "Generic",
    props: {
      form: {
        type: Object,
        required: true
      },
      depth: {
        type: Number,
        required: true
      },
    },
    components: {
      Row,
      Column,
      Page,
      Paragraph,
      SelectInput,
      CountryInput,
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
      CountryReportElement,
      RadioGridReportElement,
      RadioReportElement,
      TextReportElement,
      TextareaReportElement,
      DateReportElement,
      FileUploadReportElement,
      CommonOptions,
      CommonOptionsReport,
    },
    computed: {
    },
    methods: {
    }
  };
</script>

<style scoped lang="scss">
    .generic {
        /*flex: 1;*/
        /*display: flex;*/
        /*align-items: stretch;*/
        /*margin: 10px;*/
        border: lightgray solid 1px;
        /*padding: 10px;*/
        position: relative;
        &.page, &.column {
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
      border: orange solid 1px;
    }

    .border-red {
      border: orangered solid 1px;
    }

    .background-grey {
      background: #eee;
    }
    .inner {
        width: 100%;
        height: 100%;
    }
</style>
