export const actions = {
  updateFormId({ commit }, payload) {
    commit("UPDATE_FORM_ID", payload);
  },
  updateFormSettings({ commit }, payload) {
    commit("UPDATE_FORM_SETTINGS", payload);
  },
  updateAssessment({ commit }, payload) {
    commit("UPDATE_ASSESSMENT", payload);
  },
  updateAssessmentObject({ commit }, payload) {
    commit("UPDATE_ASSESSMENT_OBJECT", payload);
  },
  updateSelectedAssessmentForReview({ commit }, payload) {
    commit("UPDATE_SELECTED_ASSESSMENT_FOR_REVIEW", payload);
  },
  updateSelectedAssessmentForReviewId({ commit }, payload) {
    commit("UPDATE_SELECTED_ASSESSMENT_FOR_REVIEW_ID", payload);
  },
  updateReport({ commit }, payload) {
    commit("UPDATE_REPORT", payload);
  },
  clearAssessment({ commit }) {
    commit("CLEAR_ASSESSMENT");
  },
  updateValue({ commit }, payload) {
    commit("UPDATE_VALUE", payload);
  },
  updateCurrentPage({ commit }, payload) {
    commit("UPDATE_CURRENT_PAGE", payload);
  },
  updateDownloadPercentage({ commit }, payload) {
    commit("UPDATE_DOWNLOAD_PERCENTAGE", payload);
  },
  updateUser({ commit }, payload) {
    commit("UPDATE_USER", payload);
  },
  addError({ commit }, payload) {
    commit("ADD_ERROR", payload);
  },
  updateQueryParameterKey({ commit }, payload) {
    commit("UPDATE_QUERY_PARAMETER_KEY", payload);
  },
  clearErrors({ commit }) {
    commit("CLEAR_ERRORS");
  },
  enableExport({ commit }) {
    commit("ENABLE_EXPORT");
  },
  disableExport({ commit }) {
    commit("DISABLE_EXPORT");
  },
  updateSettings({ commit }, payload) {
    commit("UPDATE_SETTINGS", payload);
  }
};
