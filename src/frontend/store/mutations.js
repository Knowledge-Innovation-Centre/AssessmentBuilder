
export const mutations = {
  UPDATE_FORM_ID(state, payload) {
    state.formId = payload
  },
  UPDATE_ASSESSMENT(state, payload) {
    state.assessment = payload
  },
  UPDATE_REPORT(state, payload) {
    state.report = payload
  },
  CLEAR_ASSESSMENT(state) {
    state.assessment = {}
  },
  UPDATE_VALUE(state, payload) {
    state.assessment[payload.key] = payload.value
  },
}
