import Vue from "vue";

export const mutations = {
  UPDATE_FORM_ID(state, payload) {
    state.formId = payload;
  },
  UPDATE_FORM_SETTINGS(state, payload) {
    state.formSettings = payload;
  },
  UPDATE_ASSESSMENT_OBJECT(state, payload) {
    state.assessmentObject = payload;
  },
  UPDATE_SELECTED_ASSESSMENT_FOR_REVIEW(state, payload) {
    state.selectedAssessmentForReview = payload;
  },
  UPDATE_SELECTED_ASSESSMENT_FOR_REVIEW_ID(state, payload) {
    state.selectedAssessmentForReviewId = payload;
  },
  UPDATE_ASSESSMENT(state, payload) {
    state.assessment = payload;
  },
  UPDATE_REPORT(state, payload) {
    state.report = payload;
  },
  CLEAR_ASSESSMENT(state) {
    state.assessment = {};
  },
  UPDATE_VALUE(state, payload) {
    Vue.set(state.assessment, payload.key, payload.value);
  },
  UPDATE_CURRENT_PAGE(state, payload) {
    state.currentPage = payload;
  },
  UPDATE_DOWNLOAD_PERCENTAGE(state, payload) {
    state.downloadPercentage = payload;
  },
  UPDATE_USER(state, payload) {
    Vue.set(state, "user", payload);
  },
  ADD_ERROR(state, payload) {
    state.errors.push(payload);
  },
  CLEAR_ERRORS(state) {
    state.errors = [];
  },
  ENABLE_EXPORT(state) {
    state.exportEnabled = true;
  },
  UPDATE_QUERY_PARAMETER_KEY(state, payload) {
    state.queryParameterKey = payload;
  },
  DISABLE_EXPORT(state) {
    state.exportEnabled = false;
  },
  UPDATE_SETTINGS(state, payload) {
    state.settings = payload;
  }
};
