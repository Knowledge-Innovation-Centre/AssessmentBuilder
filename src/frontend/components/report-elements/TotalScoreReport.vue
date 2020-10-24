<template>
  <div>
    <template v-for="graphType in object.selectedResultType">
      <div :key="graphType.key">
        <template v-if="graphType.key === 'pie'">
          <pie-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
        </template>
        <template v-if="graphType.key === 'radar'">
          <radar-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="radarOptionsOptions"/>
        </template>
        <template v-if="graphType.key === 'bar'">
          <bar-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="barOptionsOptions"/>
        </template>
        <template v-if="graphType.key === 'score'">
          {{ score }}/{{ totalScore }}
        </template>
      </div>

    </template>
  </div>
</template>

<script>

import labelMixin from "./mixins/labelMixin";
import scoreMixin from "./mixins/scoreMixin";

export default {
  name: 'PartScoreReport',

  mixins: [
    labelMixin,
    scoreMixin,
  ],

  props: {
    object: {
      type: Object,
      required: true,
    }
  },

  computed: {

  },

  data () {
    return {

    };
  },

  mounted() {
    this.setData();
  },

  methods: {
    setData() {
      this.chartData.labels = [];
      for(let item of this.reportData.items) {
        this.calculateScore(item.items);
      }
      if (this.score !== this.totalScore) {
        this.chartData.labels.push('Missing scores')
        this.graphArray.push(this.totalScore - this.score)
      }
      this.chartData.datasets[0].label = this.getLabel
      this.chartData.datasets[0].backgroundColor = this.colors
      this.chartData.datasets[0].data = this.graphArray;
    },
  }
};
</script>
