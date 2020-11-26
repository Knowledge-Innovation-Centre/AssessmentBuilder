export default {
  computed: {
    getLabel() {
      if (
        this.isReport &&
        this.object.reportLabel &&
        this.object.reportLabel !== ""
      ) {
        return this.object.reportLabel;
      }
      return this.object.label;
    }
  }
};
