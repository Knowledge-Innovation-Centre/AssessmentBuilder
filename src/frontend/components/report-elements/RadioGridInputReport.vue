<template>
  <div>
    <div class="aoat-font-bold">{{ getLabel }}</div>
    <template v-for="graphType in object.selectedGraph">
      <div :key="graphType.key">

        <template v-if="graphType.key === 'pie'">
          <pie-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
        </template>
        <template v-if="graphType.key === 'radar'">
          <radar-chart v-if="chartData.datasets[0].data.length" :chart-data="chartData" :options="chartOptions"/>
        </template>
        <template v-if="graphType.key === 'grid'">
          <table class="aoat-w-full">
            <thead>
            <tr>
              <th></th>
              <th v-for="optionVertical in optionsVertical" :key="optionVertical.id">
                <span :style="'color: ' + optionVertical.color">{{ optionVertical.name }}</span>
                <span v-if="optionVertical.icon" class="dashicons" :class="optionVertical.icon"></span>
              </th>
              <th>Score</th>
            </tr>

            </thead>
            <tbody>
            <tr v-for="optionHorizontal in optionsHorizontal"  :key="optionHorizontal.id">
              <td>{{ optionHorizontal.name }}</td>
              <td v-for="optionVertical in optionsVertical" :key="optionVertical.id">
                <template v-if="$store.state.exportEnabled">
                  <template v-if="value[optionHorizontal.id] === optionVertical.id">
                <span class="checkmark">
                    <div class="checkmark_stem"></div>
                    <div class="checkmark_kick"></div>
                </span>

                  </template>
                </template>
                <template v-else>
                  <span v-if="value[optionHorizontal.id] === optionVertical.id" class="dashicons dashicons-yes"></span>
                </template>
              </td>
              <td>{{ optionsVertical.find(optionVertical => optionVertical.id === value[optionHorizontal.id]).score }}</td>
            </tr>
            </tbody>
          </table>
        </template>
      </div>

    </template>
  </div>
</template>

<script>

  import PieChart from "./PieChart";
  import RadarChart from "./RadarChart";
  import labelMixin from "./mixins/labelMixin";

  export default {

    name: 'RadioGridInputReport',

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
      optionsHorizontal() {
        return this.object.optionsHorizontal
      },
      optionsVertical() {
        return this.object.optionsVertical
      },
    },

    mixins: [
      labelMixin
    ],

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
          animation: {
            duration: 500,
          },
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
<style scoped>
.checkmark {
  display:inline-block;
  width: 22px;
  height:22px;
  -ms-transform: rotate(45deg); /* IE 9 */
  -webkit-transform: rotate(45deg); /* Chrome, Safari, Opera */
  transform: rotate(45deg);
}

.checkmark_stem {
  position: absolute;
  width:3px;
  height:9px;
  background-color:#ccc;
  left:11px;
  top:6px;
}

.checkmark_kick {
  position: absolute;
  width:3px;
  height:3px;
  background-color:#ccc;
  left:8px;
  top:12px;
}
</style>
