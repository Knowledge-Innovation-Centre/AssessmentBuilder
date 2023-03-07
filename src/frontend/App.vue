<template>
  <div id="vue-frontend-app">
    <div id="div-for-export">
      <div :class="reportData.items.length ? 'max-width-for-print' : ''">
        <template v-if="formData.items.length">
          <div class="aoat-text-center">Progress</div>
          <base-progress
            :colour="formSettings.progressBarColour"
            :percentage="percentage"
            class="aoat-mx-2 aoat-mb-5 aoat-h-5"
          >
            <span
              class="aoat-text-lg aoat-text-white aoat-w-full aoat-flex aoat-justify-end aoat-pr-2"
              >{{ percentage }}%</span
            >
          </base-progress>
        </template>
        <pages v-if="formData.items.length" :items="formData.items" />
        <pages v-if="reportData.items.length" :items="reportData.items" />
      </div>
    </div>
    <template v-if="exportEnabled">
      <base-progress
        v-if="exportEnabled"
        :percentage="downloadPercentage"
        class="aoat-mx-2 aoat-mb-5 aoat-h-5  aoat-mt-10"
        colour="#004F98"
      >
        <span
          class="aoat-text-lg aoat-text-white aoat-w-full aoat-flex aoat-justify-end aoat-pr-2"
          >{{ downloadPercentage }}%</span
        >
      </base-progress>
      <div class="aoat-text-center">
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
import Pages from "./components/Pages.vue";
import BaseProgress from "./components/BaseProgress.vue";
import itemsHelper from "./mixins/itemsHelpers.js";

export default {
  name: "App",
  components: {
    Pages,
    BaseProgress
  },
  mixins: [itemsHelper],
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
    };
  },
  computed: {
    currentPage() {
      return this.$store.state.currentPage;
    },
    downloadPercentage() {
      return this.$store.state.downloadPercentage;
    },
    percentage() {
      return Math.round(
        (this.currentPage / this.getItems(this.formData.items).length) * 100
      );
    },
    exportEnabled() {
      return this.$store.state.exportEnabled;
    },
    user() {
      return this.$store.state.user;
    },
    settings() {
      return this.$store.state.settings;
    },
    formSettings() {
      return this.$store.state.formSettings;
    },
    showLink() {
      if (!this.user) {
        return false;
      }

      if (this.formSettings.showAssessmentListLink) {
        return true;
      }
      if (!this.settings.aoat_page_for_assessments) {
        return false;
      }
      return this.settings.aoat_show_link_button;
    },
    listLink() {
      if (this.formSettings.pageAssessmentList) {
        return this.formSettings.pageAssessmentList.guid;
      }
      return this.settings.aoat_page_for_assessments;
    },
    isFilterForExport() {
      return aoat_config.isFilterForExport;
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      if (aoat_config.aoatGetFormUrl) {
        Api.get(aoat_config.aoatGetFormUrl).then(result => {
          this.form = result.data;
          this.formData = result.data.form_data;
          this.$store.dispatch("updateFormId", this.form.ID);
          this.$store
            .dispatch("updateFormSettings", this.form.form_settings)
            .then(() => {
              this.loadAdditionalFormsAssessments();
            });
        });
      }

      const url = new URL(window.location);
      let params = new URLSearchParams(url.search);

      if (params.get("edit_assessment")) {
        Api.get(
          aoat_config.aoatGetAssessmentsUrl + params.get("edit_assessment")
        ).then(result => {
          this.$store.dispatch("updateAssessment", result.data.assessment_data);
          this.$store.dispatch("updateAssessmentObject", result.data);
          this.$store.dispatch(
            "updateFormSettings",
            this.data.form.form_settings
          );
        });
      }

      if (aoat_config.aoatGetUserUrl) {
        Api.get(aoat_config.aoatGetUserUrl).then(result => {
          this.$store.dispatch("updateUser", result.data);
        });
      }

      if (aoat_config.aoatGetAssessmentUrl) {
        Api.get(aoat_config.aoatGetAssessmentUrl).then(result => {
          this.assessment = result.data;
          this.$store.dispatch("updateAssessmentObject", this.assessment);
          this.assessmentData = this.assessment.assessment_data;
          this.$store
            .dispatch("updateFormSettings", result.data.form.form_settings)
            .then(() => {
              this.loadAdditionalFormsAssessments();
            });
          this.report = this.assessment.report;
          if (this.report) {
            this.reportData = this.assessment.report.report_data;
            this.$store.dispatch("updateReport", this.reportData);
          }
          this.$store.dispatch("updateAssessment", this.assessmentData);

          const assessmentInputObject = this.findByKey(
            this.assessment.form.form_data.items,
            "AssessmentsInput",
            "component"
          );
          if (
            assessmentInputObject &&
            this.assessmentData[assessmentInputObject.key]
          ) {
            Api.get(
              aoat_config.aoatGetAssessmentsUrl +
                this.assessmentData[assessmentInputObject.key]
            ).then(result => {
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
        });
      }

      Api.get(aoat_config.aoatGetSettingsUrl).then(result => {
        let settings = {};
        for (let setting of result.data) {
          settings[setting.key] = setting.value;
        }
        this.$store.dispatch("updateSettings", settings);
      });
    },
    loadAdditionalFormsAssessments() {
      if (
        !this.isReport &&
        !this.isFilterForExport &&
        aoat_config.aoatGetLastAssessmentUrl &&
        this.formSettings.additionalForms &&
        this.formSettings.additionalForms.length
      ) {
        for (let additionalForm of this.formSettings.additionalForms) {
          Api.get(
            aoat_config.aoatGetLastAssessmentUrl +
              "?form_id=" +
              additionalForm.ID
          ).then(result => {
            const assessmentData = result.data.assessment_data;
            for (const assessmentDataKey of Object.keys(assessmentData)) {
              this.$store.dispatch("updateValue", {
                key: assessmentDataKey,
                value: assessmentData[assessmentDataKey]
              });
            }
          });
        }
      }
    }
  }
};
</script>

<style scoped></style>
