<template>
  <div>
    <h5>{{ object.label }}</h5>
    <template v-if="showPieGraph">
      <pie-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
    </template>
    <template v-if="showRadarGraph">
      <radar-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
    </template>
  </div>
</template>

<script>

  import PieChart from "./PieChart";
  import RadarChart from "./RadarChart";

  export default {

    name: 'RadioGridReportElement',

    components: {
      PieChart,
      RadarChart
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {
      value() {
          return this.$store.state.assessment[this.object.reportItemKey] ?? {}
      },
      showPieGraph() {
        return this.object.selectedGraph === 'pie' || this.isBothSelected
      },
      showRadarGraph() {
        return this.object.selectedGraph === 'radar' || this.isBothSelected
      },
      isBothSelected() {
        return this.object.selectedGraph === 'both'
      }
    },

    data () {
      return {
        chartData: {
          labels: [],
          datasets: [
            {
              label: 'Data One',
              backgroundColor: [],
              data: []
            }
          ]
        },
        chartOptions: {
          responsive: true,
          maintainAspectRatio: false,
          backgroundColor: '#ffffff'
        }
      };
    },

    mounted() {
      this.loadChart()
    },

    methods: {
      loadChart() {
        this.chartData.labels = this.object.optionsVertical.map(optionVertical => optionVertical.name)
        this.chartData.datasets[0].backgroundColor = this.object.optionsVertical.map(optionVertical => optionVertical.color)
        let options = {}
        for (let option of this.object.optionsHorizontal) {
          options[option.id] = 0
        }
        Object.keys(this.value).forEach(key => {
          options[this.value[key]]++;
        })

        let preparedArray = []

        for (let option of Object.keys(options).map((key) => options[key])) {
          preparedArray.push(option)
        }
        this.chartData.datasets[0].data = preparedArray;
      }
    }
  };
</script>
