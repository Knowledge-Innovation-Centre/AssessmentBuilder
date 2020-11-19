<template>
  <div id="vue-frontend-app">
    <div id="div-for-export">
      <div :class="reportData.items.length ? 'max-width-for-print' : ''">
        <template v-if="formData.items.length" >
          <div class="aoat-text-center">Progress</div>
          <base-progress :percentage="percentage" class="aoat-mx-2 aoat-mb-5 aoat-h-5">
            <span class="aoat-text-lg aoat-text-white aoat-w-full aoat-flex aoat-justify-end aoat-pr-2">{{percentage}}%</span>
          </base-progress>
        </template>
        <generic v-if="formData.items.length" :form="formData" />
        <generic  v-if="reportData.items.length" :form="reportData" />
      </div>
    </div>
    <template v-if="exportEnabled">
      <div class="aoat-text-center aoat-mt-10">
        Loading PDF...please wait
      </div>
    </template>
    <template v-if="showLink && !exportEnabled">
      <div class="aoat-text-center aoat-mt-4">
        <a :href="listLink">List of completed assessments</a>
      </div>
    </template>
  </div>
</template>

<script>
import Api from "./Api";
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
      return Math.round((this.currentPage / this.getItems().length) * 100)
    },
    exportEnabled() {
      return this.$store.state.exportEnabled
    },
    user() {
      return this.$store.state.user
    },
    settings() {
      return this.$store.state.settings
    },
    showLink() {
      if (! this.user) {
        return false
      }
      if (! this.settings.aoat_page_for_assessments) {
        return false
      }
      return this.settings.aoat_show_link_button;

    },
    listLink() {
      return this.settings.aoat_page_for_assessments;
    },
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      if(aoat_config.aoatGetFormUrl) {
        Api.get(aoat_config.aoatGetFormUrl).then((result) => {
          this.form  = result.data;
          this.formData  = result.data.form_data[0];
          this.$store.dispatch('updateFormId', this.form.ID)
          this.$store.dispatch('updateFormSettings', this.form.form_settings[0])
        })
      }

      if(aoat_config.aoatGetUserUrl) {
        Api.get(aoat_config.aoatGetUserUrl).then((result) => {
          this.$store.dispatch('updateUser', result.data)
        })
      }

      if(aoat_config.aoatGetAssessmentUrl) {
        Api.get(aoat_config.aoatGetAssessmentUrl).then((result) => {
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
      Api.get(aoat_config.aoatGetSettingsUrl).then((result) => {
        let settings = {}
        for (let setting of result.data) {
          settings[setting.key] = setting.value
        }
        this.$store.dispatch('updateSettings', settings)
      })
    },
    getItems() {
      return this.formData.items.filter(item => {
        if (!item.conditions.length) {
          return true
        }
        for (let condition of item.conditions) {
          let field = condition.field;
          let question = condition.question;
          let selectedOptions = condition.selectedOptions;
          let assessment = this.$store.state.assessment;
          if (!assessment[field]) {
            return false
          }
          let assessmentValue = assessment[field];
          if (selectedOptions) {
            if (question) {
              assessmentValue = assessment[field][question]
            }
            if (!selectedOptions.map(selectedOption => selectedOption.id).includes(assessmentValue)) {
              return false
            }
          }

          if (!assessmentValue === condition.value) {
            return  false
          }
        }
        return true
      })
    },
  }
}
</script>

<style scoped>
.print-settings {
  width: 180mm;
  max-width: 180mm;
}
.max-width-for-print {

  width: 180mm;
  max-width: 180mm;
}
</style>
