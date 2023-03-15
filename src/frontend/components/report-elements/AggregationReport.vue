<template>
  <div :id="'aggregation-' + object.reportItemKey">
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
                <th>Answers</th>
                <th>Count</th>
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
      </div>
    </template>
  </div>
</template>

<script>
import labelMixin from "./mixins/labelMixin";
import scoreMixin from "./mixins/scoreMixin";

export default {
  name: "AggregationReport",

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
        this.chartData.labels.push(aggregatedAnswerKey);
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
          this.setRadioGridValue(item, value);
        } else if (item.options) {
          if (!value) {
            continue;
          }
          if (Array.isArray(value)) {
            if (!value.length) {
              continue;
            }

            this.setAggregateAnswersKeys(item.options);
            this.setGraphColors(item.options);

            for (const valueItem of value) {
              let verticalOption = item.options.find(
                optionVertical => optionVertical.id === valueItem
              );

              if (verticalOption) {
                this.aggregatedAnswers[verticalOption.name]++;
              }
            }
          } else {
            let verticalOption = item.options.find(
              optionVertical => optionVertical.id === value
            );

            if (verticalOption) {
              this.aggregatedAnswers[verticalOption.name]++;
            }
          }
        }
      }
    },

    setRadioGridValue(item, value) {
      if (!value) {
        return 0;
      }

      this.setAggregateAnswersKeys(item.optionsVertical);
      this.setGraphColors(item.optionsVertical);

      for (let option of item.optionsHorizontal) {
        let verticalOption = item.optionsVertical.find(
          optionVertical => optionVertical.id === value[option.id]
        );
        if (verticalOption) {
          return this.aggregatedAnswers[verticalOption.name]++;
        }
      }

      return 0;
    },

    setAggregateAnswersKeys(options) {
      for (let option of options) {
        if (typeof this.aggregatedAnswers[option.name] === "undefined") {
          this.aggregatedAnswers[option.name] = 0;
        }
      }
    },
    setGraphColors(options) {
      for (let option of options) {
        if (option.color) {
          if (typeof this.colors[option.id] === "undefined") {
            this.colors[option.id] = option.color;
          }
        }
      }
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
