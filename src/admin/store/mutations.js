export const mutations = {
  UPDATE_FORM(state, payload) {
    state.form = payload;
  },
  UPDATE_FORM_SETTINGS(state, payload) {
    state.formSettings = payload;
  },
  REMOVE_FIELD(state, payload) {
    state.form.items = removeByKey(state.form.items, payload);
  },
  UPDATE_REPORT(state, payload) {
    state.report = payload;
  },
  UPDATE_SETTINGS(state, payload) {
    state.settings = payload;
  },
  REMOVE_FIELD_REPORT(state, payload) {
    state.report.items = removeByKey(state.report.items, payload);
  }
};

function removeByKey(items, key) {
  let newItems = [];
  for (let item of items) {
    if (item.key === key) {
      continue;
    }
    if (item.items) {
      item.items = removeByKey(item.items, key);
    }
    newItems.push(item);
  }
  return newItems;
}
