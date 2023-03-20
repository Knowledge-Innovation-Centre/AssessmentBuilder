import reverse from "lodash/reverse";

export default {
  data() {
    return {
      aggregatedAnswers: [],
      alreadyIncludedElementsForScores: [],
      colors: []
    };
  },
  methods: {
    aggregate(items, assessment = null, aggregatedAnswers = null) {
      if (!assessment) {
        assessment = this.$store.state.assessment;
      }

      for (let item of items) {
        if (!this.checkConditions(item)) {
          continue;
        }
        if (item.items) {
          this.aggregate(item.items, assessment, aggregatedAnswers);
          continue;
        }

        let value = assessment[item.reportItemKey];

        if (!item.options && !item.optionsVertical) {
          continue;
        }

        if (item.disableForScoring) {
          continue;
        }
        if (
          this.alreadyIncludedElementsForScores.includes(item.reportItemKey)
        ) {
          continue;
        }

        this.alreadyIncludedElementsForScores.push(item.reportItemKey);

        if (item.type === "radio_grid") {
          this.setRadioGridValue(item, value, aggregatedAnswers);
        } else if (item.options) {
          if (!value) {
            continue;
          }
          if (Array.isArray(value)) {
            if (!value.length) {
              continue;
            }

            for (const valueItem of value) {
              let verticalOption = item.options.find(
                optionVertical => optionVertical.id === valueItem
              );

              if (verticalOption) {
                if (aggregatedAnswers) {
                  aggregatedAnswers.push(
                    this.getAnswerObject(item, verticalOption)
                  );
                } else {
                  this.aggregatedAnswers.push(
                    this.getAnswerObject(item, verticalOption)
                  );
                }
                this.colors.push(verticalOption.color);
              }
            }
          } else {
            let verticalOption = item.options.find(
              optionVertical => optionVertical.id === value
            );

            if (verticalOption) {
              if (aggregatedAnswers) {
                aggregatedAnswers.push(
                  this.getAnswerObject(item, verticalOption)
                );
              } else {
                this.aggregatedAnswers.push(
                  this.getAnswerObject(item, verticalOption)
                );
              }
              this.colors.push(verticalOption.color);
            }
          }
        }
      }
    },
    compare(a, b) {
      let aString = a.name.toLowerCase();
      let bString = b.name.toLowerCase();

      if (isNaN(aString) && isNaN(bString)) {
        return this.sortAsc(aString, bString);
      } else if (isNaN(aString) && !isNaN(bString)) {
        return this.sortDesc(aString, bString);
      } else if (!isNaN(aString) && isNaN(bString)) {
        return this.sortDesc(aString, bString);
      } else {
        return this.sortAsc(aString, bString);
      }

      return 0;
    },
    sortAsc(aString, bString) {
      if (aString < bString) {
        if (this.orderAsc) {
          return 1;
        } else {
          return -1;
        }
      }
      if (aString > bString) {
        if (this.orderAsc) {
          return -1;
        } else {
          return 1;
        }
      }
      return 0;
    },
    sortDesc(aString, bString) {
      if (aString < bString) {
        if (this.orderAsc) {
          return -1;
        } else {
          return 1;
        }
      }
      if (aString > bString) {
        if (this.orderAsc) {
          return 1;
        } else {
          return -1;
        }
      }
      return 0;
    },
    sortAnswers() {
      if (!this.object.sortLegend) {
        return;
      }
      if (this.object.sortLegend == 0) {
        return;
      }
      this.aggregatedAnswers.sort(this.compare);
      if (this.object.sortLegend == 1) {
        return;
      }
      this.aggregatedAnswers = reverse(this.aggregatedAnswers);
    },
    getAnswerObject(item, verticalOption) {
      return {
        name: verticalOption.name,
        horizontal_name: item.optionsHorizontal
          ? item.optionsHorizontal[0].name
          : null,
        label:
          item.reportLabel && item.reportLabel !== ""
            ? item.reportLabel
            : item.label,
        color: verticalOption.color,
        graphLabel: this.getGraphLabel(item)
      };
    },
    getGraphLabel(item) {
      if (item.labelForGraphs && item.labelForGraphs !== "") {
        return item.labelForGraphs;
      }
      return null;
    },
    setRadioGridValue(item, value, aggregatedAnswers = null) {
      if (!value) {
        return 0;
      }

      for (let option of item.optionsHorizontal) {
        let verticalOption = item.optionsVertical.find(
          optionVertical => optionVertical.id === value[option.id]
        );
        if (verticalOption) {
          this.colors.push(verticalOption.color);
          if (aggregatedAnswers) {
            aggregatedAnswers.push(this.getAnswerObject(item, verticalOption));
          } else {
            this.aggregatedAnswers.push(
              this.getAnswerObject(item, verticalOption)
            );
          }

          return;
        }
      }

      return 0;
    }
  }
};
