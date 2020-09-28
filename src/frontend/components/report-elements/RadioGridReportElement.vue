<template>
  <div>
      <h5>{{ object.label }}</h5>
    <pie-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
    <radar-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
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
          maintainAspectRatio: false
        }
      };
    },

    mounted() {
      this.loadChart()
    },

    methods: {
      loadChart() {
        this.chartData.labels = this.object.optionsHorizontal.map(optionHorizontal => optionHorizontal.name)
        this.chartData.datasets[0].backgroundColor = this.object.optionsHorizontal.map(optionHorizontal => optionHorizontal.color)
        let options = {}
        for (let option of this.object.optionsVertical) {
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
