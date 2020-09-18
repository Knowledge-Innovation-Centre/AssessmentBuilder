
export const mutations = {
  UPDATE_FORM(state, payload) {
    state.form = payload
  },
  REMOVE_FIELD(state, payload) {
    state.form.items = removeByKey(state.form.items, payload);
  },
}

function removeByKey(items, key) {
  let newItems = [];
  for (let item of items) {
    if (item.key === key) {
      continue;
    }
    if (item.items) {
      item.items = removeByKey(item.items, key);
    }
    newItems.push(item)
  }
  return newItems
}
