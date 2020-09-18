
export const actions = {
  updateForm({ commit }, payload) {
    commit('UPDATE_FORM', payload)
  },
  removeField({ commit }, payload) {
    commit('REMOVE_FIELD', payload)
  },
}
