<template>
  <div :id="'total-score-' + object.reportItemKey">
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <template v-for="graphType in object.selectedResultType">
      <div :key="graphType.key">
        <template v-if="graphType.key === 'bar'">
          <bar-chart
            v-if="chartData.datasets[0].data.length"
            :chart-data="chartData"
            :options="barOptionsOptions"
            :styles="myStyles"
          />
        </template>
        <template v-if="graphType.key === 'horizontal_bar'">
          <horizontal-bar-chart
            v-if="chartData.datasets[0].data.length"
            :chart-data="chartData"
            :options="horizontalBarOptionsOptions"
            :styles="myStyles"
          />
        </template>
        <template v-if="graphType.key === 'score'">
          <table>
            <thead>
              <tr>
                <th>Question</th>
                <th>Answer</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="aggregatedValue in aggregatedValues"
                :key="aggregatedValue.key"
              >
                <td>{{ aggregatedValue.question }}</td>
                <td>{{ aggregatedValue.answer }}</td>
              </tr>
            </tbody>
          </table>
        </template>
      </div>
    </template>
    <template v-if="object.enableLegend">
      <div v-for="(label, index) in labels" :key="index">
        <small>{{ label }}</small>
      </div>
    </template>
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import scoreMixin from "./mixins/scoreMixin";
import reverse from "lodash/reverse";

export default {
  name: "FlatAggregationReport",

  mixins: [labelMixin, scoreMixin],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      aggregatedAnswers: [],
      alreadyIncludedElementsForScores: []
    };
  },

  computed: {
    formData() {
      return this.$store.state.assessmentObject.form.form_data;
    },
    aggregatedValues() {
      let aggregatedValues = [];
      for (let aggregatedAnswer of this.aggregatedAnswers) {
        aggregatedValues.push({
          answer: aggregatedAnswer.name,
          question: aggregatedAnswer.label
        });
      }
      return aggregatedValues;
    },
    labels() {
      let labels = [];
      for (let aggregatedAnswerKey of Object.keys(this.aggregatedAnswers)) {
        labels.push(
          this.aggregatedAnswers[aggregatedAnswerKey].graphLabel +
            ": " +
            this.aggregatedAnswers[aggregatedAnswerKey].label
        );
      }
      return labels;
    }
  },

  mounted() {
    this.setData();
  },

  methods: {
    setData() {
      this.chartData.labels = [];
      this.aggregatedAnswers = [];
      this.aggregate(this.reportData.items);
      this.sortAnswers();
      for (let aggregatedAnswerKey of Object.keys(this.aggregatedAnswers)) {
        if (this.object.enableLegend) {
          this.chartData.labels.push(
            this.aggregatedAnswers[aggregatedAnswerKey].graphLabel
          );
        } else {
          this.chartData.labels.push(
            this.aggregatedAnswers[aggregatedAnswerKey].graphLabel
          );
        }
        this.chartData.datasets[0].data.push(
          this.aggregatedAnswers[aggregatedAnswerKey].name
        );
      }
      this.chartData.datasets[0].backgroundColor = this.colors;
    },

    aggregate(items, assessment = null) {
      if (!assessment) {
        assessment = this.$store.state.assessment;
      }

      for (let item of items) {
        if (!this.checkConditions(item)) {
          continue;
        }
        if (item.items) {
          this.aggregate(item.items, assessment);
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
          this.setRadioGridValue(item, value);
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
                this.aggregatedAnswers.push(
                  this.getAnswerObject(item, verticalOption)
                );
                this.colors.push(verticalOption.color);
              }
            }
          } else {
            let verticalOption = item.options.find(
              optionVertical => optionVertical.id === value
            );

            if (verticalOption) {
              this.aggregatedAnswers.push(
                this.getAnswerObject(item, verticalOption)
              );
              this.colors.push(verticalOption.color);
            }
          }
        }
      }
    },
    compare(a, b) {
      if (a.graphLabel.toLowerCase() < b.graphLabel.toLowerCase()) {
        return -1;
      }
      if (a.graphLabel.toLowerCase() > b.graphLabel.toLowerCase()) {
        return 1;
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
        label: item.label,
        graphLabel: this.getGraphLabel(item)
      };
    },
    getGraphLabel(item) {
      if (item.labelForGraphs && item.labelForGraphs !== "") {
        return item.labelForGraphs;
      }
      return item.label;
    },
    setRadioGridValue(item, value) {
      if (!value) {
        return 0;
      }

      for (let option of item.optionsHorizontal) {
        let verticalOption = item.optionsVertical.find(
          optionVertical => optionVertical.id === value[option.id]
        );
        if (verticalOption) {
          this.colors.push(verticalOption.color);
          this.aggregatedAnswers.push(
            this.getAnswerObject(item, verticalOption)
          );

          return;
        }
      }

      return 0;
    },

    formatLabel(str, maxwidth = 50) {
      let sections = [];
      let words = str.split(" ");
      let temp = "";

      words.forEach(function(item, index) {
        if (temp.length > 0) {
          var concat = temp + " " + item;

          if (concat.length > maxwidth) {
            sections.push(temp);
            temp = "";
          } else {
            if (index == words.length - 1) {
              sections.push(concat);
              return;
            } else {
              temp = concat;
              return;
            }
          }
        }

        if (index == words.length - 1) {
          sections.push(item);
          return;
        }

        if (item.length < maxwidth) {
          temp = item;
        } else {
          sections.push(item);
        }
      });

      return sections;
    },

    parseLabel(label) {
      return label.split(" ")[0] ?? "";
    },

    getHeightStyle() {
      if (this.object.height) {
        return (
          "max-height:" + this.object.height + this.object.heightUnit + ";"
        );
      }
    }
  }
};
</script>
