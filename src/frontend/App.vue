<template>
  <div id="vue-frontend-app">
      <generic v-if="formData.items" :form="formData" />
      <generic  v-if="reportData.items" :form="reportData" />
  </div>
</template>

<script>
import axios from "axios";
import Generic from './components/Generic.vue';

export default {
  name: 'App',
  components: {
    Generic
  },
  data() {
    return {
      form: {},
      formData: {},
      report: {},
      reportData: {},
      assessment: {},
      assessmentData: {}
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      if(aoat_config.aoatGetFormUrl) {
        console.log('not12');
        axios.get(aoat_config.aoatGetFormUrl).then((result) => {
          this.form  = result.data;
          this.formData  = result.data.form_data[0];
          this.$store.dispatch('updateFormId', this.form.ID)
        })
      }

      if(aoat_config.aoatGetAssessmentUrl) {
        axios.get(aoat_config.aoatGetAssessmentUrl).then((result) => {
          this.assessment  = result.data;
          this.assessmentData  = this.assessment.assessment_data[0];
          this.report  = this.assessment.report;
          if(this.report) {
            this.reportData  = this.assessment.report.report_data[0];
            this.$store.dispatch('updateReport', this.reportData)
          }

          this.$store.dispatch('updateAssessment', this.assessmentData)
        })
      }
    }
  }
}
</script>

<style>

</style>
