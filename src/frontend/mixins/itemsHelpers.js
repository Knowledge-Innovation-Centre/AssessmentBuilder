export const itemsHelpers = {
  methods: {
    getItems(items) {
      return items.filter(item => {
        if (!item.conditions.length) {
          return true;
        }
        for (let condition of item.conditions) {
          let field = condition.field;
          let question = condition.question;
          let selectedOptions = condition.selectedOptions;
          let assessment = this.$store.state.assessment;
          if (!assessment[field]) {
            return false;
          }
          let assessmentValue = assessment[field];
          if (selectedOptions) {
            if (question) {
              assessmentValue = assessment[field][question];
            }
            if (
              !selectedOptions
                .map(selectedOption => selectedOption.id)
                .includes(assessmentValue)
            ) {
              return false;
            }
          }

          if (!assessmentValue === condition.value) {
            return false;
          }
        }
        return true;
      });
    }
  }
};
