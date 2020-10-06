<template>
  <div id="vue-frontend-app">
    <div class="aoat-text-center">Progress</div>
    <base-progress :percentage="percentage" class="aoat-mx-2 aoat-mb-5 aoat-h-5">
      <span class="aoat-text-lg aoat-text-white aoat-w-full aoat-flex aoat-justify-end aoat-pr-2">{{percentage}}%</span>
    </base-progress>
      <generic v-if="formData.items" :form="formData" />
      <generic  v-if="reportData.items" :form="reportData" />
  </div>
</template>

<script>
import axios from "axios";
import Generic from './components/Generic.vue';
import BaseProgress from './components/BaseProgress.vue';

export default {
  name: 'App',
  components: {
    Generic,
    BaseProgress
  },
  data() {
    return {
      form: {},
      formData: {
        items: []
      },
      report: {},
      reportData: {
        items: []
      },
      assessment: {},
      assessmentData: {}
    }
  },
  computed: {
    currentPage() {
      return this.$store.state.currentPage
    },
    percentage() {
      return (this.currentPage / this.formData.items.length) * 100
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      if(aoat_config.aoatGetFormUrl) {
        axios.get(aoat_config.aoatGetFormUrl).then((result) => {
          this.form  = result.data;
          this.formData  = result.data.form_data[0];
          this.$store.dispatch('updateFormId', this.form.ID)
          this.$store.dispatch('updateFormSettings', this.form.form_settings[0])
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
    },
  }
}
</script>

<style>

</style>
