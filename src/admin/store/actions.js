export const actions = {
  updateForm({ commit }, payload) {
    commit("UPDATE_FORM", payload);
  },
  updateFormSettings({ commit }, payload) {
    commit("UPDATE_FORM_SETTINGS", payload);
  },
  updateReport({ commit }, payload) {
    commit("UPDATE_REPORT", payload);
  },
  updateSettings({ commit }, payload) {
    commit("UPDATE_SETTINGS", payload);
  },
  removeField({ commit }, payload) {
    commit("REMOVE_FIELD", payload);
  },
  removeFieldReport({ commit }, payload) {
    commit("REMOVE_FIELD_REPORT", payload);
  }
};
