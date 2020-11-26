import isEmpty from "lodash/isEmpty";

export default {
  computed: {
    isReport() {
      return !isEmpty(this.$store.state.report);
    }
  }
};
