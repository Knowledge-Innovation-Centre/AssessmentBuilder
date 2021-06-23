export default {
  methods: {
    getItems(items) {
      return items.filter(item => {
        return this.checkConditions(item);
      });
    },

    checkConditions(item, assessment = null) {
      if (!item.conditions) {
        return true;
      }
      if (!item.conditions.length) {
        return true;
      }

      if (!assessment) {
        assessment = this.$store.state.assessment;
      }
      for (let condition of item.conditions) {
        let field = condition.field;
        let question = condition.question;
        let requiredConditions = condition.selectedOptions;
        if (!assessment[field]) {
          return false;
        }
        if (requiredConditions) {
          if (
            !this.checkSelect(assessment, requiredConditions, field, question)
          ) {
            return false;
          }
          continue;
        }

        let assessmentValue = assessment[field];
        if (assessmentValue !== condition.value) {
          return false;
        }
      }
      return true;
    },
    checkSelect(assessment, requiredConditions, field, question) {
      let assessmentValue = assessment[field];
      if (question) {
        assessmentValue = assessment[field][question];
      }

      if (typeof assessmentValue === "string") {
        assessmentValue = [assessmentValue];
      }

      let requiredConditionIds = requiredConditions.map(
        requiredCondition => requiredCondition.id
      );

      let any = false;
      for (let assessmentValueItem of assessmentValue) {
        if (requiredConditionIds.includes(assessmentValueItem)) {
          any = true;
        }
      }

      return any;
    },
    getReportValue(object) {
      let result = this.$store.state.assessment[object.reportItemKey];
      if (!result) {
        return "/";
      }

      if (!object.options) {
        return result;
      }

      let names = [];
      if (Array.isArray(result)) {
        for (const resultItem of result) {
          names.push(
            object.options.find(option => option.id === resultItem).name
          );
        }
        return names.join(", ");
      }
      return object.options.find(option => option.id === result).name;
    }
  }
};
