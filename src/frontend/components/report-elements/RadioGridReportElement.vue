<template>
  <div>
    <div class="aoat-font-bold">{{ object.label }}</div>
    <template v-if="showPieGraph">
      <pie-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
    </template>
    <template v-if="showRadarGraph">
      <radar-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
    </template>
    <template v-if="showGrid">
      <table class="aoat-w-full">
        <thead>
        <tr>
          <th></th>
          <th v-for="optionVertical in optionsVertical" :key="optionVertical.id">
            <span :style="'color: ' + optionVertical.color">{{ optionVertical.name }}</span>
            <span v-if="optionVertical.icon" class="dashicons" :class="optionVertical.icon"></span>
          </th>
        </tr>

        </thead>
        <tbody>
        <tr v-for="optionHorizontal in optionsHorizontal"  :key="optionHorizontal.id">
          <td>{{ optionHorizontal.name }}</td>
          <td v-for="optionVertical in optionsVertical" :key="optionVertical.id">
            <span v-if="value[optionHorizontal.id]" class="dashicons dashicons-yes"></span>
          </td>
        </tr>
        </tbody>
      </table>
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
        return !!this.object.selectedGraph.find(graph => graph.key === 'pie')
      },
      showRadarGraph() {
        return !!this.object.selectedGraph.find(graph => graph.key === 'radar')
      },
      showGrid() {
        return !!this.object.selectedGraph.find(graph => graph.key === 'grid')
      },
      optionsHorizontal() {
        return this.object.optionsHorizontal
      },
      optionsVertical() {
        return this.object.optionsVertical
      },
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
