
export const mutations = {
  UPDATE_ASSESSMENT(state, payload) {
    state.assessment = payload
  },
  CLEAR_ASSESSMENT(state) {
    state.assessment = {}
  },
  UPDATE_VALUE(state, payload) {
    state.assessment[payload.key] = payload.value
  },
}
