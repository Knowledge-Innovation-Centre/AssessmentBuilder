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
                <td>{{ aggregatedValue.key }}</td>
                <td>{{ aggregatedValue.value }}</td>
              </tr>
            </tbody>
          </table>
        </template>
        <template v-if="object.enableLegend">
          <div v-for="label in labels" :key="label">
            <small>{{ label }}</small>
          </div>
        </template>
      </div>
    </template>
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import scoreMixin from "./mixins/scoreMixin";

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
      aggregatedAnswers: {},
      alreadyIncludedElementsForScores: []
    };
  },

  computed: {
    formData() {
      return this.$store.state.assessmentObject.form.form_data;
    },
    aggregatedValues() {
      let aggregatedValues = [];
      for (let key of Object.keys(this.aggregatedAnswers)) {
        aggregatedValues.push({
          key: key,
          value: this.aggregatedAnswers[key]
        });
      }
      return aggregatedValues;
    },
    labels() {
      let labels = [];
      for (let aggregatedAnswerKey of Object.keys(this.aggregatedAnswers)) {
        labels.push(aggregatedAnswerKey);
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
      this.aggregatedAnswers = {};
      this.aggregate(this.reportData.items);
      for (let aggregatedAnswerKey of Object.keys(this.aggregatedAnswers)) {
        if (this.object.enableLegend) {
          this.chartData.labels.push(this.parseLabel(aggregatedAnswerKey));
        } else {
          this.chartData.labels.push(aggregatedAnswerKey);
        }
        this.chartData.datasets[0].data.push(
          this.aggregatedAnswers[aggregatedAnswerKey]
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
          // this.chartData.labels.push(this.parseLabel(item.label));
          this.setRadioGridValue(item, value);
        } else if (item.options) {
          if (!value) {
            continue;
          }
          // this.chartData.labels.push(this.parseLabel(item.label));
          if (Array.isArray(value)) {
            if (!value.length) {
              continue;
            }

            this.setAggregateAnswersKeys(item.label);

            for (const valueItem of value) {
              let verticalOption = item.options.find(
                optionVertical => optionVertical.id === valueItem
              );

              if (verticalOption) {
                this.aggregatedAnswers[item.label] = verticalOption.name;
                this.colors.push(verticalOption.color);
              }
            }
          } else {
            let verticalOption = item.options.find(
              optionVertical => optionVertical.id === value
            );

            if (verticalOption) {
              this.aggregatedAnswers[item.label] = verticalOption.name;
              this.colors.push(verticalOption.color);
            }
          }
        }
      }
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
          this.setAggregateAnswersKeys(option.name);
          this.colors.push(verticalOption.color);
          return (this.aggregatedAnswers[option.name] = verticalOption.name);
        }
      }

      return 0;
    },

    setAggregateAnswersKeys(label) {
      if (typeof this.aggregatedAnswers[label] === "undefined") {
        this.aggregatedAnswers[label] = "";
      }
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
