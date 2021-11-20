import isEmpty from "lodash/isEmpty";

export default {
  computed: {
    isReport() {
      return !isEmpty(this.$store.state.report);
    }
  },
  methods: {
    getFormItemData(items, key) {
      for (const item of items) {
        if (key === item.key) {
          return item;
        }

        if (!item.items) {
          continue;
        }
        const found = this.getFormItemData(item.items, key);
        if (found) {
          return found;
        }
      }
    }
  }
};
