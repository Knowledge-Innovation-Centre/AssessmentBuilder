export default {
  methods: {
    getItems(items) {
      return items.filter(item => {
        return this.checkConditions(item);
      });
    },

    checkConditions(item, assessment = null) {
      if (item.roleConditions && item.roleConditions.length) {
        if (!this.$store.state.user) {
          return false;
        }

        if (!this.$store.state.user.roles.includes(item.roleConditions)) {
          return false;
        }
      }

      if (item.queryParameterField) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const show = urlParams.get(item.queryParameterField);

        if (show !== "1") {
          return false;
        }
      }

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
      if (!assessmentValue) {
        return any;
      }

      for (let assessmentValueItem of assessmentValue) {
        if (requiredConditionIds.includes(assessmentValueItem)) {
          any = true;
        }
      }

      return any;
    },
    getReportValue(object, assessment = null) {
      if (!assessment) {
        assessment = this.$store.state.assessment;
      }
      let result = assessment[object.reportItemKey];
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
      const option = object.options.find(option => option.id === result);
      if (!option) {
        return null;
      }
      return option.name;
    },
    findByKey(items, value, key = "type") {
      for (const item of items) {
        if (item[key] === value) {
          return item;
        }
        if (!item.items) {
          continue;
        }
        const found = this.findByKey(item.items, value, key);
        if (found) {
          return found;
        }
      }
      return null;
    }
  }
};
