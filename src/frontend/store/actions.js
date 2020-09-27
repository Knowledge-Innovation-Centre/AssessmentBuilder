
export const actions = {
  updateFormId({ commit }, payload) {
    commit('UPDATE_FORM_ID', payload)
  },
  updateAssessment({ commit }, payload) {
    commit('UPDATE_ASSESSMENT', payload)
  },
  updateReport({ commit }, payload) {
    commit('UPDATE_REPORT', payload)
  },
  clearAssessment({ commit }) {
    commit('CLEAR_ASSESSMENT')
  },
  updateValue({ commit }, payload) {
    commit('UPDATE_VALUE', payload)
  },
}
