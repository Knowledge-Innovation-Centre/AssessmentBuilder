
export const actions = {
  updateAssessment({ commit }, payload) {
    commit('UPDATE_ASSESSMENT', payload)
  },
  clearAssessment({ commit }) {
    commit('CLEAR_ASSESSMENT')
  },
  updateValue({ commit }, payload) {
    commit('UPDATE_VALUE', payload)
  },
}
