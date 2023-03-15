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
import aggregationMixin from "./mixins/aggregationMixin";

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
      aggregatedAnswers: []
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
