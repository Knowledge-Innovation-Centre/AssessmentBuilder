<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <template v-for="graphType in object.selectedResultType">
      <div :key="graphType.key">
        <template v-if="graphType.key === 'pie'">
          <pie-chart v-if="chartData.datasets[0].data.length" :styles="myStyles" :chart-data="chartData" :options="chartOptions"/>
        </template>
        <template v-if="graphType.key === 'radar'">
          <radar-chart v-if="chartData.datasets[0].data.length" :styles="myStyles" :chart-data="chartData" :options="radarOptionsOptions"/>
        </template>
        <template v-if="graphType.key === 'bar'">
          <bar-chart v-if="chartData.datasets[0].data.length" :styles="myStyles" :chart-data="chartData" :options="barOptionsOptions"/>
        </template>
        <template v-if="graphType.key === 'horizontal_bar'">
          <horizontal-bar-chart v-if="chartData.datasets[0].data.length" :styles="myStyles" :chart-data="chartData" :options="horizontalBarOptionsOptions"/>
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
      getPage() {
        for (let page of this.reportData.items) {
          if(this.isInPage(page, page.items)) {
            return page;
          }
        }
      },
    },

    data () {
      return {

      };
    },

    mounted() {
      this.setData();
    },

    methods: {
      isInPage(page, items) {
        for (let item of items) {
          if (item.key === this.object.key) {
            return true
          }
          if (item.items) {
            if(this.isInPage(page, item.items)) {
              return true;
            }
          }
        }
        return false;
      },
      setData() {
        this.chartData.labels = [];
        this.calculateScore(this.getPage.items);
        if (this.score !== this.totalScore) {

          if (this.object.hideLabels) {
            this.chartData.labels.push('')
          } else {
            this.chartData.labels.push('Not compliant info')
          }
          this.graphArray.push(this.totalScore - this.score)
        }
        this.chartData.datasets[0].label = this.getLabel
        this.chartData.datasets[0].backgroundColor = this.colors
        this.chartData.datasets[0].data = this.graphArray;
        // this.barOptionsOptions.scales.yAxes[0].ticks.max = this.totalScore;
        // this.horizontalBarOptionsOptions.scales.xAxes[0].ticks.max = this.totalScore;
      },

      getHeightStyle() {
        if (this.object.height) {
          return "max-height:" + this.object.height + this.object.heightUnit + ';'
        }
      }
    }
  };
</script>
