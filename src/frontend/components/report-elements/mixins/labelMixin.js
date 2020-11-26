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
  },
  methods: {
    getScoreGraphColor(item) {
      if (item.scoreGraphColor && item.scoreGraphColor !== "") {
        return item.scoreGraphColor;
      }
      return "rgba(0, 0, 0, 0.1)";
    }
  }
};
