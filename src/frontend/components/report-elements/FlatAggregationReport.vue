<template>
  <div :id="'flat-aggregation-' + object.reportItemKey">
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
            :options="
              merge(horizontalBarOptionsOptions, {
                legend: {
                  display: false
                }
              })
            "
            :remove-legend="true"
            :styles="myStyles"
            class="aoat-mt-3"
          />
        </template>
        <template v-if="graphType.key === 'score'">
          <table>
            <thead>
              <tr>
                <th>Question ID</th>
                <th>Question</th>
                <th>Answer</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="aggregatedValue in aggregatedValues"
                :key="aggregatedValue.key"
              >
                <td>
                  <span v-if="object.enableLegend && aggregatedValue.graphLabel"
                    >{{ aggregatedValue.graphLabel }}
                  </span>
                </td>
                <td>
                  {{ aggregatedValue.question
                  }}<span v-if="aggregatedValue.horizontal_name"
                    >:<br />
                    {{
                      aggregatedValue.horizontal_name
                        ? aggregatedValue.horizontal_name
                        : ""
                    }}
                  </span>
                </td>
                <td>
                  <span :style="`color: ${aggregatedValue.color}`">
                    {{ aggregatedValue.answer }}
                  </span>
                </td>
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
import aggregationMixin from "./mixins/aggregationMixin";
import merge from "lodash/merge";

export default {
  name: "FlatAggregationReport",

  mixins: [labelMixin, scoreMixin, aggregationMixin],

  props: {
    object: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      aggregatedAnswers: [],
      orderAsc: true
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
          question: aggregatedAnswer.label.trim(),
          horizontal_name: aggregatedAnswer.horizontal_name,
          color: aggregatedAnswer.color,
          graphLabel: aggregatedAnswer.graphLabel
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
    merge,
    setData() {
      this.chartData.labels = [];
      this.aggregatedAnswers = [];
      this.aggregate(this.reportData.items);
      this.sortAnswers();
      for (let aggregatedAnswerKey of Object.keys(this.aggregatedAnswers)) {
        if (this.object.enableLegend) {
          this.chartData.labels.push(
            this.aggregatedAnswers[aggregatedAnswerKey].graphLabel ??
              this.aggregatedAnswers[aggregatedAnswerKey].label
          );
        } else {
          this.chartData.labels.push(
            this.aggregatedAnswers[aggregatedAnswerKey].graphLabel ??
              this.aggregatedAnswers[aggregatedAnswerKey].label
          );
        }
        this.chartData.datasets[0].data.push(
          this.aggregatedAnswers[aggregatedAnswerKey].name
        );
      }
      this.chartData.datasets[0].backgroundColor = this.colors;
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
